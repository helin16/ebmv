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
    private $_name;
	/**
	 * The products that the products are belongin to 
	 * 
	 * @var multiple:Product
	 */
	protected $_products;
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'sess');
		DaoMap::setStringType('key', 'varchar', 32);
		DaoMap::setStringType('data', 'longtext');
		parent::__loadDaoMap();
		DaoMap::commit();
	}
}

?>