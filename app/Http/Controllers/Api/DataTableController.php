<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DataTableController extends Controller
{
    /**
     * Process DataTable request with generic filtering and sorting
     */
    public function process(Request $request, array $data, array $columns): array
    {
        // Get request parameters
        $page = $request->input('page', 0);
        $first = $request->input('first', 0);
        $rows = $request->input('rows', 10);
        $sortField = $request->input('sortField');
        $sortOrder = $request->input('sortOrder', 1);
        $multiSortMeta = $request->input('multiSortMeta', []);
        $globalFilter = $request->input('globalFilter', '');
        $filters = $request->input('filters', []);

        // Apply column filters
        if (!empty($filters)) {
            $data = $this->applyColumnFilters($data, $filters);
        }

        // Apply global filter based on searchable columns
        if (!empty($globalFilter)) {
            $searchableColumns = $this->getSearchableColumns($columns);
            $data = $this->applyGlobalFilter($data, $globalFilter, $searchableColumns);
        }

        // Apply sorting
        if (!empty($multiSortMeta)) {
            // Multi-column sorting
            $data = $this->applyMultiSort($data, $multiSortMeta);
        } elseif ($sortField) {
            // Single column sorting
            $data = $this->applySort($data, $sortField, $sortOrder);
        }

        // Reset array keys after filtering
        $data = array_values($data);

        // Calculate pagination
        $totalRecords = count($data);
        $start = $first ?: ($page * $rows);
        $paginatedData = array_slice($data, $start, $rows);

        return [
            'data' => $paginatedData,
            'total' => $totalRecords
        ];
    }

    /**
     * Get searchable columns from column configuration
     */
    protected function getSearchableColumns(array $columns): array
    {
        $searchableColumns = [];

        foreach ($columns as $column) {
            // Skip if searchExclude is true
            if (isset($column['searchExclude']) && $column['searchExclude'] === true) {
                continue;
            }

            // Add field to searchable columns
            if (isset($column['field'])) {
                $searchableColumns[] = $column['field'];
            }
        }

        return $searchableColumns;
    }

    /**
     * Apply global filter to data
     */
    protected function applyGlobalFilter(array $data, string $globalFilter, array $searchableColumns): array
    {
        $searchStr = strtolower($globalFilter);

        return array_filter($data, function ($item) use ($searchStr, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $value = $item[$column] ?? '';

                // Convert value to string for searching
                if (is_array($value)) {
                    $value = implode(' ', $value);
                } elseif (is_object($value)) {
                    $value = json_encode($value);
                } else {
                    $value = (string) $value;
                }

                if (stripos(strtolower($value), $searchStr) !== false) {
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Apply column filters to data
     */
    protected function applyColumnFilters(array $data, array $filters): array
    {
        foreach ($filters as $field => $filterMeta) {
            if (isset($filterMeta['constraints'])) {
                foreach ($filterMeta['constraints'] as $constraint) {
                    if (!empty($constraint['value']) || $constraint['value'] === 0) {
                        $data = $this->applyConstraint($data, $field, $constraint);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Apply a single filter constraint
     */
    protected function applyConstraint(array $data, string $field, array $constraint): array
    {
        $filterValue = $constraint['value'];
        $matchMode = $constraint['matchMode'] ?? 'contains';

        return array_filter($data, function ($item) use ($field, $filterValue, $matchMode) {
            $value = $item[$field] ?? '';

            switch ($matchMode) {
                case 'contains':
                    return stripos((string)$value, (string)$filterValue) !== false;

                case 'notContains':
                    return stripos((string)$value, (string)$filterValue) === false;

                case 'equals':
                    return $value == $filterValue;

                case 'notEquals':
                    return $value != $filterValue;

                case 'in':
                    return in_array($value, (array)$filterValue);

                case 'lt':
                    return $value < $filterValue;

                case 'lte':
                    return $value <= $filterValue;

                case 'gt':
                    return $value > $filterValue;

                case 'gte':
                    return $value >= $filterValue;

                case 'between':
                    return $value >= $filterValue[0] && $value <= $filterValue[1];

                case 'startsWith':
                    return strpos(strtolower($value), strtolower($filterValue)) === 0;

                case 'endsWith':
                    return substr(strtolower($value), -strlen($filterValue)) === strtolower($filterValue);

                default:
                    return true;
            }
        });
    }

    /**
     * Apply single column sorting
     */
    protected function applySort(array $data, string $sortField, int $sortOrder): array
    {
        usort($data, function ($a, $b) use ($sortField, $sortOrder) {
            $aValue = $a[$sortField] ?? '';
            $bValue = $b[$sortField] ?? '';

            // Handle numeric comparison
            if (is_numeric($aValue) && is_numeric($bValue)) {
                $result = $aValue <=> $bValue;
            } else {
                // String comparison
                $result = strcasecmp((string)$aValue, (string)$bValue);
            }

            return $sortOrder === 1 ? $result : -$result;
        });

        return $data;
    }

    /**
     * Apply multi-column sorting
     */
    protected function applyMultiSort(array $data, array $multiSortMeta): array
    {
        usort($data, function ($a, $b) use ($multiSortMeta) {
            foreach ($multiSortMeta as $meta) {
                $field = $meta['field'];
                $order = $meta['order'] ?? 1;

                $aValue = $a[$field] ?? '';
                $bValue = $b[$field] ?? '';

                // Handle numeric comparison
                if (is_numeric($aValue) && is_numeric($bValue)) {
                    $result = $aValue <=> $bValue;
                } else {
                    // String comparison
                    $result = strcasecmp((string)$aValue, (string)$bValue);
                }

                if ($result !== 0) {
                    return $order === 1 ? $result : -$result;
                }
            }

            return 0;
        });

        return $data;
    }
}
