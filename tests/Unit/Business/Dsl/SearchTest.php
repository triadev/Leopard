<?php
namespace Tests\Unit\Business\Dsl;

use Illuminate\Database\Eloquent\Model;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Tests\TestCase;
use Triadev\Es\ODM\Busines\Dsl\Query\Specialized;
use Triadev\Es\ODM\Business\Dsl\Aggregation;
use Triadev\Es\ODM\Business\Dsl\Query\Compound;
use Triadev\Es\ODM\Business\Dsl\Query\InnerHit;
use Triadev\Es\ODM\Business\Dsl\Query\Joining;
use Triadev\Es\ODM\Business\Dsl\Query\TermLevel;
use Triadev\Es\ODM\Business\Dsl\Query\Fulltext;
use Triadev\Es\ODM\Business\Dsl\Query\Geo;
use Triadev\Es\ODM\Business\Dsl\Search;
use Triadev\Es\ODM\Model\Location;
use Triadev\Es\ODM\Searchable;

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
    public function it_builds_a_match_all_query()
    {
        $result = $this->searchDsl->termLevel(function (TermLevel $boolQuery) {
            $boolQuery->must()->matchAll();
        })->toDsl();
        
        $this->assertEquals([
            'query' => [
                'match_all' => new \stdClass()
            ]
        ], $result);
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
    public function it_builds_a_bool_terms_query()
    {
        $result = $this->searchDsl->termLevel(function (TermLevel $boolQuery) {
            $boolQuery
                ->must()
                    ->term('FIELD_MUST', 'VALUE_MUST')
                ->mustNot()
                    ->term('FIELD_MUST_NOT', 'VALUE_MUST_NOT')
                ->should()
                    ->term('FIELD_SHOULD', 'VALUE_SHOULD')
                ->filter()
                    ->term('FIELD_FILTER', 'VALUE_FILTER')
                    ->exists('FIELD')
                    ->fuzzy('FIELD', 'VALUE')
                    ->ids([1,2,3])
                    ->prefix('FIELD', 'VALUE')
                    ->range('FIELD', [
                        'gt' => 10,
                        'lt' => 20
                    ])
                    ->regexp('FIELD', 'VALUE')
                    ->terms('FIELD', [
                        'VALUE1',
                        'VALUE2'
                    ])
                    ->type('TYPE')
                    ->wildcard('FIELD', 'VALUE');
        })->toDsl();
        
        $this->assertEquals([
            [
                'term' => [
                    'FIELD_MUST' => 'VALUE_MUST'
                ]
            ]
        ], array_get($result, 'query.bool.must'));
    
        $this->assertEquals([
            [
                'term' => [
                    'FIELD_MUST_NOT' => 'VALUE_MUST_NOT'
                ]
            ]
        ], array_get($result, 'query.bool.must_not'));
    
        $this->assertEquals([
            [
                'term' => [
                    'FIELD_SHOULD' => 'VALUE_SHOULD'
                ]
            ]
        ], array_get($result, 'query.bool.should'));
    
        $this->assertEquals([
            [
                'term' => [
                    'FIELD_FILTER' => 'VALUE_FILTER'
                ]
            ],
            [
                'exists' => [
                    'field' => 'FIELD'
                ]
            ],
            [
                'fuzzy' => [
                    'FIELD' => [
                        'value' => 'VALUE'
                    ]
                ]
            ],
            [
                'ids' => [
                    'values' => [1,2,3]
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
                'range' => [
                    'FIELD' => [
                        'gt' => 10,
                        'lt' => 20
                    ]
                ]
            ],
            [
                'regexp' => [
                    'FIELD' => [
                        'value' => 'VALUE'
                    ]
                ]
            ],
            [
                'terms' => [
                    'FIELD' => [
                        'VALUE1',
                        'VALUE2'
                    ]
                ]
            ],
            [
                'type' => [
                    'value' => 'TYPE'
                ]
            ],
            [
                'wildcard' => [
                    'FIELD' => [
                        'value' => 'VALUE'
                    ]
                ]
            ]
        ], array_get($result, 'query.bool.filter'));
    }
    
    /**
     * @test
     */
    public function it_builds_a_bool_fulltext_query()
    {
        $result = $this->searchDsl->fulltext(function (Fulltext $fulltext) {
            $fulltext
                ->must()
                    ->match('FIELD', 'QUERY')
                    ->matchPhrase('FIELD', 'QUERY')
                    ->matchPhrasePrefix('FIELD', 'QUERY')
                    ->multiMatch([
                        'FIELD1',
                        'FIELD2'
                    ], 'QUERY')
                    ->queryString('QUERY')
                    ->simpleQueryString('QUERY')
                    ->commonTerms('FIELD', 'QUERY');
        })->toDsl();
        
        $this->assertEquals([
            'match' => [
                'FIELD' => [
                    'query' => 'QUERY'
                ]
            ]
        ], array_get($result, 'query.bool.must.0'));
    
        $this->assertEquals([
            'match_phrase' => [
                'FIELD' => [
                    'query' => 'QUERY'
                ]
            ]
        ], array_get($result, 'query.bool.must.1'));
    
        $this->assertEquals([
            'match_phrase_prefix' => [
                'FIELD' => [
                    'query' => 'QUERY'
                ]
            ]
        ], array_get($result, 'query.bool.must.2'));
    
        $this->assertEquals([
            'multi_match' => [
                'fields' => [
                    'FIELD1',
                    'FIELD2'
                ],
                'query' => 'QUERY'
            ]
        ], array_get($result, 'query.bool.must.3'));
    
    
        $this->assertEquals([
            'query_string' => [
                'query' => 'QUERY'
            ]
        ], array_get($result, 'query.bool.must.4'));
    
        $this->assertEquals([
            'simple_query_string' => [
                'query' => 'QUERY'
            ]
        ], array_get($result, 'query.bool.must.5'));
    
        $this->assertEquals([
            'common' => [
                'FIELD' => [
                    'query' => 'QUERY'
                ]
            ]
        ], array_get($result, 'query.bool.must.6'));
    }
    
    /**
     * @test
     */
    public function it_builds_a_bool_geo_query()
    {
        $result = $this->searchDsl->geo(function (Geo $geo) {
            $geo
                ->filter()
                    ->geoShape([])
                    ->geoBoundingBox('FIELD', [
                        new Location(1, 2),
                        new Location(3, 4)
                    ])
                    ->geoDistance('FIELD', '10km', new Location(1, 2))
                    ->geoPolygon('FIELD', [
                        new Location(1, 2),
                        new Location(3, 4)
                    ]);
        })->toDsl();
        
        $this->assertEquals([
            'geo_shape' => []
        ], array_get($result, 'query.bool.filter.0'));
        
        $this->assertEquals([
            'geo_bounding_box' => [
                'FIELD' => [
                    'top_left' => [
                        'lat' => 1.0,
                        'lon' => 2.0
                    ],
                    'bottom_right' => [
                        'lat' => 3.0,
                        'lon' => 4.0
                    ]
                ]
            ]
        ], array_get($result, 'query.bool.filter.1'));
    
        $this->assertEquals([
            'geo_distance' => [
                'distance' => '10km',
                'FIELD' => [
                    'lat' => 1.0,
                    'lon' => 2.0
                ]
            ]
        ], array_get($result, 'query.bool.filter.2'));
    
        $this->assertEquals([
            'geo_polygon' => [
                'FIELD' => [
                    'points' => [
                        [
                            'lat' => 1.0,
                            'lon' => 2.0
                        ],
                        [
                            'lat' => 3.0,
                            'lon' => 4.0
                        ]
                    ]
                ]
            ]
        ], array_get($result, 'query.bool.filter.3'));
    }
    
    /**
     * @test
     */
    public function it_builds_a_nested_query()
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
    public function it_builds_a_has_child_query()
    {
        $result = $this->searchDsl->joining(function (Joining $joining) {
            $joining->hasChild('TYPE', function (Search $search) {
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
                'has_child' => [
                    'type' => 'TYPE',
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
    public function it_builds_a_has_parent_query()
    {
        $result = $this->searchDsl->joining(function (Joining $joining) {
            $joining->hasParent('TYPE', function (Search $search) {
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
                'has_parent' => [
                    'parent_type' => 'TYPE',
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
    public function it_builds_a_more_like_this_query()
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
    public function it_builds_a_boosting_query()
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
    public function it_builds_a_constant_score_query()
    {
        $result = $this->searchDsl->compound(function (Compound $compound) {
            $compound->constantScore(function (Search $search) {
                $search->termLevel(function (TermLevel $boolQuery) {
                    $boolQuery->term('FIELD', 'VALUE');
                });
            });
        })->toDsl();
        
        $this->assertEquals([
            'query' => [
                'constant_score' => [
                    'filter' => [
                        'term' => [
                            'FIELD' => 'VALUE'
                        ]
                    ]
                ]
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_a_dis_max_query()
    {
        $result = $this->searchDsl->compound(function (Compound $compound) {
            $compound->disMax([
                new TermQuery('FIELD', 'VALUE')
            ]);
        })->toDsl();
        
        $this->assertEquals([
            'query' => [
                'dis_max' => [
                    'queries' => [
                        [
                            'term' => [
                                'FIELD' => 'VALUE'
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
    public function it_builds_a_nested_inner_hit_query()
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
    
    /**
     * @test
     */
    public function it_builds_a_parent_inner_hit_query()
    {
        $result = $this->searchDsl->innerHit(function (InnerHit $innerHit) {
            $innerHit->parentInnerHit('NAME', 'PATH', function (Search $search) {
                $search->termLevel(function (TermLevel $boolQuery) {
                    $boolQuery->term('FIELD', 'VALUE');
                });
            });
        })->toDsl();
        
        $this->assertEquals([
            'inner_hits' => [
                'NAME' => [
                    'type' => [
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
