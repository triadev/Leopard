<?php
namespace Triadev\Es\ODM;

use Elasticsearch\Client;
use Triadev\Es\Contract\ElasticsearchContract;
use Triadev\Es\ODM\Business\Dsl\Search;
use Triadev\Es\ODM\Contract\ElasticsearchManagerContract;

class ElasticsearchManager implements ElasticsearchManagerContract
{
    /** @var Client */
    private $esClient;
    
    /**
     * ElasticsearchManager constructor.
     * @param ElasticsearchContract $elasticsearch
     */
    public function __construct(ElasticsearchContract $elasticsearch)
    {
        $this->esClient = $elasticsearch->getClient();
    }
    
    /**
     * Get elasticsearch client
     *
     * @return Client
     */
    public function getEsClient(): Client
    {
        return $this->esClient;
    }
    
    /**
     * Search
     *
     * @return Search
     */
    public function search(): Search
    {
        return app()->make(Search::class);
    }
    
    /**
     * Search statement
     *
     * @param array $params
     * @return array
     */
    public function searchStatement(array $params) : array
    {
        return $this->esClient->search($params);
    }
}
