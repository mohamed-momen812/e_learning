<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales
     */
    protected function getSupportedLocales(): array
    {
        return config('app.supported_locales', ['en', 'ar']);
    }

    /**
     * Default locale
     */
    protected function getDefaultLocale(): string
    {
        return config('app.locale', 'ar');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->detectLocale($request);

        app()->setLocale($locale);

        // Set locale for Carbon (date formatting)
        if (class_exists(\Carbon\Carbon::class)) {
            \Carbon\Carbon::setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Detect locale from Accept-Language header or request parameter
     */
    protected function detectLocale(Request $request): string
    {
        // Priority 1: Check if locale is explicitly set in request (e.g., ?locale=ar)
        if ($request->has('locale')) {
            $locale = $request->get('locale');
            if ($this->isSupported($locale)) {
                return $locale;
            }
        }

        // Priority 2: Check Accept-Language header
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $locale = $this->parseAcceptLanguage($acceptLanguage);
            if ($locale) {
                return $locale;
            }
        }

        // Priority 3: Use default locale
        return $this->getDefaultLocale();
    }

    /**
     * Parse Accept-Language header and return supported locale
     */
    protected function parseAcceptLanguage(string $acceptLanguage): ?string
    {
        // Parse Accept-Language header (e.g., "ar,en;q=0.9,fr;q=0.8")
        $languages = [];

        // Split by comma
        $parts = explode(',', $acceptLanguage);

        foreach ($parts as $part) {
            // Remove quality value if present (e.g., "en;q=0.9" -> "en")
            $lang = trim(explode(';', $part)[0]);

            // Extract language code (e.g., "ar-SA" -> "ar")
            $langCode = strtolower(explode('-', $lang)[0]);

            // Extract quality value (default to 1.0 if not specified)
            $quality = 1.0;
            if (preg_match('/q=([\d.]+)/', $part, $matches)) {
                $quality = (float) $matches[1];
            }

            $languages[$langCode] = $quality;
        }

        // Sort by quality (descending)
        arsort($languages);

        // Return first supported locale
        foreach (array_keys($languages) as $langCode) {
            if ($this->isSupported($langCode)) {
                return $langCode;
            }
        }

        return null;
    }

    /**
     * Check if locale is supported
     */
    protected function isSupported(string $locale): bool
    {
        return in_array(strtolower($locale), $this->getSupportedLocales(), true);
    }
}
