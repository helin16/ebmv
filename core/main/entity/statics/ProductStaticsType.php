<?php
/**
 * ProductStaticsType Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class ProductStaticsType extends BaseEntityAbstract
{
	const ID_CLICK_RATE = 1;
	const ID_BORROW_RATE = 2;
    /**
     * The attribute of the product
     * 
     * @var string
     */
    private $name;
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
     * @return ProductStaticsType
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return ProductStaticsType
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
    public static function getByCode($code)
    {
    	$class = get_called_class();
    	$objects = EntityDao::getInstance($class)->findByCriteria('code = ?', array(trim($code)),1, 1);
    	return (count($objects) > 0 ? $objects[0] : null);
    }
    public static function get($id)
    {
    	return EntityDao::getInstance(get_called_class())->findById($id);
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
        DaoMap::begin($this, 'pstatstype');
        DaoMap::setStringType('name','varchar', 50);
        DaoMap::setStringType('code','varchar', 50);
        parent::__loadDaoMap();
        DaoMap::createIndex('name');
        DaoMap::createUniqueIndex('code');
        DaoMap::commit();
    }
}