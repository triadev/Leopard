<?php
namespace Triadev\Es\ODM;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
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
    
    /**
     * Put mapping statement
     *
     * @param array $params
     * @return array
     */
    public function putMappingStatement(array $params): array
    {
        return $this->esClient->indices()->putMapping($params);
    }
    
    /**
     * Index statement
     *
     * @param array $params
     * @return array
     */
    public function indexStatement(array $params) : array
    {
        return $this->esClient->index($params);
    }
    
    /**
     * Update statement
     *
     * @param array $params
     * @return array
     */
    public function updateStatement(array $params) : array
    {
        return $this->esClient->update($params);
    }
    
    /**
     * Exist statement
     *
     * @param array $params
     * @return bool
     */
    public function existStatement(array $params) : bool
    {
        return $this->esClient->exists($params);
    }
    
    /**
     * Delete statement
     *
     * @param array $params
     * @return array
     */
    public function deleteStatement(array $params) : array
    {
        return $this->esClient->delete($params);
    }
    
    /**
     * Get statement
     *
     * @param array $params
     * @return array|null
     */
    public function getStatement(array $params) : ?array
    {
        try {
            return $this->esClient->get($params);
        } catch (Missing404Exception $e) {
            return null;
        }
    }
}
