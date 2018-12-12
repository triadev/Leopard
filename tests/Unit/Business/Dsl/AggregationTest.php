<?php
namespace Tests\Unit\Business\Dsl;

use ONGR\ElasticsearchDSL\Search;
use Tests\TestCase;
use Triadev\Es\ODM\Business\Dsl\Aggregation;

class AggregationTest extends TestCase
{
    /** @var Search */
    private $search;
    
    /** @var Aggregation */
    private $aggregation;
    
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->search = new Search();
        $this->aggregation = new Aggregation($this->search);
    }
    
    /**
     * @test
     */
    public function it_builds_a_bucketing_children_aggregation()
    {
        $this->aggregation->bucketing(function (Aggregation\Bucketing $bucketing) {
            $bucketing->children('CHILDREN', 'TYPE', function (Aggregation\Bucketing $bucketing) {
                $bucketing->terms('NAME', 'FIELD');
            });
        });
        
        $this->assertEquals([
            'aggregations' => [
                'CHILDREN' => [
                    'children' => [
                        'type' => 'TYPE'
                    ],
                    'aggregations' => [
                        'NAME' => [
                            'terms' => [
                                'field' => 'FIELD'
                            ]
                        ]
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_bucketing_date_histogram_aggregation()
    {
        $this->aggregation->bucketing(function (Aggregation\Bucketing $bucketing) {
            $bucketing->dateHistogram(
                'NAME',
                'FIELD',
                'month'
            );
        });
    
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'date_histogram' => [
                        'field' => 'FIELD',
                        'interval' => 'month'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_bucketing_date_range_aggregation()
    {
        $this->aggregation->bucketing(function (Aggregation\Bucketing $bucketing) {
            $bucketing->dateRange('NAME', 'FIELD', 'MM-yyyy', [
                [
                    'from' => 'now-10M/M',
                    'to' => 'now-10M/M'
                ]
            ]);
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'date_range' => [
                        'format' => 'MM-yyyy',
                        'field' => 'FIELD',
                        'ranges' => [
                            [
                                'from' => 'now-10M/M',
                                'to' => 'now-10M/M'
                            ]
                        ]
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_bucketing_terms_aggregation()
    {
        $this->aggregation->bucketing(function (Aggregation\Bucketing $bucketing) {
            $bucketing->terms('NAME', 'FIELD');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'terms' => [
                        'field' => 'FIELD'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
}
