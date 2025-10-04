<?php

namespace Tests\Unit\Database\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Matchish\ScoutElasticSearch\Database\Scopes\ChunkScope;
use PHPUnit\Framework\TestCase;
use Mockery;

class ChunkScopeTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_sets_start_and_end()
    {
        $scope = new ChunkScope(10, 100);
        
        $reflection = new \ReflectionClass($scope);
        $startProperty = $reflection->getProperty('start');
        $startProperty->setAccessible(true);
        $endProperty = $reflection->getProperty('end');
        $endProperty->setAccessible(true);
        
        $this->assertEquals(10, $startProperty->getValue($scope));
        $this->assertEquals(100, $endProperty->getValue($scope));
    }

    public function test_apply_with_both_start_and_end()
    {
        $builder = Mockery::mock(Builder::class);
        $model = Mockery::mock(Model::class);
        $scope = new ChunkScope(10, 100);

        $model->shouldReceive('getKeyName')->andReturn('id');
        
        $builder->shouldReceive('when')
            ->with(true, Mockery::type('Closure'))
            ->twice()
            ->andReturnSelf();

        $scope->apply($builder, $model);
        
        $this->assertTrue(true); // Assert that the method executes without error
    }

    public function test_apply_with_only_start()
    {
        $builder = Mockery::mock(Builder::class);
        $model = Mockery::mock(Model::class);
        $scope = new ChunkScope(10, null);

        $model->shouldReceive('getKeyName')->andReturn('id');
        
        $builder->shouldReceive('when')
            ->with(true, Mockery::type('Closure'))
            ->once()
            ->andReturnSelf();
            
        $builder->shouldReceive('when')
            ->with(false, Mockery::type('Closure'))
            ->once()
            ->andReturnSelf();

        $scope->apply($builder, $model);
        
        $this->assertTrue(true); // Assert that the method executes without error
    }

    public function test_apply_with_only_end()
    {
        $builder = Mockery::mock(Builder::class);
        $model = Mockery::mock(Model::class);
        $scope = new ChunkScope(null, 100);

        $model->shouldReceive('getKeyName')->andReturn('id');
        
        $builder->shouldReceive('when')
            ->with(false, Mockery::type('Closure'))
            ->once()
            ->andReturnSelf();
            
        $builder->shouldReceive('when')
            ->with(true, Mockery::type('Closure'))
            ->once()
            ->andReturnSelf();

        $scope->apply($builder, $model);
        
        $this->assertTrue(true); // Assert that the method executes without error
    }

    public function test_apply_with_null_start_and_end()
    {
        $builder = Mockery::mock(Builder::class);
        $model = Mockery::mock(Model::class);
        $scope = new ChunkScope(null, null);

        $model->shouldReceive('getKeyName')->andReturn('id');
        
        $builder->shouldReceive('when')
            ->with(false, Mockery::type('Closure'))
            ->twice()
            ->andReturnSelf();

        $scope->apply($builder, $model);
        
        $this->assertTrue(true); // Assert that the method executes without error
    }

    public function test_apply_where_conditions()
    {
        $builder = Mockery::mock(Builder::class);
        $model = Mockery::mock(Model::class);
        $scope = new ChunkScope(10, 100);

        $model->shouldReceive('getKeyName')->andReturn('id');
        
        $builder->shouldReceive('when')
            ->with(true, Mockery::on(function ($closure) use ($builder, $model) {
                $mockQuery = Mockery::mock(Builder::class);
                $mockQuery->shouldReceive('where')->with('id', '>', 10)->andReturnSelf();
                $result = $closure($mockQuery);
                return $result === $mockQuery;
            }))
            ->once()
            ->andReturnSelf();
            
        $builder->shouldReceive('when')
            ->with(true, Mockery::on(function ($closure) use ($builder, $model) {
                $mockQuery = Mockery::mock(Builder::class);
                $mockQuery->shouldReceive('where')->with('id', '<=', 100)->andReturnSelf();
                $result = $closure($mockQuery);
                return $result === $mockQuery;
            }))
            ->once()
            ->andReturnSelf();

        $scope->apply($builder, $model);
        
        $this->assertTrue(true); // Assert that the method executes without error
    }

    public function test_key_returns_class_name()
    {
        $scope = new ChunkScope(10, 100);
        $this->assertEquals(ChunkScope::class, $scope->key());
    }
}