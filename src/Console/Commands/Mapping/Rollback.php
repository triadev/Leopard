<?php
namespace Triadev\Es\ODM\Console\Commands\Mapping;

use Illuminate\Console\ConfirmableTrait;
use Triadev\Es\ODM\Contract\Repository\MappingLogRepositoryContract;

class Rollback extends BaseCommand
{
    use ConfirmableTrait;
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'triadev:mapping:rollback {--steps=}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last migration steps.';
    
    /** @var MappingLogRepositoryContract */
    private $mappingLogRepository;
    
    /**
     * Rollback constructor.
     * @param MappingLogRepositoryContract $mappingLogRepository
     */
    public function __construct(MappingLogRepositoryContract $mappingLogRepository)
    {
        parent::__construct();
        
        $this->mappingLogRepository = $mappingLogRepository;
    }
    
    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }
        
        $steps = $this->option('steps') ?: $this->mappingLogRepository->reset();
        if (is_int($steps)) {
            $this->deleteLogsBySteps($steps);
        }
    }
    
    /**
     * @param int $steps
     * @throws \Throwable
     */
    private function deleteLogsBySteps(int $steps)
    {
        $logs = $this->mappingLogRepository->all();
    
        if ($steps >= count($logs)) {
            $this->mappingLogRepository->reset();
        }
    
        $logs = array_slice(
            array_reverse($logs),
            0,
            $steps
        );
    
        foreach ($logs as $log) {
            $this->mappingLogRepository->delete($log['id']);
        }
    }
}
