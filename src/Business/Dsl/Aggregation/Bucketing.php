<?php
namespace Triadev\Es\ODM\Business\Dsl\Aggregation;

use ONGR\ElasticsearchDSL\Aggregation\Bucketing\ChildrenAggregation;
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
