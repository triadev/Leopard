<?php
namespace Tests\Unit\Business\Dsl\Compound;

use Tests\TestCase;
use Triadev\Es\ODM\Business\Dsl\Compound\FunctionScore;
use Triadev\Es\ODM\Business\Dsl\Search;
use Triadev\Es\ODM\Contract\ElasticsearchManagerContract;

class FunctionScoreTest extends TestCase
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
    }
    
    /**
     * @test
     */
    public function it_builds_a_weight_function_score_query()
    {
        $result = $this->manager->search()->functionScore(
            function (Search $search) {
                $search->term('FIELD1', 'VALUE1');
            },
            function (FunctionScore $functionScore) {
                $functionScore->weight(
                    1.1,
                    function (Search $search) {
                        $search->term('FIELD2', 'VALUE2');
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
                            'weight' => 1.1,
                            'filter' => [
                                'term' => [
                                    'FIELD2' => 'VALUE2'
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
    public function it_builds_a_random_function_score_query()
    {
        $result = $this->manager->search()->functionScore(
            function (Search $search) {
                $search->term('FIELD1', 'VALUE1');
            },
            function (FunctionScore $functionScore) {
                $functionScore->random();
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
                            'random_score' => new \stdClass()
                        ]
                    ]
                ]
            ]
        ], $result);
    }
}
