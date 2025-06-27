<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold;

use JoeSu\LaravelScaffold\Exceptions\RepositoryException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use ReflectionClass;
use function __;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function newQuery()
    {
        return $this->model->newQuery();
    }

    public function index(
        int $perPage = 0,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        array $relationships = [],
        array $columns = ['*'],
        array $filters = []
    ) {
        $query = $this->model->select($columns);

        if (!empty($relationships)) {
            $query = $this->loadRelationships($query, $relationships);
        }

        if (!empty($filters)) {
            $this->applyFilters($query, $filters);
        }

        if ($orderBy !== null) {
            $this->applySorting($query, $orderBy, $orderDirection ?? 'asc');
        }

        return $perPage > 0 ? $query->paginate($perPage) : $query->get();
    }

    public function find($id, array $columns = ['*'], array $relationships = [])
    {
        try {
            $query = $this->model->select($columns);

            if (!empty($relationships)) {
                $query = $this->loadRelationships($query, $relationships);
            }

            $result = $query->findOrFail($id);

            if (!$result) {
                throw new RepositoryException(__('laravel-scaffold::messages.record_not_found', ['id' => $id]), 404);
            }

            return $result;
        } catch (RepositoryException $e) {
            throw $e;
        } catch (ModelNotFoundException $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.record_not_found', ['id' => $id]), 404);
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.find_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function chunk(int $count, callable $callback, array $conditions = [])
    {
        $query = $this->model->newQuery();
        if (!empty($conditions)) {
            $this->applyFilters($query, $conditions);
        }
        return $query->chunk($count, $callback);
    }

    public function cursor(array $conditions = [])
    {
        $query = $this->model->newQuery();
        if (!empty($conditions)) {
            $this->applyFilters($query, $conditions);
        }
        return $query->cursor();
    }

    protected function loadRelationships($query, array $relationships)
    {
        $withRelations = [];
        $countRelations = [];

        foreach ($relationships as $relationship) {
            if (Str::endsWith($relationship, '.count')) {
                $countRelations[] = Str::before($relationship, '.count');
            } else {
                $withRelations[] = $relationship;
            }
        }

        if (!empty($withRelations)) {
            $query->with($withRelations);
        }

        if (!empty($countRelations)) {
            $query->withCount($countRelations);
        }

        return $query;
    }

    protected function applyFilters($query, array $filters)
    {
        foreach ($filters as $filter) {
            $this->applySingleFilter($query, $filter);
        }
    }

    protected function applySingleFilter($query, $filter)
    {
        if (is_string($filter)) {
            $filter = json_decode($filter, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RepositoryException(__('laravel-scaffold::messages.invalid_filter_format'), 400);
            }
        }

        if (!is_array($filter)) {
            throw new RepositoryException(__('laravel-scaffold::messages.filter_must_be_array'), 400);
        }

        $filterCount = count($filter);

        if ($filterCount === 2) {
            [$field, $value] = $filter;
            $this->applyFieldFilter($query, $field, $value);
        } elseif ($filterCount === 3) {
            [$field, $operator, $value] = $filter;
            $this->applyFieldFilter($query, $field, $value, $operator);
        } else {
            throw new RepositoryException(__('laravel-scaffold::messages.invalid_filter_format'), 400);
        }
    }

    protected function applyFieldFilter($query, string $field, $value, string $operator = '=')
    {
        if (strpos($field, '.') !== false) {
            $this->applyRelationFilter($query, $field, $value, $operator);
        } else {
            $query->where($field, $operator, $value);
        }
    }

    protected function applyRelationFilter($query, string $field, $value, string $operator = '=')
    {
        $parts = explode('.', $field);
        if (count($parts) !== 2) {
            throw new RepositoryException(__('laravel-scaffold::messages.invalid_relation_field_format'), 400);
        }

        [$relation, $column] = $parts;

        $query->whereHas($relation, function ($q) use ($column, $value, $operator) {
            $q->where($column, $operator, $value);
        });
    }

    protected function applySorting($query, string $orderBy, string $orderDirection)
    {
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            throw new RepositoryException(__('laravel-scaffold::messages.invalid_order_direction'), 400);
        }

        $allowedColumns = $this->getAllowedSortColumns();
        if (!empty($allowedColumns) && !in_array($orderBy, $allowedColumns)) {
            throw new RepositoryException(__('laravel-scaffold::messages.invalid_sort_column', ['column' => $orderBy]), 400);
        }

        $query->orderBy($orderBy, $orderDirection);
    }

    protected function getAllowedSortColumns(): array
    {
        return [];
    }

    public function create(array $attributes)
    {
        try {
            return $this->model->create($attributes);
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.create_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function update($id, array $attributes)
    {
        try {
            $model = $this->find($id);
            $model->update($attributes);
            return $model;
        } catch (RepositoryException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.update_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function delete($id)
    {
        try {
            $model = $this->find($id);
            return $model->delete();
        } catch (RepositoryException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.delete_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function batchCreate(array $records)
    {
        try {
            return $this->model->insert($records);
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.batch_create_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function batchUpdate(array $ids, array $attributes)
    {
        try {
            return $this->model->whereIn('id', $ids)->update($attributes);
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.batch_update_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function batchDelete(array $ids)
    {
        try {
            return $this->model->whereIn('id', $ids)->delete();
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.batch_delete_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $model = $this->find($id);
            return $model->forceDelete();
        } catch (RepositoryException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.force_delete_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function restore($id)
    {
        try {
            $model = $this->model->withTrashed()->findOrFail($id);
            return $model->restore();
        } catch (ModelNotFoundException $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.record_not_found', ['id' => $id]), 404);
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.restore_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        try {
            return $this->model->updateOrCreate($attributes, $values);
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.update_or_create_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function exists(array $conditions)
    {
        try {
            $query = $this->model->newQuery();
            $this->applyFilters($query, $conditions);
            return $query->exists();
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.exists_check_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function count(array $conditions = [])
    {
        try {
            $query = $this->model->newQuery();
            if (!empty($conditions)) {
                $this->applyFilters($query, $conditions);
            }
            return $query->count();
        } catch (Exception $e) {
            throw new RepositoryException(__('laravel-scaffold::messages.count_failed', ['error' => $e->getMessage()]), 500);
        }
    }

    public function getModel(): Model
    {
        return $this->model;
    }
}
