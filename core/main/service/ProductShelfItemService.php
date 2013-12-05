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
    	$where = 'psitem.ownerId = ?';
    	$params = array($user->getId());
    	if($supplier instanceof Supplier)
    	{
    		$query = EntityDao::getInstance($this->_entityName)->getQuery();
    		$query->eagerLoad("ProductShelfItem.product", DaoQuery::DEFAULT_JOIN_TYPE, 'p');
    		$where .=' AND p.SupplierId = ?';
    		$params[] = $supplier->getId();
    	}
    	return $this->findByCriteria($where, $params, true, $pageNo, $pageSize, $orderBy);
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
    		$this->syncShelfItem($user, $product, new UDate(), ProductShelfItem::ITEM_STATUS_BORROWED);
    		SupplierConnectorAbstract::getInstance($supplier, Core::getLibrary())->addToBookShelfList($user, $product);
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
    		
    		EntityDao::getInstance('ProductShelfItem')->updateByCriteria('`active` = 0', '`productId` = ? and `ownerId` = ?', array($product->getId(), $user->getId()));
    		
    		SupplierConnectorAbstract::getInstance($supplier, Core::getLibrary())->removeBookShelfList($user, $product);
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
     * @return SupplierConnectorAbstract
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
