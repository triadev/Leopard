<?php
namespace Tests\Unit\Business\Dsl;

use Illuminate\Database\Eloquent\Model;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Tests\TestCase;
use Triadev\Leopard\Busines\Dsl\Query\Specialized;
use Triadev\Leopard\Business\Dsl\Aggregation;
use Triadev\Leopard\Business\Dsl\Query\Compound;
use Triadev\Leopard\Business\Dsl\Query\InnerHit;
use Triadev\Leopard\Business\Dsl\Query\Joining;
use Triadev\Leopard\Business\Dsl\Query\TermLevel;
use Triadev\Leopard\Business\Dsl\Query\Fulltext;
use Triadev\Leopard\Business\Dsl\Query\Geo;
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
        ], $this->searchDsl->termLevel(function (TermLevel $boolQuery) {
            $boolQuery->must()->matchAll();
        })->toDsl());
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
        ], $this->searchDsl->termLevel(function (TermLevel $boolQuery) {
            $boolQuery->term('FIELD', 'VALUE');
        })->toDsl());
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
        ], $this->searchDsl->fulltext(function (Fulltext $fulltext) {
            $fulltext->match('FIELD', 'QUERY');
        })->toDsl());
    }
    
    /**
     * @test
     */
    public function it_builds_a_geo_query()
    {
        $result = $this->searchDsl->geo(function (Geo $geo) {
            $geo->filter()->geoDistance(
                'FIELD',
                '10km',
                new Location(1, 2)
            );
        })->toDsl();
        
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
        $result = $this->searchDsl->joining(function (Joining $joining) {
            $joining->nested('PATH', function (Search $search) {
                $search->termLevel(function (TermLevel $boolQuery) {
                    $boolQuery
                        ->filter()
                        ->term('FIELD1', 'VALUE1')
                        ->term('FIELD2', 'VALUE2');
                });
            });
        })->toDsl();
        
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
        $result = $this->searchDsl->specialized(function (Specialized $specialized) {
            $specialized->moreLikeThis('LIKE');
        })->toDsl();
        
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
            ->termLevel(function (TermLevel $boolQuery) {
                $boolQuery->term('FIELD', 'VALUE');
            })
            ->paginate(3, 25)
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
            ->termLevel(function (TermLevel $boolQuery) {
                $boolQuery->term('FIELD', 'VALUE');
            })
            ->minScore(5)
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
            ->termLevel(function (TermLevel $boolQuery) {
                $boolQuery->term('FIELD', 'VALUE');
            })
            ->sort('FIELD1', FieldSort::DESC)
            ->sort('FIELD2', FieldSort::ASC)
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
        $result = $this->searchDsl->compound(function (Compound $compound) {
            $compound->boosting(
                new TermQuery('FIELD1', 'VALUE1'),
                new TermQuery('FIELD2', 'VALUE2'),
                1.2
            );
        })->toDsl();
        
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
        $result = $this->searchDsl->innerHit(function (InnerHit $innerHit) {
            $innerHit->nestedInnerHit('NAME', 'PATH', function (Search $search) {
                $search->termLevel(function (TermLevel $boolQuery) {
                    $boolQuery->term('FIELD', 'VALUE');
                });
            });
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
}
