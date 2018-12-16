<?php
namespace Tests\Integration\Console\Commands\Mapping;

use Tests\TestCase;
use Triadev\Es\ODM\Contract\Repository\MappingLogRepositoryContract;

class RollbackTest extends TestCase
{
    /** @var MappingLogRepositoryContract */
    private $repository;
    
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->repository = app(MappingLogRepositoryContract::class);
    
        $this->repository->add('mapping1');
        $this->repository->add('mapping2');
        $this->repository->add('mapping3');
    
        $this->assertCount(3, $this->repository->all());
    }
    
    /**
     * @test
     */
    public function it_deletes_all_run_mappings()
    {
        $this->artisan('triadev:mapping:rollback');
    
        $this->assertCount(0, $this->repository->all());
    }
    
    /**
     * @test
     */
    public function it_deletes_all_run_mappings_if_steps_is_equal_with_logs()
    {
        $this->artisan('triadev:mapping:rollback', [
            '--steps' => 3
        ]);
        
        $this->assertCount(0, $this->repository->all());
    }
    
    /**
     * @test
     */
    public function it_deletes_all_run_mappings_if_steps_is_higher_then_logs()
    {
        $this->artisan('triadev:mapping:rollback', [
            '--steps' => 4
        ]);
        
        $this->assertCount(0, $this->repository->all());
    }
    
    /**
     * @test
     */
    public function it_deletes_the_last_two_steps_of_run_mappings()
    {
        $this->artisan('triadev:mapping:rollback', [
            '--steps' => 2
        ]);
        
        $logs = $this->repository->all();
        
        $this->assertCount(1, $logs);
        $this->assertEquals('mapping1', $logs[0]['mapping']);
    }
}
