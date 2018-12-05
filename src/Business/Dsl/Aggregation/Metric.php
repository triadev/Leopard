<?php
namespace Triadev\Es\ODM\Business\Dsl\Aggregation;

use ONGR\ElasticsearchDSL\Aggregation\Metric\AvgAggregation;

class Metric extends Aggs
{
    /**
     * Avg
     *
     * @param string $name
     * @param string|null $field
     * @param string|null $script
     * @return Metric
     */
    public function avg(string $name, ?string $field = null, ?string $script = null) : Metric
    {
        $this->addAggregation(new AvgAggregation($name, $field, $script));
        return $this;
    }
}
