<?php
namespace Triadev\Es\ODM\Business\Mapping;

class Builder
{
    /**
     * Create
     *
     * @param \Closure $blueprint
     * @param string $index
     * @param string $type
     */
    public static function create(\Closure $blueprint, string $index, string $type)
    {
        $blueprintMapping = new Blueprint();
        $blueprint($blueprintMapping);
        
        $blueprintMapping->build($index, $type);
    }
}
