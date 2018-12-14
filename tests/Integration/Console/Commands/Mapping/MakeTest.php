<?php
namespace Tests\Integration\Console\Commands\Mapping;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class MakeTest extends TestCase
{
    /**
     * @test
     */
    public function it_runs_a_mapping_update()
    {
        $filesystem = new Filesystem();
     
        $mappingPath = $this->getMappingsPath() . DIRECTORY_SEPARATOR . 'Phpunit.php';
        
        $this->assertFalse($filesystem->exists($mappingPath));
        
        $this->artisan('triadev:mapping:make', [
            'mapping' => 'phpunit',
            'model' => '\App\Test\Phpunit'
        ]);
    
        $this->assertTrue($filesystem->exists($mappingPath));
        
        $this->assertTrue(strpos(
            (string)$filesystem->get($mappingPath),
            'class Phpunit extends Mapping'
        ) !== false);
    
        $this->assertTrue(strpos(
                (string)$filesystem->get($mappingPath),
                'return \App\Test\Phpunit::class;'
            ) !== false);
   
        $filesystem->delete($mappingPath);
    
        $this->assertFalse($filesystem->exists($mappingPath));
    }
}
