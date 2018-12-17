<?php
namespace Triadev\Leopard\Business\Dsl\Query;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoostingQuery;
use ONGR\ElasticsearchDSL\Query\Compound\ConstantScoreQuery;
use ONGR\ElasticsearchDSL\Query\Compound\DisMaxQuery;
use Triadev\Leopard\Business\Dsl\FunctionScore;
use Triadev\Leopard\Business\Dsl\Search;
use Triadev\Leopard\Business\Dsl\AbstractQuery;

class Compound extends AbstractQuery
{
    /**
     * Function score
     *
     * @param \Closure $search
     * @param \Closure $functionScore
     * @param array $params
     * @return Compound
     */
    public function functionScore(\Closure $search, \Closure $functionScore, array $params = []) : Compound
    {
        $searchBuilder = app()->make(Search::class);
        $search($searchBuilder);
        
        $functionScoreBuilder = new FunctionScore($searchBuilder->getQuery(), $params);
        $functionScore($functionScoreBuilder);
        
        $this->append($functionScoreBuilder->getQuery());
        return $this;
    }
    
    /**
     * Constant score
     *
     * @param \Closure $search
     * @param array $params
     * @return Compound
     */
    public function constantScore(\Closure $search, array $params = []) : Compound
    {
        $searchBuilder = app()->make(Search::class);
        $search($searchBuilder);
        
        $this->append(new ConstantScoreQuery($searchBuilder->getQuery(), $params));
        return $this;
    }
    
    /**
     * Boosting
     *
     * @param BuilderInterface $positive
     * @param BuilderInterface $negative
     * @param float $negativeBoost
     * @return Compound
     */
    public function boosting(
        BuilderInterface $positive,
        BuilderInterface $negative,
        float $negativeBoost
    ) : Compound {
        $this->append(new BoostingQuery($positive, $negative, $negativeBoost));
        return $this;
    }
    
    /**
     * Dis max
     *
     * @param BuilderInterface[] $queries
     * @param array $params
     * @return Compound
     */
    public function disMax(array $queries, array $params = []) : Compound
    {
        $disMaxQuery = new DisMaxQuery($params);
        
        foreach ($queries as $query) {
            if ($query instanceof BuilderInterface) {
                $disMaxQuery->addQuery($query);
            }
        }
        
        $this->append($disMaxQuery);
        return $this;
    }
}
