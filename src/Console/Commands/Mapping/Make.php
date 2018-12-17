<?php
namespace Triadev\Es\ODM\Console\Commands\Mapping;

use Illuminate\Console\ConfirmableTrait;
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
    protected $signature = 'triadev:mapping:make {mapping} {--model=}';
    
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
     */
    public function handle()
    {
        $mapping = $this->formatMappingName($this->argument('mapping'));
        
        $model = $this->option('model') ?
            $this->formatModelName($this->option('model')) :
            null;
        
        $this->filesystem->put(
            $this->buildMappingFilePath($mapping),
            $this->buildMapping(
                $this->getDefaultStub(),
                $mapping,
                $model
            )
        );
        
        $this->composer->dumpAutoloads();
    }
    
    private function formatMappingName(string $mapping) : string
    {
        return trim(Str::studly($mapping));
    }
    
    private function formatModelName(string $model) : string
    {
        return trim($model);
    }
    
    private function getDefaultStub() : string
    {
        return $this->filesystem->get(__DIR__ . DIRECTORY_SEPARATOR . 'default.stub');
    }
    
    private function buildMappingFilePath(string $mapping) : string
    {
        return $this->getMappingPath() . DIRECTORY_SEPARATOR . $mapping . '.php';
    }
    
    private function buildMapping(string $stub, string $mapping, ?string $model) : string
    {
        $stub = str_replace('DefaultClass', $mapping, $stub);
        
        if ($model) {
            $stub = str_replace('DefaultModel', $model, $stub);
        }
        
        return $stub;
    }
}
