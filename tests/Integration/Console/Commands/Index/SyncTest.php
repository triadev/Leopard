<?php
namespace Tests\Integration\Console\Commands\Index;

use Illuminate\Support\Facades\DB;
use Tests\Integration\Model\Entity\TestModel;
use Tests\TestCase;
use Triadev\Leopard\Facade\Leopard;

class SyncTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->refreshElasticsearchMappings();
    }
    
    /**
     * @test
     */
    public function it_returns_an_error_output_that_index_not_exist()
    {
        $this->artisan('triadev:index:sync', [
            '--index' => 'NOT_EXIST'
        ])->expectsOutput('The index does not exist: NOT_EXIST');
    }
    
    /**
     * @test
     */
    public function it_syncs_elasticsearch_with_persist_data()
    {
        DB::table('test')->insert([
            ['name' => 'NAME1', 'email' => 'email1@test.de'],
            ['name' => 'NAME2', 'email' => 'email2@test.de']
        ]);
     
        $model = new TestModel();
        
        $this->assertEquals(0, Leopard::getEsClient()->count([
            'index' => $model->getDocumentIndex(),
            'type' => $model->getDocumentType()
        ])['count']);
        
        $this->artisan('triadev:index:sync', [
            '--index' => 'phpunit'
        ])
            ->expectsOutput('Sync index with model: Tests\Integration\Model\Entity\TestModel')
            ->expectsOutput('Indexing a chunk of 2 documents ...');
    
        Leopard::getEsClient()->indices()->refresh(['index' => $model->getDocumentIndex()]);
    
        $this->assertEquals(2, Leopard::getEsClient()->count([
            'index' => $model->getDocumentIndex(),
            'type' => $model->getDocumentType()
        ])['count']);
    }
}
