<?php
/**
 * ProductAttributeType Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class ProductAttributeType extends BaseEntityAbstract
{
    /**
     * The attribute of the product
     * 
     * @var string
     */
    private $name;
    /**
     * Whether this attribute is searchable
     * 
     * @var bool
     */
    private $searchable = true;
    /**
     * The unique code for this type
     * 
     * @var string
     */
    private $code;
    /**
     * Getter for the name of the type
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Setter for the name fo the type
     * 
     * @param string $name The name
     * 
     * @return ProductAttributeType
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * Getter for the searchable of the type
     * 
     * @return string
     */
    public function getSearchable()
    {
        return $this->searchable;
    }
    /**
     * Setter for the searchable fo the type
     * 
     * @param bool $searchable The searchable of the type
     * 
     * @return ProductAttributeType
     */
    public function setSearchable($searchable)
    {
        $this->searchable = $searchable;
        return $this;
    }
    /**
     * Getter for the code
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * Setter for the code
     * 
     * @param string $code The code of the type
     * 
     * @return ProductAttributeType
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::__toString()
     */
    public function __toString()
    {
        return $this->name;
    }
    /**
    * (non-PHPdoc)
    * @see BaseEntity::__loadDaoMap()
    */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'pat');
        DaoMap::setStringType('name','varchar', 50);
        DaoMap::setStringType('code','varchar', 50);
        DaoMap::setBoolType('searchable');
        parent::__loadDaoMap();
        DaoMap::createIndex('name');
        DaoMap::createUniqueIndex('code');
        DaoMap::createIndex('searchable');
        DaoMap::commit();
    }
}