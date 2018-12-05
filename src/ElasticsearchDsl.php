<?php
namespace Triadev\Es\ODM;

use Triadev\Es\ODM\Business\Dsl\Search;
use Triadev\Es\ODM\Contract\ElasticsearchDslContract;

class ElasticsearchDsl implements ElasticsearchDslContract
{
    /**
     * @inheritdoc
     */
    public function search() : Search
    {
        return new Search();
    }
}
