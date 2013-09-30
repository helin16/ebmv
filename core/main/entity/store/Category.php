<?php
/**
 * Category Entity - storing the session data in the database
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Category extends TreeEntityAbstract
{
    /**
     * The name of the category
     * 
     * @var string
     */
    private $name;
    /**
     * The products that the products are belongin to
     *
     * @var multiple:Product
     */
    protected $products;
	/**
     * getter Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * setter Name
     *
     * @param string $Name The name of the role
     *
     * @return Role
     */
    public function setName($Name)
    {
        $this->name = $Name;
        return $this;
    }
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'pcat');
		DaoMap::setStringType('name', 'varchar', 255);
		DaoMap::setManyToMany("products", "Product", DaoMap::RIGHT_SIDE, "p", false);
		parent::__loadDaoMap();
		
		DaoMap::createIndex('name');
		DaoMap::commit();
	}
}

?>