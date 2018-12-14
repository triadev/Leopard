<?php
namespace Triadev\Es\ODM\Console\Commands\Mapping;

use Illuminate\Console\Command;

abstract class BaseCommand extends Command
{
    /**
     * Get mapping path
     *
     * @return string
     */
    public function getMappingPath() : string
    {
        return app()->databasePath() . DIRECTORY_SEPARATOR . 'mappings';
    }
}
