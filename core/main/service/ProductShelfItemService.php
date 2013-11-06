<?php
/**
 * ProductShelfItemService
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class ProductShelfItemService extends BaseServiceAbastract
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("ProductShelfItem");
    }
    public function getShelfItems(UserAccount $user, Supplier $supplier = null, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
    {
    	$query = EntityDao::getInstance($this->_entityName)->getQuery();
    	$query->eagerLoad("ProductShelfItem.product", DaoQuery::DEFAULT_JOIN_TYPE, 'p')->eagerLoad("Product.supplierPrices", DaoQuery::DEFAULT_JOIN_TYPE, 'sup_price');
    	return $this->findByCriteria("sup_price.SupplierId = ? and psitem.ownerId = ?", array($supplier->getId(), $user->getId()), true, $pageNo, $pageSize, $orderBy);
    }
    /**
     * Borrow an item / add to our self
     * 
     * @param UserAccount $user
     * @param Product     $product
     * @param Library     $lib
     * @param Supplier    $supplier
     * 
     * @throws Exception
     * @return ProductShelfItemService
     */
    public function borrowItem(UserAccount $user, Product $product, Library $lib = null, Supplier $supplier = null)
    {
    	$transStarted = false;
		try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true;}
		try
		{
	    	$lib = ($lib instanceof Library ? $lib : Core::getLibrary());
	    	$supplier = ($supplier instanceof Supplier ? $supplier : BaseServiceAbastract::getInstance('Supplier')->getCheapestSupplier($product));
    		SupplierConnector::getInstance($supplier)->addToBookShelfList($user, $product, $lib);
    		$this->syncShelfItem($user, $product, new UDate(), ProductShelfItem::ITEM_STATUS_BORROWED);
    		
	    	if($transStarted === false)
	    		Dao::commitTransaction();
    		return $this;
    	}
    	catch(Exception $ex)
    	{
    		if($transStarted === false)
    			Dao::rollbackTransaction();
    		throw $ex;
    	}
    }
    public function removeItem(UserAccount $user, Product $product, Library $lib = null, Supplier $supplier = null)
    {
    	$transStarted = false;
    	try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true;}
    	try
    	{
    		$lib = ($lib instanceof Library ? $lib : Core::getLibrary());
    		$supplier = ($supplier instanceof Supplier ? $supplier : BaseServiceAbastract::getInstance('Supplier')->getCheapestSupplier($product));
    		SupplierConnector::getInstance($supplier)->removeBookShelfList($user, $product, $lib);
    		EntityDao::getInstance('ProductShelfItem')->updateByCriteria('`active` = 0', '`productId` = ? and `ownerId` = ?', array($product->getId(), $user->getId()));
    		if($transStarted === false)
    			Dao::commitTransaction();
    		return $this;
    	}
    	catch(Exception $ex)
    	{
    		if($transStarted === false)
    			Dao::rollbackTransaction();
    		throw $ex;
    	}
    }
    /**
     * synchronize shelf item with local database
     *
     * @param UserAccount $user
     * @param Product     $product
     * @param string      $borrowTime
     * @param int         $status
     *
     * @return SupplierConnector
     */
    public function syncShelfItem(UserAccount $user, Product $product, $borrowTime, $status)
    {
    	$where = '`productId` = ? and `ownerId` = ?';
    	$params = array($product->getId(), $user->getId());
    	$count = EntityDao::getInstance('ProductShelfItem')->countByCriteria($where, $params);
    	if($count == 0 )
    	{
    		$item = new ProductShelfItem();
    		$item->setOwner($user);
    		$item->setProduct($product);
    		$item->setBorrowTime($borrowTime);
    		$item->setStatus($status);
    		EntityDao::getInstance('ProductShelfItem')->save($item);
    	}
    	else
    		EntityDao::getInstance('ProductShelfItem')->updateByCriteria('`borrowTime` = ?, `status` = ?', $where, array_merge(array($borrowTime, $status), $params));
    	return $this;
    }
}
?>
