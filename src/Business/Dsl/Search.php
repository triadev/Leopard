<?php
namespace Triadev\Leopard\Business\Dsl;

use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Triadev\Leopard\Busines\Dsl\Query\Specialized;
use Triadev\Leopard\Business\Dsl\Query\Compound;
use Triadev\Leopard\Business\Dsl\Query\Fulltext;
use Triadev\Leopard\Business\Dsl\Query\Geo;
use Triadev\Leopard\Business\Dsl\Query\InnerHit;
use Triadev\Leopard\Business\Dsl\Query\Joining;
use Triadev\Leopard\Business\Dsl\Query\TermLevel;

class Search extends AbstractQuery
{
    /**
     * Aggregation
     *
     * @param \Closure $aggregation
     * @return Search
     */
    public function aggregation(\Closure $aggregation) : Search
    {
        $aggregation(new Aggregation($this->search));
        return $this;
    }
    
    /**
     * Term level
     *
     * @return TermLevel
     */
    public function termLevel() : TermLevel
    {
        return new TermLevel($this->search, $this->model);
    }
    
    /**
     * Fulltext
     *
     * @return Fulltext
     */
    public function fulltext() : Fulltext
    {
        return new Fulltext($this->search, $this->model);
    }
    
    /**
     * Geo
     *
     * @return Geo
     */
    public function geo() : Geo
    {
        return new Geo($this->search, $this->model);
    }
    
    /**
     * Compound
     *
     * @return Compound
     */
    public function compound() : Compound
    {
        return new Compound($this->search, $this->model);
    }
    
    /**
     * Joining
     *
     * @return Joining
     */
    public function joining() : Joining
    {
        return new Joining($this->search, $this->model);
    }
    
    /**
     * Specialized
     *
     * @return Specialized
     */
    public function specialized() : Specialized
    {
        return new Specialized($this->search, $this->model);
    }
    
    /**
     * Inner hit
     *
     * @return InnerHit
     */
    public function innerHit() : InnerHit
    {
        return new InnerHit($this->search, $this->model);
    }
    
    /**
     * Paginate
     *
     * @param int $page
     * @param int $limit
     * @return Search
     */
    public function paginate(int $page, int $limit = 25) : Search
    {
        $this->search->setFrom($limit * ($page - 1))->setSize($limit);
        return $this;
    }
    
    /**
     * Min score
     *
     * @param int $minScore
     * @return Search
     */
    public function minScore(int $minScore) : Search
    {
        $this->search->setMinScore($minScore);
        return $this;
    }
    
    /**
     * Sort
     *
     * @param string $field
     * @param string $order
     * @param array $params
     * @return Search
     */
    public function sort(string $field, string $order = FieldSort::DESC, array $params = []) : Search
    {
        $this->search->addSort(new FieldSort(
            $field,
            $order,
            $params
        ));
        
        return $this;
    }
}
