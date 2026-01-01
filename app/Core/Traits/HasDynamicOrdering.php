<?php

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasDynamicOrdering
{
    /**
     * Apply dynamic ordering to query
     * 
     * @param Builder $query
     * @param string|array $sort - Can be a string like "name" or "-name" or array like ["name", "-email"]
     * @param array $allowedFields - Whitelist of allowed sortable fields (for security)
     * @param string $defaultSort - Default sort field if none provided
     * @return Builder
     */
    protected function applyOrdering(
        Builder $query,
        string|array $sort = null,
        array $allowedFields = [],
        string $defaultSort = 'created_at'
    ): Builder {
        // If no sort provided, use default
        if (empty($sort)) {
            $sort = $defaultSort;
        }

        // Convert string to array for consistent handling
        if (is_string($sort)) {
            $sortFields = array_filter(array_map('trim', explode(',', $sort)));
        } else {
            $sortFields = $sort;
        }

        // If no sort fields, use default
        if (empty($sortFields)) {
            $sortFields = [$defaultSort];
        }

        // Apply each sort field
        foreach ($sortFields as $sortField) {
            $field = ltrim($sortField, '-');
            $direction = str_starts_with($sortField, '-') ? 'desc' : 'asc';

            // Security: Only allow sorting by whitelisted fields if provided
            if (!empty($allowedFields) && !in_array($field, $allowedFields)) {
                continue; // Skip invalid fields
            }

            // Check if field exists in table (basic validation)
            if ($this->isValidSortField($query, $field)) {
                $query->orderBy($field, $direction);
            }
        }

        return $query;
    }

    /**
     * Check if a field is valid for sorting
     * 
     * @param Builder $query
     * @param string $field
     * @return bool
     */
    protected function isValidSortField(Builder $query, string $field): bool
    {
        // Basic validation: field should be alphanumeric with underscores
        // This prevents SQL injection while allowing valid column names
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $field) === 1;
    }

    /**
     * Parse sort parameter from request
     * Supports formats:
     * - "name" (single field, ascending)
     * - "-name" (single field, descending)
     * - "name,email" (multiple fields, both ascending)
     * - "name,-email" (multiple fields, name ascending, email descending)
     * - ["name", "-email"] (array format)
     * 
     * @param string|array|null $sort
     * @return array
     */
    protected function parseSortParameter(string|array|null $sort): array
    {
        if (empty($sort)) {
            return [];
        }

        if (is_array($sort)) {
            return $sort;
        }

        return array_filter(array_map('trim', explode(',', $sort)));
    }
}
