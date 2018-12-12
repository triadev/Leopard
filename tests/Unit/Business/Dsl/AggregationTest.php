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
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_avg_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->avg('NAME', 'BUCKETS_PATH');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'avg_bucket' => [
                        'buckets_path' => 'BUCKETS_PATH'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_max_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->max('NAME', 'BUCKETS_PATH');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'max_bucket' => [
                        'buckets_path' => 'BUCKETS_PATH'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_min_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->min('NAME', 'BUCKETS_PATH');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'min_bucket' => [
                        'buckets_path' => 'BUCKETS_PATH'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_percentiles_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->percentiles('NAME', 'BUCKETS_PATH');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'percentiles_bucket' => [
                        'buckets_path' => 'BUCKETS_PATH'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_stats_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->stats('NAME', 'BUCKETS_PATH');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'stats_bucket' => [
                        'buckets_path' => 'BUCKETS_PATH'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_sum_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->sum('NAME', 'BUCKETS_PATH');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'sum_bucket' => [
                        'buckets_path' => 'BUCKETS_PATH'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_serial_differencing_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->serialDifferencing('NAME', 'BUCKETS_PATH');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'serial_diff' => [
                        'buckets_path' => 'BUCKETS_PATH'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_extended_stats_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->extendedStats('NAME', 'BUCKETS_PATH');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'extended_stats_bucket' => [
                        'buckets_path' => 'BUCKETS_PATH'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_derivative_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->derivative('NAME', 'BUCKETS_PATH');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'derivative' => [
                        'buckets_path' => 'BUCKETS_PATH'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_cumulative_sum_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->cumulativeSum('NAME', 'BUCKETS_PATH');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'cumulative_sum' => [
                        'buckets_path' => 'BUCKETS_PATH'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_bucket_selector_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->bucketSelector('NAME', 'BUCKETS_PATH', 'SCRIPT');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'bucket_selector' => [
                        'buckets_path' => 'BUCKETS_PATH',
                        'script' => 'SCRIPT'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
    
    /**
     * @test
     */
    public function it_builds_a_pipeline_bucket_script_aggregation()
    {
        $this->aggregation->pipeline(function (Aggregation\Pipeline $pipeline) {
            $pipeline->bucketScript('NAME', 'BUCKETS_PATH', 'SCRIPT');
        });
        
        $this->assertEquals([
            'aggregations' => [
                'NAME' => [
                    'bucket_script' => [
                        'buckets_path' => 'BUCKETS_PATH',
                        'script' => 'SCRIPT'
                    ]
                ]
            ]
        ], $this->search->toArray());
    }
}
