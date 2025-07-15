<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get available fields for validation.
     * Should be implemented by child classes.
     */
    abstract protected function getAvailableFields(): array;

    /**
     * Get the model instance.
     * Should be implemented by child classes.
     */
    abstract protected function model();

    /**
     * Get select columns from request input.
     */
    public function getSelectColumns(): array
    {
        $columns = $this->input('columns');
        if (is_array($columns) && count($columns) > 0) {
            // Filter invalid fields
            return array_values(array_intersect($columns, $this->getAvailableFields()));
        }
        // Return all if no columns specified
        return ['*'];
    }

    /**
     * Get order_by validation rule.
     */
    protected function getOrderByRule(): array
    {
        return ['string', 'in:' . implode(',', $this->getAvailableFields())];
    }

    /**
     * Get columns validation rule.
     */
    protected function getColumnsRule(): array
    {
        return ['array'];
    }

    /**
     * Get columns.* validation rule.
     */
    protected function getColumnsEachRule(): array
    {
        return ['string', 'in:' . implode(',', $this->getAvailableFields())];
    }

    /**
     * Get filters field validation rule.
     */
    protected function getFiltersFieldRule(): array
    {
        return ['string', 'in:' . implode(',', $this->getAvailableFields())];
    }

    /**
     * Get filters operator validation rule.
     */
    protected function getFiltersOperatorRule(): array
    {
        return ['string', 'in:=,!=,>,<,>=,<=,like,not like,in,not in'];
    }

    /**
     * Get filters value validation rule.
     */
    protected function getFiltersValueRule(): array
    {
        return ['required', 'string'];
    }

    /**
     * Get only specified validation rules.
     */
    protected function onlyRules(array $keys): array
    {
        $allRules = $this->getAllAvailableRules();
        return array_intersect_key($allRules, array_flip($keys));
    }

    /**
     * Get all rules except specified keys.
     */
    protected function exceptRules(array $keys): array
    {
        $allRules = $this->getAllAvailableRules();
        return array_diff_key($allRules, array_flip($keys));
    }

    /**
     * Get all available validation rules.
     */
    protected function getAllAvailableRules(): array
    {
        return [
            'per_page' => ['integer', 'min:1', 'max:100'],
            'page' => ['integer', 'min:1'],
            'order_by' => $this->getOrderByRule(),
            'order_direction' => ['string', 'in:asc,desc'],
            'columns' => $this->getColumnsRule(),
            'columns.*' => $this->getColumnsEachRule(),
            'with' => ['array'],
            'filters' => ['array'],
            'filters.*.0' => $this->getFiltersFieldRule(),
            'filters.*.1' => $this->getFiltersOperatorRule(),
            'filters.*.2' => $this->getFiltersValueRule(),
            'search' => ['string'],
        ];
    }

    /**
     * Get default index validation rules.
     */
    protected function getDefaultIndexRules(): array
    {
        return $this->onlyRules([
            'per_page',
            'page',
            'order_by',
            'order_direction',
            'columns',
            'columns.*',
            'with',
            'filters',
            'filters.*.0',
            'filters.*.1',
            'filters.*.2',
            'search'
        ]);
    }

    /**
     * Get default show validation rules.
     */
    protected function getDefaultShowRules(): array
    {
        return $this->onlyRules(['columns', 'columns.*', 'with']);
    }

    /**
     * Get pagination rules only.
     */
    protected function getPaginationRules(): array
    {
        return $this->onlyRules(['per_page', 'page']);
    }

    /**
     * Get sorting rules only.
     */
    protected function getSortingRules(): array
    {
        return $this->onlyRules(['order_by', 'order_direction']);
    }

    /**
     * Get selection rules only.
     */
    protected function getSelectionRules(): array
    {
        return $this->onlyRules(['columns', 'columns.*', 'with']);
    }

    /**
     * Get filtering rules only.
     */
    protected function getFilteringRules(): array
    {
        return $this->onlyRules(['filters', 'filters.*.0', 'filters.*.1', 'filters.*.2', 'search']);
    }

    /**
     * Get validation messages for all rules.
     */
    protected function getAllMessages(): array
    {
        return [
            'per_page.min' => __('laravel-scaffold::messages.pagination.per_page.min'),
            'per_page.max' => __('laravel-scaffold::messages.pagination.per_page.max'),
            'page.min' => __('laravel-scaffold::messages.pagination.page.min'),
            'order_direction.in' => __('laravel-scaffold::messages.sorting.order_direction.in'),
            'columns.array' => __('laravel-scaffold::messages.selection.columns.array'),
            'columns.*.in' => __('laravel-scaffold::messages.selection.columns_each.in'),
            'with.array' => __('laravel-scaffold::messages.selection.with.array'),
            'filters.array' => __('laravel-scaffold::messages.filtering.filters.array'),
            'filters.*.0.in' => __('laravel-scaffold::messages.filtering.filters_field.in'),
            'filters.*.1.in' => __('laravel-scaffold::messages.filtering.filters_operator.in'),
            'filters.*.2.required' => __('laravel-scaffold::messages.filtering.filters_value.required'),
            'search.string' => __('laravel-scaffold::messages.filtering.search.string'),
        ];
    }

    /**
     * Get validation attributes for all rules.
     */
    protected function getAllAttributes(): array
    {
        return [
            'per_page' => __('laravel-scaffold::attributes.pagination.per_page'),
            'page' => __('laravel-scaffold::attributes.pagination.page'),
            'order_by' => __('laravel-scaffold::attributes.sorting.order_by'),
            'order_direction' => __('laravel-scaffold::attributes.sorting.order_direction'),
            'columns' => __('laravel-scaffold::attributes.selection.columns'),
            'with' => __('laravel-scaffold::attributes.selection.with'),
            'filters' => __('laravel-scaffold::attributes.filtering.filters'),
            'search' => __('laravel-scaffold::attributes.filtering.search'),
        ];
    }

    /**
     * Get model fields dynamically.
     */
    protected function getModelFields(string $modelClass): array
    {
        if (!class_exists($modelClass)) {
            return [];
        }

        $model = new $modelClass();

        // Priority: use fillable fields
        if (property_exists($model, 'fillable') && !empty($model->getFillable())) {
            return $model->getFillable();
        }

        // Fallback: try to get from database schema
        if (method_exists($model, 'getConnection') && method_exists($model, 'getTable')) {
            try {
                $connection = $model->getConnection();
                $table = $model->getTable();
                $columns = $connection->getSchemaBuilder()->getColumnListing($table);
                return $columns ?: [];
            } catch (\Exception $e) {
                // If unable to get database schema, return empty array
                return [];
            }
        }

        return [];
    }
}
