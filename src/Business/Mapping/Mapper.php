<?php
namespace Triadev\Es\ODM\Business\Mapping;

use Illuminate\Filesystem\Filesystem;

class Mapper
{
    /** @var Filesystem */
    private $filesystem;
    
    /**
     * Mapper constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }
    
    /**
     * Run mappings
     *
     * @param string $path
     */
    public function run(string $path)
    {
        $mappingFiles = $this->getMappingFiles($path);
        
        foreach ($mappingFiles as $mappingFile) {
            $this->filesystem->requireOnce($path . DIRECTORY_SEPARATOR . $mappingFile . '.php');
            
            /** @var Mapping $mapping */
            $mapping = new $mappingFile();
            $mapping->map();
        }
    }
    
    private function getMappingFiles(string $path): array
    {
        $files = $this->filesystem->glob($path . DIRECTORY_SEPARATOR . '*.php');
        
        return array_map(function (string $file) {
            return str_replace('.php', '', basename($file));
        }, $files);
    }
}
