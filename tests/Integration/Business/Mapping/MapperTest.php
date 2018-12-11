<?php
namespace Tests\Integration\Business\Mapping;

use Tests\TestCase;
use Triadev\Es\ODM\Business\Mapping\Mapper;
use Triadev\Es\ODM\Facade\EsManager;

class MapperTest extends TestCase
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
        
        app()->make(Mapper::class)->run(__DIR__. '/../../../Resources/Database/mappings');
    
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
