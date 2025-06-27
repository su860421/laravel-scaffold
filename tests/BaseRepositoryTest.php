<?php

namespace JoeSu\LaravelScaffold\Tests;

use JoeSu\LaravelScaffold\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class BaseRepositoryTest extends TestCase
{
    public function test_repository_can_be_instantiated()
    {
        $model = new class extends Model {};
        $repository = new class($model) extends BaseRepository {};

        $this->assertInstanceOf(BaseRepository::class, $repository);
    }

    public function test_repository_has_model()
    {
        $model = new class extends Model {};
        $repository = new class($model) extends BaseRepository {};

        $this->assertSame($model, $repository->getModel());
    }
}
