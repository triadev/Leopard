<?php
namespace Tests\Integration\Console\Commands\Mapping;

use Tests\TestCase;
use Triadev\Es\ODM\Facade\EsManager;

class MigrationTest extends TestCase
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
    public function it_runs_a_mapping_update()
    {
        $this->assertEquals([
            'phpunit' => [
                'mappings' => []
            ]
        ], EsManager::getEsClient()->indices()->getMapping(['index' => 'phpunit']));
        
        $this->artisan('triadev:mapping:migrate', [
            '--index' => 'phpunit',
            '--type' => 'test'
        ]);
        
        $this->assertEquals([
            'phpunit' => [
                'mappings' => [
                    'test' => [
                        'properties' => [
                            'id' => [
                                'type' => 'integer'
                            ],
                            'name' => [
                                'type' => 'text'
                            ],
                            'email' => [
                                'type' => 'keyword'
                            ]
                        ]
                    ]
                ]
            ]
        ], EsManager::getEsClient()->indices()->getMapping(['index' => 'phpunit']));
    }
}