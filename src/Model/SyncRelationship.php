<?php
namespace Triadev\Leopard\Model;

class SyncRelationship
{
    /** @var string */
    private $relatedClass;
    
    /** @var string */
    private $foreignKey;
    
    /** @var string|null */
    private $ownerKey;
    
    /** @var string|null */
    private $relation;
    
    /**
     * SyncRelationship constructor.
     * @param string $relatedClass
     */
    public function __construct(string $relatedClass)
    {
        $this->relatedClass = $relatedClass;
    }
    
    /**
     * @param string $relatedClass
     * @return SyncRelationship
     */
    public static function create(string $relatedClass) : SyncRelationship
    {
        $class = get_called_class();
        return new $class($relatedClass);
    }
    
    /**
     * @return string
     */
    public function getRelatedClass(): string
    {
        return $this->relatedClass;
    }
    
    /**
     * Foreign key
     *
     * @param string $foreignKey
     * @return SyncRelationship
     */
    public function foreignKey(string $foreignKey) : SyncRelationship
    {
        $this->foreignKey = $foreignKey;
        return $this;
    }
    
    /**
     * Get foreign key
     *
     * @return string
     */
    public function getForeignKey() : ?string
    {
        return $this->foreignKey ?: (new $this->relatedClass())->getForeignKey();
    }
    
    /**
     * Owner key
     *
     * @param string $ownerKey
     * @return SyncRelationship
     */
    public function ownerKey(string $ownerKey) : SyncRelationship
    {
        $this->ownerKey = $ownerKey;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getOwnerKey(): ?string
    {
        return $this->ownerKey;
    }
    
    /**
     * Relation
     *
     * @param string $relation
     * @return SyncRelationship
     */
    public function relation(string $relation) : SyncRelationship
    {
        $this->relation = $relation;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getRelation(): ?string
    {
        return $this->relation;
    }
}
