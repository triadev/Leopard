<?php

use Tests\Integration\Model\Entity\TestModel;
use Triadev\Es\ODM\Business\Mapping\Blueprint;
use Triadev\Es\ODM\Business\Mapping\Mapping;
use Triadev\Es\ODM\Facade\EsManager;

class TestMapping extends Mapping
{
    /**
     * Get mapped eloquent model class
     *
     * @return string
     */
    public function model(): string
    {
        return TestModel::class;
    }
    
    /**
     * Map
     */
    public function map()
    {
        EsManager::map(function (Blueprint $blueprint) {
            $blueprint->integer('id');
            $blueprint->text('name');
            $blueprint->keyword('email');
        }, $this->getDocumentIndex(), $this->getDocumentType());
    }
}
