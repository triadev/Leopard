<?php
namespace Triadev\Es\ODM\Contract;

use Elasticsearch\Client;
use Triadev\Es\ODM\Business\Dsl\Search;

interface ElasticsearchManagerContract
{
    /**
     * Get elasticsearch client
     *
     * @return Client
     */
    public function getEsClient() : Client;
    
    /**
     * Search
     *
     * @return Search
     */
    public function search() : Search;
    
    /**
     * Search statement
     *
     * @param array $params
     * @return array
     */
    public function searchStatement(array $params) : array;
}
