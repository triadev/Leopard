<?php
namespace Tests\Database\Elasticsearch;

use Illuminate\Database\Eloquent\Model;
use Tests\Database\Eloquent\TestModel;
use Triadev\Es\ODM\Business\Mapping\Blueprint;
use Triadev\Es\ODM\Business\Mapping\Builder;
use Triadev\Es\ODM\Business\Mapping\Mapping;

class TestMapping extends Mapping
{
    /**
     * Get mapped eloquent model
     *
     * @return Model
     */
    public function getMappedEloquentModel(): Model
    {
        return new TestModel();
    }
    
    /**
     * Build a mapping
     */
    public function buildMapping()
    {
        Builder::create(function (Blueprint $blueprint) {
            $blueprint->text('TEXT');
        }, $this->getDocumentIndex(), $this->getDocumentType());
    }
}
