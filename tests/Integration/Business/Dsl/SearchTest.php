<?php
namespace Tests\Integration\Business\Dsl;

use Tests\TestCase;
use Triadev\Es\ODM\Facade\EsManager;

class SearchTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    
        if (EsManager::getEsClient()->indices()->exists(['index' => 'phpunit'])) {
            EsManager::getEsClient()->indices()->delete(['index' => 'phpunit']);
        }
    
        EsManager::getEsClient()->indices()->create([
            'index' => 'phpunit',
            'body' => [
                'settings' => [
                    'refresh_interval' => '1s'
                ],
                'mappings' => [
                    'test' => [
                        'properties' => [
                            'test' => [
                                'type' => 'keyword'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * @test
     */
    public function it_returns_an_elasticsearch_search_result_object()
    {
        EsManager::indexStatement([
            'index' => 'phpunit',
            'type' => 'test',
            'id' => 1,
            'body' => [
                'test' => 'phpunit'
            ]
        ]);
    
        EsManager::getEsClient()->indices()->refresh();
        
        $result = EsManager::search()
            ->overwriteIndex('phpunit')
            ->overwriteType('test')
            ->term('test', 'phpunit')
            ->get();
        
        $this->assertIsInt($result->getTook());
        $this->assertIsBool($result->isTimedOut());
        $this->assertIsFloat($result->getMaxScore());
        
        $this->assertEquals(5, $result->getShards()['total']);
        $this->assertEquals(5, $result->getShards()['successful']);
        $this->assertEquals(1, $result->getTotalHits());
        
        $this->assertNotEmpty($result->getHits());
    }
}
