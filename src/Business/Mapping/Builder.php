<?php
namespace Triadev\Leopard\Business\Mapping;

class Builder
{
    /**
     * Create
     *
     * @param \Closure $blueprint
     * @param string $index
     * @param string $type
     */
    public function create(\Closure $blueprint, string $index, string $type)
    {
        $blueprintMapping = new Blueprint();
        $blueprint($blueprintMapping);
        
        $blueprintMapping->build($index, $type);
    }
}
