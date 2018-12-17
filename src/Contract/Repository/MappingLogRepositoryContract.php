<?php
namespace Triadev\Leopard\Contract\Repository;

use Triadev\Leopard\Model\Entity\MappingLog;

interface MappingLogRepositoryContract
{
    /**
     * Add mapping log entry
     *
     * @param string $mapping
     *
     * @throws \Throwable
     */
    public function add(string $mapping);
    
    /**
     * Find mapping log entry
     *
     * @param int $id
     * @return MappingLog|null
     */
    public function find(int $id) : ?MappingLog;
    
    /**
     * Delete mapping log entry
     *
     * @param int $id
     *
     * @throws \Throwable
     */
    public function delete(int $id);
    
    /**
     * Get all mapping log entries
     *
     * @return array [
     *      [
     *          'id' => INTEGER,
     *          'mapping' => STRING
     *      ],
     *      ...
     * ]
     */
    public function all() : array;
    
    /**
     * Reset mapping log table
     */
    public function reset();
}
