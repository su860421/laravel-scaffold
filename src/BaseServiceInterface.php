<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold;

interface BaseServiceInterface
{
    public function index(
        int $perPage = 0,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        array $relationships = [],
        array $columns = ['*'],
        array $filters = []
    );

    public function find($id, array $columns = ['*'], array $relationships = []);

    public function create(array $attributes);

    public function update($id, array $attributes);

    public function delete($id);

    public function batchCreate(array $records);

    public function batchUpdate(array $ids, array $attributes);

    public function batchDelete(array $ids);

    public function updateOrCreate(array $attributes, array $values = []);

    public function exists(array $conditions);

    public function count(array $conditions = []);
}
