<?php
namespace Triadev\Es\ODM\Console\Commands\Mapping;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class Make extends Command
{
    use ConfirmableTrait;
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'triadev:mapping:make';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new mapping.';
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
