<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold;

use Illuminate\Database\Eloquent\Collection;

abstract class BaseService implements BaseServiceInterface
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function index(
        int $perPage = 0,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        array $relationships = [],
        array $columns = ['*'],
        array $filters = []
    ) {
        return $this->repository->index($perPage, $orderBy, $orderDirection, $relationships, $columns, $filters);
    }

    public function find($id, array $columns = ['*'], array $relationships = [])
    {
        return $this->repository->find($id, $columns, $relationships);
    }

    public function create(array $attributes)
    {
        return $this->repository->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->repository->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function batchCreate(array $records)
    {
        return $this->repository->batchCreate($records);
    }

    public function batchUpdate(array $ids, array $attributes)
    {
        return $this->repository->batchUpdate($ids, $attributes);
    }

    public function batchDelete(array $ids)
    {
        return $this->repository->batchDelete($ids);
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->repository->updateOrCreate($attributes, $values);
    }

    public function exists(array $conditions)
    {
        return $this->repository->exists($conditions);
    }

    public function count(array $conditions = [])
    {
        return $this->repository->count($conditions);
    }
}
