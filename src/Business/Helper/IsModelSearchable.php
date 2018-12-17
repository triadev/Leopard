<?php
namespace Triadev\Leopard\Business\Helper;

use Illuminate\Database\Eloquent\Model;
use Triadev\Leopard\Searchable;

trait IsModelSearchable
{
    /**
     * Is model searchable
     *
     * @param Model $model
     * @return true
     *
     * @throws \InvalidArgumentException
     */
    public function isModelSearchable(Model $model) : bool
    {
        $traits = class_uses_recursive(get_class($model));
        
        if (!isset($traits[Searchable::class])) {
            throw new \InvalidArgumentException(get_class($model).' does not use the searchable trait.');
        }
        
        return true;
    }
}
