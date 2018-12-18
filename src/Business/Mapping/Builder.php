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
     * @param bool $createIndex
     */
    public function create(\Closure $blueprint, string $index, string $type, bool $createIndex = false)
    {
        $blueprintMapping = new Blueprint();
        $blueprint($blueprintMapping);
        
        $blueprintMapping->build($index, $type, $createIndex);
    }
}
