<?php
namespace Triadev\Es\ODM\Facade;

use Elasticsearch\Client;
use Illuminate\Support\Facades\Facade;
use Triadev\Es\ODM\Contract\ElasticsearchManagerContract;
use Triadev\Es\ODM\Business\Dsl\Search;

/**
 * Class EsManager
 * @package Triadev\Es\ODM\Facade
 *
 * @method static Client getEsClient()
 * @method static Search search()
 * @method static array searchStatement(array $params)
 * @method static array putMappingStatement(array $params)
 * @method static array indexStatement(array $params)
 * @method static bool existStatement(array $params)
 * @method static array deleteStatement(array $params)
 */
class EsManager extends Facade
{
    /**
     * Get a plastic manager instance for the default connection.
     *
     * @return ElasticsearchManagerContract
     */
    protected static function getFacadeAccessor()
    {
        return app(ElasticsearchManagerContract::class);
    }
}
