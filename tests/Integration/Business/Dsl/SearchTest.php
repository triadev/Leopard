<?php
namespace Tests\Integration\Business\Dsl;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use Tests\Integration\Model\Entity\TestModel;
use Tests\TestCase;
use Triadev\Es\Dsl\Dsl\FunctionScore;
use Triadev\Es\Dsl\Dsl\Search;
use Triadev\Leopard\Facade\Leopard;

class SearchTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->refreshElasticsearchMappings();
    
        Leopard::getEsClient()->indices()->putMapping([
            'index' => 'phpunit',
            'type' => 'test',
            'body' => [
                'properties' => [
                    'test' => [
                        'type' => 'keyword'
                    ]
                ]
            ]
        ]);
    }
    
    private function buildPayload(array $payload = []) : array
    {
        return array_merge([
            'index' => $this->testModel->getDocumentIndex(),
            'type' => $this->testModel->getDocumentType()
        ], $payload);
    }
    
    private function createTestDocument()
    {
        Leopard::indexStatement($this->buildPayload([
            'id' => 1,
            'body' => [
                'test' => 'phpunit'
            ]
        ]));
    
        Leopard::getEsClient()->indices()->refresh();
    }
    
    /**
     * @test
     */
    public function it_returns_an_elasticsearch_search_result_object_with_array_documents()
    {
        $this->createTestDocument();
        
        $result = Leopard::search()
            ->esIndex($this->testModel->getDocumentIndex())
            ->esType($this->testModel->getDocumentType())
            ->termLevel()
                ->term('test', 'phpunit')
            ->get();
        
        $this->assertIsInt($result->getTook());
        $this->assertIsBool($result->isTimedOut());
        $this->assertIsFloat($result->getMaxScore());
        
        $this->assertEquals(5, $result->getShards()['total']);
        $this->assertEquals(5, $result->getShards()['successful']);
        $this->assertEquals(1, $result->getTotalHits());
        
        $this->assertNotEmpty($result->getHits());
    
        foreach ($result->getHits() as $hit) {
            $this->assertTrue(is_array($hit));
        }
    }
    
    /**
     * @test
     */
    public function it_returns_an_elasticsearch_search_result_object_with_eloquent_models()
    {
        $this->createTestDocument();
        
        $result = Leopard::search()
            ->model($this->testModel)
            ->termLevel()
                ->term('test', 'phpunit')
            ->get();
        
        $this->assertIsInt($result->getTook());
        $this->assertIsBool($result->isTimedOut());
        $this->assertIsFloat($result->getMaxScore());
        
        $this->assertEquals(5, $result->getShards()['total']);
        $this->assertEquals(5, $result->getShards()['successful']);
        $this->assertEquals(1, $result->getTotalHits());
        
        $this->assertNotEmpty($result->getHits());
        
        foreach ($result->getHits() as $hit) {
            $this->assertInstanceOf(
                TestModel::class,
                $hit
            );
            
            $this->assertTrue($hit->isDocument);
            $this->assertNotNull($hit->documentScore);
            $this->assertNull($hit->documentVersion);
        }
    }
    
    /**
     * @test
     */
    public function it_gets_a_dsl_query_array()
    {
        $result = Leopard::search()->compound()->functionScore(
            function (Search $search) {
                $search->termLevel()->term('FIELD1', 'VALUE1');
            },
            function (FunctionScore $functionScore) {
                $functionScore->field(
                    'FIELD2',
                    0.2,
                    'none',
                    function (Search $search) {
                        $search->termLevel()->term('FIELD3', 'VALUE3');
                    }
                );
            }
        )->toDsl();
    
        $this->assertEquals([
            'query' => [
                'function_score' => [
                    'query' => [
                        'term' => [
                            'FIELD1' => 'VALUE1'
                        ]
                    ],
                    'functions' => [
                        [
                            'field_value_factor' => [
                                'field' => 'FIELD2',
                                'factor' => 0.2,
                                'modifier' => 'none'
                            ],
                            'filter' => [
                                'term' => [
                                    'FIELD3' => 'VALUE3'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_gets_the_search_object()
    {
        $this->assertInstanceOf(
            \ONGR\ElasticsearchDSL\Search::class,
            Leopard::search()->getSearch()
        );
    }
    
    /**
     * @test
     */
    public function it_gets_the_query_object()
    {
        $this->assertInstanceOf(
            BoolQuery::class,
            Leopard::search()->termLevel()->term('FIELD', 'VALUE')->getQuery()
        );
    }
}
