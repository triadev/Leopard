<?php
namespace Tests\Integration\Business\Dsl;

use Tests\TestCase;
use Triadev\Es\ODM\Contract\ElasticsearchManagerContract;

class SearchTest extends TestCase
{
    /** @var ElasticsearchManagerContract */
    private $manager;
    
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->manager = app(ElasticsearchManagerContract::class);
        
        if (!$this->manager->getEsClient()->indices()->exists(['index' => 'index'])) {
            $this->manager->getEsClient()->indices()->create([
                'index' => 'index',
                'body' => [
                    'settings' => [
                        'refresh_interval' => '1s'
                    ],
                    'mappings' => [
                        'type' => [
                            'properties' => [
                                'test' => [
                                    'type' => 'keyword'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    
            $this->manager->getEsClient()->index([
                'index' => 'index',
                'type' => 'type',
                'id' => 1,
                'body' => [
                    'test' => 'phpunit'
                ]
            ]);
            
            $this->manager->getEsClient()->indices()->refresh();
        }
    }
    
    /**
     * @test
     */
    public function it_returns_an_elasticsearch_search_result_object()
    {
        $result = $this->manager->search()
            ->overwriteIndex('index')
            ->overwriteType('type')
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
