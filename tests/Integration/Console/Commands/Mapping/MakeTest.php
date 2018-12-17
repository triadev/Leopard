<?php
namespace Tests\Integration\Console\Commands\Mapping;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class MakeTest extends TestCase
{
    /** @var Filesystem */
    private $filesystem;
    
    /** @var string */
    private $mappingPath;
    
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->filesystem = new Filesystem();
        
        $this->mappingPath = $this->getMappingsPath() . DIRECTORY_SEPARATOR . 'Phpunit.php';
    
        $this->assertFalse(
            $this->filesystem->exists(
                $this->mappingPath
            )
        );
    }
    
    /**
     * @inheritDoc
     *
     * (Increase visibility to public)
     *
     * @return void
     */
    public function tearDown()
    {
        $this->filesystem->delete($this->mappingPath);
    
        $this->assertFalse(
            $this->filesystem->exists(
                $this->mappingPath
            )
        );
        
        parent::tearDown();
    }
    
    /**
     * @test
     */
    public function it_creates_a_new_mapping_file_via_artisan_command()
    {
        $this->artisan('triadev:mapping:make', [
            'mapping' => 'phpunit'
        ]);
        
        $mapping = $this->getMapping();
    
        $this->assertStringExist($mapping, 'class Phpunit extends Mapping');
        $this->assertStringExist($mapping, 'return DefaultModel::class;');
    }
    
    /**
     * @test
     */
    public function it_creates_a_new_mapping_file_via_artisan_command_with_individual_optional_params()
    {
        $this->artisan('triadev:mapping:make', [
            'mapping' => 'phpunit',
            '--model' => '\App\Test\Phpunit'
        ]);
    
        $mapping = $this->getMapping();
        
        $this->assertStringExist($mapping, 'class Phpunit extends Mapping');
        $this->assertStringExist($mapping, 'return \App\Test\Phpunit::class;');
    }
    
    private function getMapping() : string
    {
        return (string)$this->filesystem->get($this->mappingPath);
    }
    
    private function assertStringExist(string $hasystack, string $needle)
    {
        $this->assertTrue(strpos($hasystack, $needle) !== false);
    }
}
