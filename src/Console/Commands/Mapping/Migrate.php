<?php
namespace Triadev\Es\ODM\Console\Commands\Mapping;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Triadev\Es\ODM\Business\Mapping\Mapper;

class Migrate extends Command
{
    use ConfirmableTrait;
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'triadev:mapping:migrate {--index=} {--type=}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the mapping migrations.';
    
    /**
     * Execute the console command.
     *
     * @param Mapper $mapper
     */
    public function handle(Mapper $mapper)
    {
        if (!$this->confirmToProceed()) {
            return;
        }
        
        $mapper->run(
            $this->getMappingPath(),
            $this->option('index'),
            $this->option('type')
        );
    }
    
    private function getMappingPath() : string
    {
        return app()->databasePath() . DIRECTORY_SEPARATOR . 'mappings';
    }
}
