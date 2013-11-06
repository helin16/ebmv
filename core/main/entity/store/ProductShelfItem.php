<?php
/**
 * ProductShelfItem Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class ProductShelfItem extends BaseEntityAbstract
{
	/**
	 * The status code for borrowed item
	 * @var int
	 */
	const ITEM_STATUS_BORROWED = 1;
	/**
	 * The status code for NOT borrowed item
	 * @var int
	 */
	const ITEM_STATUS_NOT_BORROWED = 0;
    /**
     * The owner of the shelf
     * 
     * @var UserAccount
     */
    protected $owner;
    /**
     * The product this attribute is belonging to
     * 
     * @var Product
     */
    protected $product;
    /**
     * Status of the item
     * @var int
     */
    private $status = self::ITEM_STATUS_NOT_BORROWED;
    /**
     * The borrow time of that product
     * @var UDate
     */
    private $borrowTime;
    /**
     * Getter for the staus
     * @return number
     */
    public function getStatus() 
    {
    	return $this->status;
    }
    /**
     * Setter for the status
     * 
     * @param int $value The status of the item
     * 
     * @return ProductShelfItem
     */
    public function setStatus($value) 
    {
    	$this->status = $value;
    	return $this;
    }
    /**
     * Getter for the borrow time of the item
     * 
     * @return UDate
     */   
    public function getBorrowTime() 
    {
    	if(is_string($this->borrowTime))
    		$this->borrowTime = new UDate($this->borrowTime);
    	return $this->borrowTime;
    }
    /**
     * Setter for the borrow time of the item
     * 
     * @param string $value
     * 
     * @return ProductShelfItem
     */
    public function setBorrowTime($value) 
    {
    	$this->borrowTime = $value;
    	return $this;
    }
    /**
     * Getter for the owner
     * 
     * @return UserAccount
     */
    public function getOwner()
    {
        $this->loadManyToOne('owner');
        return $this->owner;
    }
    /**
     * Setter for the Owner
     * 
     * @param UserAccount $owner The owner of the shelf
     * 
     * @return ProductShelfItem
     */
    public function setOwner(UserAccount $owner)
    {
        $this->owner = $owner;
        return $this;
    }
    /**
     * Getter for the product
     * 
     * @return Product
     */
    public function getProduct()
    {
        $this->loadManyToOne('product');
        return $this->product;
    }
    /**
     * Setter for the product
     * 
     * @param Product $product The product
     * 
     * @return ProductShelfItem
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::getJson()
     */
    public function getJson()
    {
        $array = parent::getJson();
        $array['type'] = $this->getType()->getJson();
        return $array;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'psitem');
        DaoMap::setManyToOne('product', 'Product');
        DaoMap::setManyToOne('owner', 'UserAccount');
        DaoMap::setIntType('status', 'int', 1);
        DaoMap::setDateType('borrowTime');
        parent::__loadDaoMap();
        DaoMap::createIndex('status');
        DaoMap::createIndex('borrowTime');
        DaoMap::commit();
    }
}