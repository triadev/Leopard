<?php
namespace Triadev\Leopard\Contract;

use Illuminate\Database\Eloquent\Model;
use Triadev\Es\Dsl\Model\SearchResult;

interface FillerContract
{
    /**
     * Fill
     *
     * @param Model $model
     * @param SearchResult $searchResult
     */
    public function fill(Model $model, SearchResult $searchResult);
}
