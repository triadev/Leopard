<?php
namespace Triadev\Es\ODM\Business\Dsl\Aggregation;

use ONGR\ElasticsearchDSL\Aggregation\Bucketing\ChildrenAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\DateHistogramAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\DateRangeAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\DiversifiedSamplerAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\FilterAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;

class Bucketing extends Aggs
{
    /**
     * Children
     *
     * @param string $name
     * @param \Closure $bucketing
     * @param string|null $children
     * @return Bucketing
     */
    public function children(string $name, \Closure $bucketing, ?string $children = null) : Bucketing
    {
        $resultAgg = new ChildrenAggregation($name, $children);
        
        $bucketingBuilder = new self();
        $bucketing($bucketingBuilder);
        
        foreach ($bucketingBuilder->getAggregations() as $agg) {
            $resultAgg->addAggregation($agg);
        }
        
        $this->addAggregation($resultAgg);
        return $this;
    }
    
    /**
     * Date histogram
     *
     * @param string $name
     * @param string|null $field
     * @param string|null $interval
     * @param string|null $format
     * @return Bucketing
     */
    public function dateHistogram(string $name, ?string $field = null, ?string $interval = null, ?string $format = null) : Bucketing
    {
        $this->addAggregation(new DateHistogramAggregation($name, $field, $interval, $format));
        return $this;
    }
    
    /**
     * Date range
     *
     * @param string $name
     * @param string|null $field
     * @param string|null $format
     * @param array $ranges
     * @return Bucketing
     */
    public function dateRange(string $name, ?string $field = null, ?string $format = null, array $ranges = []) : Bucketing
    {
        $this->addAggregation(new DateRangeAggregation($name, $field, $format, $ranges));
        return $this;
    }
    
    /**
     * Diversified sampler
     *
     * @param string $name
     * @param string|null $field
     * @param int|null $shardSize
     * @return Bucketing
     */
    public function diversifiedSampler(string $name, ?string $field = null, ?int $shardSize = null) : Bucketing
    {
        $this->addAggregation(new DiversifiedSamplerAggregation($name, $field, $shardSize));
        return $this;
    }
    
    public function filter() : Bucketing
    {
        return $this;
    }
    
    public function filters() : Bucketing
    {
        return $this;
    }
    
    public function geoDistance() : Bucketing
    {
        return $this;
    }
    
    public function geoHashGrid() : Bucketing
    {
        return $this;
    }
    
    public function histogram() : Bucketing
    {
        return $this;
    }
    
    public function ipv4Range() : Bucketing
    {
        return $this;
    }
    
    public function missing() : Bucketing
    {
        return $this;
    }
    
    public function nested() : Bucketing
    {
        return $this;
    }
    
    public function range() : Bucketing
    {
        return $this;
    }
    
    public function reverseNested() : Bucketing
    {
        return $this;
    }
    
    public function samplerAgg() : Bucketing
    {
        return $this;
    }
    
    public function significantTerms() : Bucketing
    {
        return $this;
    }
    
    /**
     * Terms
     *
     * @param string $name
     * @param string|null $field
     * @param string|null $script
     * @return Bucketing
     */
    public function terms(string $name, ?string $field = null, ?string $script = null) : Bucketing
    {
        $this->addAggregation(new TermsAggregation($name, $field, $script));
        return $this;
    }
}
