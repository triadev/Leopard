<?php

use Tests\Integration\Model\Entity\TestModel;
use Triadev\Leopard\Business\Mapping\Mapping;
use Triadev\Es\Mapping\Facade\ElasticMapping;
use Triadev\Es\Mapping\Mapping\Blueprint;

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
        ElasticMapping::map(function (Blueprint $blueprint) {
            $blueprint->integer('id');
            $blueprint->text('name');
            $blueprint->keyword('email');
    
            $blueprint->settings([
                'index' => [
                    'number_of_replicas' => 10,
                    'number_of_shards' => 12,
                    'refresh_interval' => '30s'
                ]
            ]);
        }, $this->getDocumentIndex(), $this->getDocumentType());
    }
}
