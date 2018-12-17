<?php
namespace Tests\Integration\Business\Mapping;

use Tests\TestCase;
use Triadev\Es\ODM\Business\Mapping\Mapper;

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
        ], $this->getEsMapping());
        
        app()->make(Mapper::class)->run($this->getMappingsPath());
    
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
}
