<?php
namespace Triadev\Es\ODM\Contract;

use Triadev\Es\ODM\Business\Dsl\Search;

interface ElasticsearchDslContract
{
    /**
     * Search
     *
     * @return Search
     */
    public function search() : Search;
}
