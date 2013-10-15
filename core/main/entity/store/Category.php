<?php
/**
 * Category Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Category extends TreeEntityAbstract
{
    /**
     * how many digits of PER LEVEL
     * @var int
     */
    const POS_LENGTH_PER_LEVEL = 4;
    /**
     * The default separator for breadCrubms
     * @var string
     */
    const BREADCRUMBS_SEPARATOR = ' / ';
    
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
    public function getProducts()
    {
        $this->loadManyToMany('products');
        return $this->products;
    }
    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }
    /**
     * Getting the next position for the new children of the provided parent
     *
     * @throws ServiceException
     * @return int
     */
    public function getNextPosition()
    {
        $pos = $this->getPosition();
        $sql="select position from " . strtoupper(get_class($this)) . " where active = 1 and position like '" . $parentAccountNumber . str_repeat('_', self::POS_LENGTH_PER_LEVEL). "' order by position asc";
        $result = Dao::getResultsNative($sql);
        if(count($result) === 0)
            return $parentAccountNumber . str_repeat('0', AccountEntry::ACC_NO_LENGTH);
         
        $expectedAccountNos = array_map(create_function('$a', 'return "' . $parentAccountNumber . '".str_pad($a, ' . self::POS_LENGTH_PER_LEVEL . ', 0, STR_PAD_LEFT);'), range(0, str_repeat('9', self::POS_LENGTH_PER_LEVEL)));
        $usedAccountNos = array_map(create_function('$a', 'return $a["accountNumber"];'), $result);
        $unUsed = array_diff($expectedAccountNos, $usedAccountNos);
        sort($unUsed);
        if (count($unUsed) === 0)
            throw new ServiceException("Position over loaded (parentId = " . $this->getId() . ")!");
         
        return $unUsed[0];
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