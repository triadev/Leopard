<?php

use Illuminate\Database\Eloquent\Model;
use Tests\Integration\Model\Entity\TestModel;
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
     * Map
     */
    public function map()
    {
        Builder::create(function (Blueprint $blueprint) {
            $blueprint->integer('id');
            $blueprint->text('name');
            $blueprint->keyword('email');
        }, $this->getDocumentIndex(), $this->getDocumentType());
    }
}
