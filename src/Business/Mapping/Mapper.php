<?php
namespace Triadev\Es\ODM\Business\Mapping;

use Illuminate\Filesystem\Filesystem;
use Triadev\Es\ODM\Contract\Repository\MappingLogRepositoryContract;

class Mapper
{
    /** @var Filesystem */
    private $filesystem;
    
    /** @var MappingLogRepositoryContract */
    private $mappingLogRepository;
    
    /** @var array */
    private $mappingLogs;
    
    /**
     * Mapper constructor.
     * @param Filesystem $filesystem
     * @param MappingLogRepositoryContract $mappingLogRepository
     */
    public function __construct(Filesystem $filesystem, MappingLogRepositoryContract $mappingLogRepository)
    {
        $this->filesystem = $filesystem;
        $this->mappingLogRepository = $mappingLogRepository;
    
        $this->mappingLogs = array_map(function ($log) {
            return $log['mapping'];
        }, $this->mappingLogRepository->all());
    }
    
    /**
     * Run mappings
     *
     * @param string $path
     * @param string|null $index
     * @param string|null $type
     *
     * @throws \Throwable
     */
    public function run(string $path, ?string $index = null, ?string $type = null)
    {
        $mappingFiles = $this->getMappingFiles($path);
        
        sort($mappingFiles);
        
        foreach ($mappingFiles as $mappingFile) {
            if ($this->isMappingAlreadyRun($mappingFile)) {
                continue;
            }
            
            $this->filesystem->requireOnce($path . DIRECTORY_SEPARATOR . $mappingFile . '.php');
            
            /** @var Mapping $mapping */
            $mapping = new $mappingFile();
            
            if ($index) {
                $mapping->setDocumentIndex($index);
            }
            
            if ($type) {
                $mapping->setDocumentType($type);
            }
            
            $mapping->map();
            
            $this->mappingLogRepository->add($mappingFile);
        }
    }
    
    private function getMappingFiles(string $path): array
    {
        $files = $this->filesystem->glob($path . DIRECTORY_SEPARATOR . '*.php');
        
        return array_map(function (string $file) {
            return str_replace('.php', '', basename($file));
        }, $files);
    }
    
    private function isMappingAlreadyRun(string $mapping) : bool
    {
        if (in_array($mapping, $this->mappingLogs)) {
            return true;
        }
        
        return false;
    }
}
