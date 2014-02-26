<?php
/**
 * ProductType Entity which will hold the type: BOOK, MAGZINE or NEWSPAPER
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class ProductType extends BaseEntityAbstract
{
	const ID_BOOK = 1;
	const ID_NEWSPAPER = 2;
	const ID_MAGAZINE = 3;
    /**
     * The name of the language
     * 
     * @var string
     */
    private $name;
    /**
     * Getters for the name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Setters for the name
     * 
     * @param string $name The name of the ProductType
     * 
     * @return ProductType
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'lan');
        DaoMap::setStringType('name','varchar', 200);
        parent::__loadDaoMap();
    
        DaoMap::createIndex('name');
        DaoMap::commit();
    }
}