<?php
namespace Triadev\Es\ODM\Business\Helper;

use Illuminate\Database\Eloquent\Model;
use Triadev\Es\ODM\Searchable;

trait IsModelSearchable
{
    /**
     * Is model searchable
     *
     * @param Model $model
     *
     * @throws \InvalidArgumentException
     */
    public function isModelSearchable(Model $model)
    {
        $traits = class_uses_recursive(get_class($model));
        
        if (!isset($traits[Searchable::class])) {
            throw new \InvalidArgumentException(get_class($model).' does not use the searchable trait.');
        }
    }
}
