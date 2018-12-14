<?php
namespace Triadev\Es\ODM\Console\Commands\Mapping;

use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class Make extends BaseCommand
{
    use ConfirmableTrait;
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'triadev:mapping:make {mapping}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new mapping';
    
    /** @var Filesystem */
    private $filesystem;
    
    /** @var Composer */
    private $composer;
    
    /**
     * Make constructor.
     * @param Filesystem $filesystem
     * @param Composer $composer
     */
    public function __construct(Filesystem $filesystem, Composer $composer)
    {
        parent::__construct();
        
        $this->filesystem = $filesystem;
        $this->composer = $composer;
    }
    
    /**
     * Execute the console command.
     *
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $mapping = Str::studly($this->argument('mapping'));
        
        $this->filesystem->put(
            $this->buildPath($mapping),
            $this->buildMapping(
                $this->filesystem->get(
                    __DIR__ . DIRECTORY_SEPARATOR . 'default.stub'
                ),
                $mapping
            )
        );
        
        $this->composer->dumpAutoloads();
    }
    
    private function buildPath(string $mapping) : string
    {
        return $this->getMappingPath() . DIRECTORY_SEPARATOR . $mapping . '.php';
    }
    
    private function buildMapping(string $stub, string $mapping) : string
    {
        $stub = str_replace('DefaultClass', $mapping, $stub);
        
        return $stub;
    }
}
