<?php
namespace Tests\Unit\Business\Dsl;

use Illuminate\Database\Eloquent\Model;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Tests\TestCase;
use Triadev\Leopard\Business\Dsl\Aggregation;
use Triadev\Leopard\Business\Dsl\Search;
use Triadev\Leopard\Model\Location;
use Triadev\Leopard\Searchable;

class SearchTest extends TestCase
{
    /** @var Search */
    private $searchDsl;
    
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->searchDsl = app()->makeWith(Search::class, [
            'search' => new \ONGR\ElasticsearchDSL\Search()
        ]);
    }
    
    /**
     * @test
     */
    public function it_gets_the_search_instance()
    {
        $this->assertInstanceOf(
            \ONGR\ElasticsearchDSL\Search::class,
            $this->searchDsl->getSearch()
        );
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_if_the_eloquent_model_is_not_searchable()
    {
        $model = new class extends Model {};
        $this->searchDsl->model($model);
    }
    
    /**
     * @test
     */
    public function it_appends_an_searchable_eloquent_model()
    {
        $this->assertNotEquals('INDEX', $this->searchDsl->getIndex());
        $this->assertNotEquals('TYPE', $this->searchDsl->getType());
        
        $this->searchDsl->model(new class extends Model {
            use Searchable;
            
            public $documentIndex = 'INDEX';
            
            public $documentType = 'TYPE';
        });
        
        $this->assertEquals('INDEX', $this->searchDsl->getIndex());
        $this->assertEquals('TYPE', $this->searchDsl->getType());
    }
    
    /**
     * @test
     */
    public function it_builds_a_match_all_query()
    {
        $this->assertEquals([
            'query' => [
                'match_all' => new \stdClass()
            ]
        ], $this->searchDsl->termLevel()->matchAll()->toDsl());
    }
    
    /**
     * @test
     */
    public function it_builds_a_terms_level_query()
    {
        $this->assertEquals([
            'query' => [
                'term' => [
                    'FIELD' => 'VALUE'
                ]
            ]
        ], $this->searchDsl->termLevel()->term('FIELD', 'VALUE')->toDsl());
    }
    
    /**
     * @test
     */
    public function it_builds_a_fulltext_query()
    {
        $this->assertEquals([
            'query' => [
                'match' => [
                    'FIELD' => [
                        'query' => 'QUERY'
                    ]
                ]
            ]
        ], $this->searchDsl->fulltext()->match('FIELD', 'QUERY')->toDsl());
    }
    
    /**
     * @test
     */
    public function it_builds_a_geo_query()
    {
        $result = $this->searchDsl->geo()->filter()->geoDistance(
            'FIELD',
            '10km',
            new Location(1, 2)
        )->toDsl();
        
        $this->assertEquals([
            'geo_distance' => [
                'distance' => '10km',
                'FIELD' => [
                    'lat' => 1.0,
                    'lon' => 2.0
                ]
            ]
        ], array_get($result, 'query.bool.filter.0'));
    }
    
    /**
     * @test
     */
    public function it_builds_a_joining_query()
    {
        $result = $this->searchDsl->joining()->nested(
            'PATH', function (Search $search) {
                $search
                    ->termLevel()
                        ->filter()
                            ->term('FIELD1', 'VALUE1')
                            ->term('FIELD2', 'VALUE2');
            }
        )->toDsl();
        
        $this->assertEquals([
            'query' => [
                'nested' => [
                    'path' => 'PATH',
                    'query' => [
                        'bool' => [
                            'filter' => [
                                [
                                    'term' => [
                                        'FIELD1' => 'VALUE1'
                                    ]
                                ],
                                [
                                    'term' => [
                                        'FIELD2' => 'VALUE2'
                                    ]
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
    public function it_builds_a_specialized_query()
    {
        $result = $this->searchDsl->specialized()->moreLikeThis('LIKE')->toDsl();
        
        $this->assertEquals([
            'query' => [
                'more_like_this' => [
                    'like' => 'LIKE'
                ]
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_a_paginate_query()
    {
        $result = $this->searchDsl
            ->paginate(3, 25)
            ->termLevel()->term('FIELD', 'VALUE')
            ->toDsl();
    
        $this->assertEquals([
            'query' => [
                'term' => [
                    'FIELD' => 'VALUE'
                ]
            ],
            'from' => 50,
            'size' => 25
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_a_min_score_query()
    {
        $result = $this->searchDsl
            ->minScore(5)
            ->termLevel()->term('FIELD', 'VALUE')
            ->toDsl();
        
        $this->assertEquals([
            'query' => [
                'term' => [
                    'FIELD' => 'VALUE'
                ]
            ],
            'min_score' => 5
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_a_sorted_query()
    {
        $result = $this->searchDsl
            ->sort('FIELD1', FieldSort::DESC)
            ->sort('FIELD2', FieldSort::ASC)
            ->termLevel()->term('FIELD', 'VALUE')
            ->toDsl();
        
        $this->assertEquals([
            'query' => [
                'term' => [
                    'FIELD' => 'VALUE'
                ]
            ],
            'sort' => [
                [
                    'FIELD1' => [
                        'order' => 'desc'
                    ]
                ],
                [
                    'FIELD2' => [
                        'order' => 'asc'
                    ]
                ]
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_an_aggregation_query()
    {
        $result = $this->searchDsl->aggregation(function (Aggregation $aggregation) {
            $aggregation->metric(function (Aggregation\Metric $metric) {
                $metric->avg('AVG');
            });
        })->toDsl();
        
        $this->assertEquals([
            'aggregations' => [
                'AVG' => [
                    'avg' => []
                ]
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_a_compound_query()
    {
        $result = $this->searchDsl->compound()->boosting(
            new TermQuery('FIELD1', 'VALUE1'),
            new TermQuery('FIELD2', 'VALUE2'),
            1.2
        )->toDsl();
        
        $this->assertEquals([
            'query' => [
                'boosting' => [
                    'positive' => [
                        'term' => [
                            'FIELD1' => 'VALUE1'
                        ]
                    ],
                    'negative' => [
                        'term' => [
                            'FIELD2' => 'VALUE2'
                        ]
                    ],
                    'negative_boost' => 1.2
                ]
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_an_inner_hit_query()
    {
        $result = $this->searchDsl->innerHit()->nestedInnerHit('NAME', 'PATH', function (Search $search) {
            $search->termLevel()->term('FIELD', 'VALUE');
        })->toDsl();
        
        $this->assertEquals([
            'inner_hits' => [
                'NAME' => [
                    'path' => [
                        'PATH' => [
                            'query' => [
                                'term' => [
                                    'FIELD' => 'VALUE'
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
    public function it_builds_a_nested_terms_level_query()
    {
        $result = $this->searchDsl
            ->termLevel()
                ->term('FIELD', 'VALUE')
                ->bool(function (Search $search) {
                    $search
                        ->termLevel()
                            ->term('FIELD1', 'VALUE1')
                            ->term('FIELD2', 'VALUE2')
                            ->bool(function (Search $search) {
                                $search
                                    ->fulltext()
                                        ->match('FIELD1', 'QUERY1')
                                        ->matchPhrase('FIELD2', 'QUERY2');
                            });
                })
                ->prefix('FIELD', 'VALUE')
                ->bool(function (Search $search) {
                    $search
                        ->fulltext()
                            ->filter()
                                ->match('FIELD1', 'QUERY1')
                                ->matchPhrase('FIELD2', 'QUERY2');
                })
            ->toDsl();
        
        $this->assertEquals([
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'term' => [
                                'FIELD' => 'VALUE'
                            ]
                        ],
                        [
                            'bool' => [
                                'must' => [
                                    [
                                        'term' => [
                                            'FIELD1' => 'VALUE1'
                                        ]
                                    ],
                                    [
                                        'term' => [
                                            'FIELD2' => 'VALUE2'
                                        ]
                                    ],
                                    [
                                        'bool' => [
                                            'must' => [
                                                [
                                                    'match' => [
                                                        'FIELD1' => [
                                                            'query' => 'QUERY1'
                                                        ]
                                                    ]
                                                ],
                                                [
                                                    'match_phrase' => [
                                                        'FIELD2' => [
                                                            'query' => 'QUERY2'
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'prefix' => [
                                'FIELD' => [
                                    'value' => 'VALUE'
                                ]
                            ]
                        ],
                        [
                            'bool' => [
                                'filter' => [
                                    [
                                        'match' => [
                                            'FIELD1' => [
                                                'query' => 'QUERY1'
                                            ]
                                        ]
                                    ],
                                    [
                                        'match_phrase' => [
                                            'FIELD2' => [
                                                'query' => 'QUERY2'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ], $result);
    }
}
