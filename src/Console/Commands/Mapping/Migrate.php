<?php
namespace Triadev\Leopard\Console\Commands\Mapping;

use Illuminate\Console\ConfirmableTrait;
use Triadev\Leopard\Business\Mapping\Mapper;

class Migrate extends BaseCommand
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
     * @throws \Throwable
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
}
