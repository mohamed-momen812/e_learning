<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stancl\Tenancy\Exceptions\NotASubdomainException;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;

class InitializeTenancyByHeader extends InitializeTenancyBySubdomain
{
    /**
     * The header name to read the tenant subdomain from.
     *
     * @var string
     */
    public static $headerName = 'X-Tenant';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Override to pass request instead of hostname
        $subdomain = $this->makeSubdomain($request);

        if (is_object($subdomain) && $subdomain instanceof Exception) {
            $onFail = static::$onFail ?? function ($e) {
                throw $e;
            };

            return $onFail($subdomain, $request, $next);
        }

        // If a Response instance was returned, we return it immediately.
        if (is_object($subdomain) && $subdomain instanceof Response) {
            return $subdomain;
        }

        return $this->initializeTenancy(
            $request,
            $next,
            $subdomain
        );
    }

    /**
     * Override makeSubdomain to get subdomain from header instead of hostname.
     * 
     * @param  \Illuminate\Http\Request|string  $requestOrHostname
     * @return string|Response|Exception|mixed
     */
    protected function makeSubdomain($requestOrHostname)
    {
        // If it's a Request object, get subdomain from header
        if ($requestOrHostname instanceof Request) {
            return $this->getSubdomainFromHeader($requestOrHostname);
        }

        // Fallback to parent behavior if string (hostname) is passed
        return parent::makeSubdomain($requestOrHostname);
    }

    /**
     * Get the subdomain from the request header.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|Exception|Response
     */
    protected function getSubdomainFromHeader(Request $request)
    {
        $subdomain = $request->header(static::$headerName);

        // If header is missing, return exception
        if (!$subdomain) {
            return new NotASubdomainException('Header ' . static::$headerName . ' is required');
        }

        // Clean the subdomain (remove any whitespace, convert to lowercase)
        $subdomain = strtolower(trim($subdomain));

        // Validate subdomain format (alphanumeric and hyphens only)
        if (!preg_match('/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?$/', $subdomain)) {
            return new NotASubdomainException('Invalid subdomain format in header');
        }

        return $subdomain;
    }
}
