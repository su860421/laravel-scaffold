<?php

namespace Tests;

use Tests\TestCase;
use JoeSu\LaravelScaffold\BaseRepository;
use JoeSu\LaravelScaffold\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;

class BaseRepositoryTest extends TestCase
{
    protected $repository;
    protected $mockModel;

    protected function setUp(): void
    {
        parent::setUp();

        // 建立 Mock Model
        $this->mockModel = Mockery::mock(Model::class);

        // 建立測試用的 Repository 實例
        $this->repository = new TestRepository($this->mockModel);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testNewQueryReturnsModelQuery()
    {
        $mockQuery = Mockery::mock();
        $this->mockModel->shouldReceive('newQuery')->once()->andReturn($mockQuery);

        $result = $this->repository->newQuery();

        $this->assertSame($mockQuery, $result);
    }

    public function testIndexReturnsCollectionWhenPerPageIsZero()
    {
        $mockQuery = Mockery::mock();
        $mockCollection = new Collection();

        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('get')->once()->andReturn($mockCollection);

        $result = $this->repository->index(0);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testIndexReturnsPaginatorWhenPerPageIsGreaterThanZero()
    {
        $mockQuery = Mockery::mock();
        $mockPaginator = Mockery::mock(LengthAwarePaginator::class);

        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('paginate')->with(10)->once()->andReturn($mockPaginator);

        $result = $this->repository->index(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function testIndexWithRelationships()
    {
        $mockQuery = Mockery::mock();
        $mockCollection = new Collection();

        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('with')->with(['posts'])->once()->andReturnSelf();
        $mockQuery->shouldReceive('get')->once()->andReturn($mockCollection);

        $result = $this->repository->index(0, null, null, ['posts']);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testIndexWithRelationshipCounts()
    {
        $mockQuery = Mockery::mock();
        $mockCollection = new Collection();

        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('withCount')->with(['posts'])->once()->andReturnSelf();
        $mockQuery->shouldReceive('get')->once()->andReturn($mockCollection);

        $result = $this->repository->index(0, null, null, ['posts.count']);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testIndexWithSorting()
    {
        $mockQuery = Mockery::mock();
        $mockCollection = new Collection();

        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('orderBy')->with('created_at', 'desc')->once()->andReturnSelf();
        $mockQuery->shouldReceive('get')->once()->andReturn($mockCollection);

        $result = $this->repository->index(0, 'created_at', 'desc');

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testIndexWithFilters()
    {
        $mockQuery = Mockery::mock();
        $mockCollection = new Collection();

        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('where')->with('status', '=', 'active')->once()->andReturnSelf();
        $mockQuery->shouldReceive('get')->once()->andReturn($mockCollection);

        $result = $this->repository->index(0, null, null, [], ['*'], [['status', 'active']]);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testFindReturnsModel()
    {
        $mockQuery = Mockery::mock();
        $mockModel = Mockery::mock(Model::class);

        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('findOrFail')->with(1)->once()->andReturn($mockModel);

        $result = $this->repository->find(1);

        $this->assertSame($mockModel, $result);
    }

    public function testFindWithCustomColumns()
    {
        $mockQuery = Mockery::mock();
        $mockModel = Mockery::mock(Model::class);

        $this->mockModel->shouldReceive('select')->with(['id', 'name'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('findOrFail')->with(1)->once()->andReturn($mockModel);

        $result = $this->repository->find(1, ['id', 'name']);

        $this->assertSame($mockModel, $result);
    }

    public function testFindWithRelationships()
    {
        $mockQuery = Mockery::mock();
        $mockModel = Mockery::mock(Model::class);

        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('with')->with(['posts'])->once()->andReturnSelf();
        $mockQuery->shouldReceive('findOrFail')->with(1)->once()->andReturn($mockModel);

        $result = $this->repository->find(1, ['*'], ['posts']);

        $this->assertSame($mockModel, $result);
    }

    public function testCreateReturnsModel()
    {
        $attributes = ['name' => 'Test User', 'email' => 'test@example.com'];
        $mockModel = Mockery::mock(Model::class);

        $this->mockModel->shouldReceive('create')->with($attributes)->once()->andReturn($mockModel);

        $result = $this->repository->create($attributes);

        $this->assertSame($mockModel, $result);
    }

    public function testUpdateReturnsModel()
    {
        $attributes = ['name' => 'Updated User'];
        $mockModel = Mockery::mock(Model::class);
        $mockQuery = Mockery::mock();

        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('findOrFail')->with(1)->once()->andReturn($mockModel);
        $mockModel->shouldReceive('update')->with($attributes)->once()->andReturn(true);

        $result = $this->repository->update(1, $attributes);

        $this->assertSame($mockModel, $result);
    }

    public function testDeleteReturnsBoolean()
    {
        $mockModel = Mockery::mock(Model::class);
        $mockQuery = Mockery::mock();

        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('findOrFail')->with(1)->once()->andReturn($mockModel);
        $mockModel->shouldReceive('delete')->once()->andReturn(true);

        $result = $this->repository->delete(1);

        $this->assertTrue($result);
    }

    public function testBatchCreateReturnsBoolean()
    {
        $records = [
            ['name' => 'User 1', 'email' => 'user1@example.com'],
            ['name' => 'User 2', 'email' => 'user2@example.com']
        ];

        $this->mockModel->shouldReceive('insert')->with($records)->once()->andReturn(true);

        $result = $this->repository->batchCreate($records);

        $this->assertTrue($result);
    }

    public function testBatchUpdateReturnsInteger()
    {
        $ids = [1, 2, 3];
        $attributes = ['status' => 'inactive'];

        $this->mockModel->shouldReceive('whereIn')->with('id', $ids)->once()->andReturnSelf();
        $this->mockModel->shouldReceive('update')->with($attributes)->once()->andReturn(3);

        $result = $this->repository->batchUpdate($ids, $attributes);

        $this->assertEquals(3, $result);
    }

    public function testBatchDeleteReturnsInteger()
    {
        $ids = [1, 2, 3];

        $this->mockModel->shouldReceive('whereIn')->with('id', $ids)->once()->andReturnSelf();
        $this->mockModel->shouldReceive('delete')->once()->andReturn(3);

        $result = $this->repository->batchDelete($ids);

        $this->assertEquals(3, $result);
    }

    public function testUpdateOrCreateReturnsModel()
    {
        $attributes = ['email' => 'test@example.com'];
        $values = ['name' => 'Test User'];
        $mockModel = Mockery::mock(Model::class);

        $this->mockModel->shouldReceive('updateOrCreate')->with($attributes, $values)->once()->andReturn($mockModel);

        $result = $this->repository->updateOrCreate($attributes, $values);

        $this->assertSame($mockModel, $result);
    }

    public function testExistsReturnsBoolean()
    {
        $conditions = [['status', 'active']];
        $mockQuery = Mockery::mock();

        $this->mockModel->shouldReceive('newQuery')->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('where')->with('status', '=', 'active')->once()->andReturnSelf();
        $mockQuery->shouldReceive('exists')->once()->andReturn(true);

        $result = $this->repository->exists($conditions);

        $this->assertTrue($result);
    }

    public function testCountReturnsInteger()
    {
        $conditions = [['status', 'active']];
        $mockQuery = Mockery::mock();

        $this->mockModel->shouldReceive('newQuery')->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('where')->with('status', '=', 'active')->once()->andReturnSelf();
        $mockQuery->shouldReceive('count')->once()->andReturn(5);

        $result = $this->repository->count($conditions);

        $this->assertEquals(5, $result);
    }

    public function testCountWithoutConditions()
    {
        $mockQuery = Mockery::mock();

        $this->mockModel->shouldReceive('newQuery')->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('count')->once()->andReturn(10);

        $result = $this->repository->count();

        $this->assertEquals(10, $result);
    }

    public function testChunkProcessesRecords()
    {
        $mockQuery = Mockery::mock();
        $callback = function ($records) {
            return true;
        };
        $conditions = [['status', 'active']];

        $this->mockModel->shouldReceive('newQuery')->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('where')->with('status', '=', 'active')->once()->andReturnSelf();
        $mockQuery->shouldReceive('chunk')->with(100, $callback)->once()->andReturn(true);

        $result = $this->repository->chunk(100, $callback, $conditions);

        $this->assertTrue($result);
    }

    public function testCursorReturnsCursor()
    {
        $mockQuery = Mockery::mock();
        $mockCursor = Mockery::mock();
        $conditions = [['status', 'active']];

        $this->mockModel->shouldReceive('newQuery')->once()->andReturn($mockQuery);
        $mockQuery->shouldReceive('where')->with('status', '=', 'active')->once()->andReturnSelf();
        $mockQuery->shouldReceive('cursor')->once()->andReturn($mockCursor);

        $result = $this->repository->cursor($conditions);

        $this->assertSame($mockCursor, $result);
    }

    public function testGetModelReturnsModel()
    {
        $result = $this->repository->getModel();

        $this->assertSame($this->mockModel, $result);
    }

    public function testInvalidFilterFormatThrowsException()
    {
        $this->expectException(RepositoryException::class);

        $mockQuery = Mockery::mock();
        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);

        $this->repository->index(0, null, null, [], ['*'], ['invalid_filter']);
    }

    public function testInvalidOrderDirectionThrowsException()
    {
        $this->expectException(RepositoryException::class);

        $mockQuery = Mockery::mock();
        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);

        $this->repository->index(0, 'created_at', 'invalid');
    }

    public function testInvalidRelationFieldFormatThrowsException()
    {
        $this->expectException(RepositoryException::class);

        $mockQuery = Mockery::mock();
        $this->mockModel->shouldReceive('select')->with(['*'])->once()->andReturn($mockQuery);

        $this->repository->index(0, null, null, [], ['*'], [['invalid.relation.field', 'value']]);
    }
}

// 測試用的 Repository 類別
class TestRepository extends BaseRepository
{
    protected function getAllowedSortColumns(): array
    {
        return ['id', 'name', 'created_at', 'updated_at'];
    }
}
