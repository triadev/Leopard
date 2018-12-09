<?php
namespace Tests\Unit\Business\Dsl;

use Tests\TestCase;
use Triadev\Es\ODM\Business\Dsl\Search;
use Triadev\Es\ODM\Model\Location;

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
        
        $this->searchDsl = new Search(new \ONGR\ElasticsearchDSL\Search());
    }
    
    /**
     * @test
     */
    public function it_builds_a_match_all_query()
    {
        $result = $this->searchDsl
            ->must()
                ->matchAll()
            ->toDsl();
        
        $this->assertEquals([
            'query' => [
                'match_all' => new \stdClass()
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_a_bool_terms_query()
    {
        $result = $this->searchDsl
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
                ->wildcard('FIELD', 'VALUE')
            ->toDsl();
        
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
        $result = $this->searchDsl
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
                ->commonTerms('FIELD', 'QUERY')
            ->toDsl();
        
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
        $result = $this->searchDsl
            ->filter()
                ->geoBoundingBox('FIELD', [
                    new Location(1, 2),
                    new Location(3, 4)
                ])
                ->geoDistance('FIELD', '10km', new Location(1, 2))
                ->geoPolygon('FIELD', [
                    new Location(1, 2),
                    new Location(3, 4)
                ])
            ->toDsl();
        
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
        ], array_get($result, 'query.bool.filter.0'));
    
        $this->assertEquals([
            'geo_distance' => [
                'distance' => '10km',
                'FIELD' => [
                    'lat' => 1.0,
                    'lon' => 2.0
                ]
            ]
        ], array_get($result, 'query.bool.filter.1'));
    
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
        ], array_get($result, 'query.bool.filter.2'));
    }
    
    /**
     * @test
     */
    public function it_builds_a_nested_query()
    {
        $result = $this->searchDsl
            ->nested('PATH', function (Search $search) {
                $search
                    ->filter()
                        ->term('FIELD1', 'VALUE1')
                        ->term('FIELD2', 'VALUE2');
            })
            ->toDsl();
        
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
        $result = $this->searchDsl
            ->hasChild('TYPE', function (Search $search) {
                $search
                    ->filter()
                    ->term('FIELD1', 'VALUE1')
                    ->term('FIELD2', 'VALUE2');
            })
            ->toDsl();
        
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
        $result = $this->searchDsl
            ->hasParent('TYPE', function (Search $search) {
                $search
                    ->filter()
                    ->term('FIELD1', 'VALUE1')
                    ->term('FIELD2', 'VALUE2');
            })
            ->toDsl();
        
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
        $result = $this->searchDsl
            ->moreLikeThis('LIKE')
            ->toDsl();
        
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
            ->term('FIELD', 'VALUE')
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
            ->term('FIELD', 'VALUE')
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
}
