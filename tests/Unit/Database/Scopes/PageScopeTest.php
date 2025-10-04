<?php

namespace Tests\Unit\Database\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Matchish\ScoutElasticSearch\Database\Scopes\PageScope;
use PHPUnit\Framework\TestCase;
use Mockery;

class PageScopeTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_sets_page_and_per_page()
    {
        $scope = new PageScope(2, 25);
        
        $reflection = new \ReflectionClass($scope);
        $pageProperty = $reflection->getProperty('page');
        $pageProperty->setAccessible(true);
        $perPageProperty = $reflection->getProperty('perPage');
        $perPageProperty->setAccessible(true);
        
        $this->assertEquals(2, $pageProperty->getValue($scope));
        $this->assertEquals(25, $perPageProperty->getValue($scope));
    }

    public function test_apply_calls_for_page_on_builder()
    {
        $builder = Mockery::mock(Builder::class);
        $model = Mockery::mock(Model::class);
        $scope = new PageScope(3, 50);

        $builder->shouldReceive('forPage')
            ->with(3, 50)
            ->once()
            ->andReturnSelf();

        $scope->apply($builder, $model);
        
        $this->assertTrue(true); // Assert that the method executes without error
    }

    public function test_apply_with_first_page()
    {
        $builder = Mockery::mock(Builder::class);
        $model = Mockery::mock(Model::class);
        $scope = new PageScope(1, 10);

        $builder->shouldReceive('forPage')
            ->with(1, 10)
            ->once()
            ->andReturnSelf();

        $scope->apply($builder, $model);
        
        $this->assertTrue(true);
    }

    public function test_apply_with_large_page_size()
    {
        $builder = Mockery::mock(Builder::class);
        $model = Mockery::mock(Model::class);
        $scope = new PageScope(1, 1000);

        $builder->shouldReceive('forPage')
            ->with(1, 1000)
            ->once()
            ->andReturnSelf();

        $scope->apply($builder, $model);
        
        $this->assertTrue(true);
    }

    public function test_apply_with_high_page_number()
    {
        $builder = Mockery::mock(Builder::class);
        $model = Mockery::mock(Model::class);
        $scope = new PageScope(999, 15);

        $builder->shouldReceive('forPage')
            ->with(999, 15)
            ->once()
            ->andReturnSelf();

        $scope->apply($builder, $model);
        
        $this->assertTrue(true);
    }
}