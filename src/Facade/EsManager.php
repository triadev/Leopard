<?php
namespace Triadev\Es\ODM\Facade;

use Elasticsearch\Client;
use Illuminate\Support\Facades\Facade;
use Triadev\Es\ODM\Contract\ElasticsearchManagerContract;
use Triadev\Es\ODM\Business\Dsl\Search;
use Triadev\Es\ODM\Business\Dsl\Suggestion;

/**
 * Class EsManager
 * @package Triadev\Es\ODM\Facade
 *
 * @method static Client getEsClient()
 * @method static Search search()
 * @method static Suggestion suggest()
 * @method static array searchStatement(array $params)
 * @method static array putMappingStatement(array $params)
 * @method static array putSettingStatement(array $params)
 * @method static array indexStatement(array $params)
 * @method static array updateStatement(array $params)
 * @method static bool existStatement(array $params)
 * @method static array deleteStatement(array $params)
 * @method static array getStatement(array $params)
 * @method static array suggestStatement(array $params)
 * @method static array bulkStatement(array $params)
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
