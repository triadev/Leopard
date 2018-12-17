<?php

use Tests\Integration\Model\Entity\TestModel;
use Triadev\Leopard\Business\Mapping\Blueprint;
use Triadev\Leopard\Business\Mapping\Mapping;
use Triadev\Leopard\Facade\Leopard;

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
        Leopard::map(function (Blueprint $blueprint) {
            $blueprint->integer('id');
            $blueprint->text('name');
            $blueprint->keyword('email');
        }, $this->getDocumentIndex(), $this->getDocumentType());
    }
}
