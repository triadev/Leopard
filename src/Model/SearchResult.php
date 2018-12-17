<?php
namespace Triadev\Leopard\Model;

use Illuminate\Support\Collection;

class SearchResult
{
    /** @var int */
    private $_took;
    
    /** @var bool */
    private $_timedOut;
    
    /** @var array */
    private $_shards;
    
    /** @var Collection */
    private $_hits;
    
    /** @var int */
    private $_totalHits;
    
    /** @var float */
    private $_maxScore;
    
    /** @var array|null */
    private $_aggregation;
    
    /**
     * SearchResult constructor.
     * @param array $result
     */
    public function __construct(array $result)
    {
        $this->_took = (int)array_get($result, 'took');
        $this->_timedOut = (bool)array_get($result, 'timed_out');
        $this->_shards = (array)array_get($result, '_shards');
        $this->_hits = new Collection(array_get($result, 'hits.hits'));
        $this->_totalHits = (int)array_get($result, 'hits.total');
        $this->_maxScore = (float)array_get($result, 'hits.max_score');
        $this->_aggregation = array_get($result, 'aggregations', null);
    }
    
    /**
     * @return int
     */
    public function getTook(): int
    {
        return $this->_took;
    }
    
    /**
     * @return bool
     */
    public function isTimedOut(): bool
    {
        return $this->_timedOut;
    }
    
    /**
     * @return array
     */
    public function getShards(): array
    {
        return $this->_shards;
    }
    
    /**
     * @return Collection
     */
    public function getHits(): Collection
    {
        return $this->_hits;
    }
    
    /**
     * @param Collection $hits
     */
    public function setHits(Collection $hits)
    {
        $this->_hits = $hits;
    }
    
    /**
     * @return int
     */
    public function getTotalHits(): int
    {
        return $this->_totalHits;
    }
    
    /**
     * @return float
     */
    public function getMaxScore(): float
    {
        return $this->_maxScore;
    }
    
    /**
     * @return array|null
     */
    public function getAggregation(): ?array
    {
        return $this->_aggregation;
    }
}
