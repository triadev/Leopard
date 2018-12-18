<?php
namespace Tests\Integration\Console\Commands\Mapping;

use Tests\TestCase;
use Triadev\Leopard\Facade\Leopard;

class MigrationTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    }
    
    /**
     * @test
     */
    public function it_runs_a_mapping_update()
    {
        $this->refreshElasticsearchMappings();
        
        $this->assertEquals([
            'phpunit' => [
                'mappings' => []
            ]
        ], $this->getEsMapping());
    
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
        ], $this->getEsMapping());
    }
    
    /**
     * @test
     */
    public function it_creates_a_new_index_with_mapping_and_settings()
    {
        $this->deleteElasticsearchMappings();
        
        $this->assertFalse(Leopard::existIndexStatement([
            'index' => $this->testModel->getDocumentIndex()
        ]));
        
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
        ], $this->getEsMapping());
    
        $settings = $this->getEsSetting();
    
        $this->assertEquals('30s', array_get($settings, 'phpunit.settings.index.refresh_interval'));
        $this->assertEquals(10, array_get($settings, 'phpunit.settings.index.number_of_replicas'));
        $this->assertEquals(12, array_get($settings, 'phpunit.settings.index.number_of_shards'));
    }
}
