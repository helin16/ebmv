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
    /**
     * cleanup unused shelfitems
     * 
     * @param UserAccount $user The owner of the shelfitem
     * 
     * @return ProductShelfItemService
     */
    public function cleanUpShelfItems(UserAccount $user = null)
    {
    	if($user instanceof UserAccount)
    		$user->deleteInactiveShelfItems();
    	else
    	{
	    	$sql = "select p.productId from productshelfitem p left join product pro on (pro.id = p.productId and pro.active = 1) where pro.id is null;";
	    	$productIds = array_map(create_function('$a', 'return trim($a[0]);'), Dao::getResultsNative($sql, array(), PDO::FETCH_NUM));
	    	if(count($productIds) > 0)
	    		Dao::deleteByCriteria(new DaoQuery($this->_entityName), 'productId in (' . implode(', ', $productIds) . ')');
	    	Dao::deleteByCriteria(new DaoQuery($this->_entityName), 'active = 0');
    	}
    	return $this;
    }
    /**
     * Getting the shelf items
     * 
     * @param UserAccount $user
     * @param Supplier    $supplier
     * @param number      $pageNo
     * @param number      $pageSize
     * @param array       $orderBy
     * 
     * @return Ambigous <Ambigous, multitype:, multitype:BaseEntityAbstract >
     */
    public function getShelfItems(UserAccount $user, Supplier $supplier = null, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array())
    {
    	$where = 'psitem.ownerId = ?';
    	$params = array($user->getId());
    	if($supplier instanceof Supplier)
    	{
    		$query = EntityDao::getInstance($this->_entityName)->getQuery();
    		$query->eagerLoad("ProductShelfItem.product", DaoQuery::DEFAULT_JOIN_TYPE, 'p');
    		$where .= ' AND p.SupplierId = ?';
    		$params[] = $supplier->getId();
    	}
    	return $this->findByCriteria($where, $params, true, $pageNo, $pageSize, $orderBy);
    }
    /**
     * Adding a product onto shelf
     * 
     * @param UserAccount $user
     * @param Product     $product
     * @param Library     $lib
     * 
     * @throws Exception
     * @return ProductShelfItemService
     */
    public function addToShelf(UserAccount $user, Product $product, Library $lib = null)
    {
    	$lib = ($lib instanceof Library ? $lib : Core::getLibrary());
    	$this->syncShelfItem($user, $product, new UDate(), ProductShelfItem::ITEM_STATUS_NOT_BORROWED);
    	SupplierConnectorAbstract::getInstance($product->getSupplier(), $lib)->addToBookShelfList($user, $product);
   		return $this;
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
    public function borrowItem(UserAccount $user, Product $product, Library $lib = null)
    {
    	$lib = ($lib instanceof Library ? $lib : Core::getLibrary());
    	SupplierConnectorAbstract::getInstance($product->getSupplier(), $lib)->borrowProduct($product, $user);
    	$this->syncShelfItem($user, $product, new UDate(), ProductShelfItem::ITEM_STATUS_BORROWED);
    	return $this;
    }
    /**
     * Removing the item from the bookshelf
     * 
     * @param UserAccount $user
     * @param Product     $product
     * @param Library     $lib
     * 
     * @throws Exception
     * @return ProductShelfItemService
     */
    public function removeItem(UserAccount $user, Product $product, Library $lib = null)
    {
    	$lib = ($lib instanceof Library ? $lib : Core::getLibrary());
    	EntityDao::getInstance('ProductShelfItem')->updateByCriteria('`active` = 0', '`productId` = ? and `ownerId` = ?', array($product->getId(), $user->getId()));
    	SupplierConnectorAbstract::getInstance($product->getSupplier(), $lib)->removeBookShelfList($user, $product);
    	return $this;
    }
    /**
     * returnItem
     * 
     * @param UserAccount $user
     * @param Product     $product
     * @param Library     $lib
     * 
     * @throws Exception
     * @return ProductShelfItemService
     */
    public function returnItem(UserAccount $user, Product $product, Library $lib = null)
    {
    	$lib = ($lib instanceof Library ? $lib : Core::getLibrary());
    	EntityDao::getInstance('ProductShelfItem')->updateByCriteria('`status` = ?', '`productId` = ? and `ownerId` = ?', array(ProductShelfItem::ITEM_STATUS_NOT_BORROWED, $product->getId(), $user->getId()));
    	SupplierConnectorAbstract::getInstance($product->getSupplier(), $lib)->returnProduct($product, $user);
    	SupplierConnectorAbstract::getInstance($product->getSupplier(), $lib)->removeBookShelfList($user, $product);
    	return $this;
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
    		$libraryLoanTime = Core::getLibrary()->getInfo('max_loan_time');
    		if(trim($libraryLoanTime) === '')
    			$libraryLoanTime = SystemSettings::getSettings(SystemSettings::TYPE_DEFAULT_MAX_LOAN_TIME);
    		$item = new ProductShelfItem();
    		$item->setOwner($user);
    		$item->setProduct($product);
    		$item->setBorrowTime($borrowTime);
    		$item->setExpiryTime($item->getBorrowTime()->modify($libraryLoanTime));
    		$item->setStatus($status);
    		$this->save($item);
    	}
    	else
    		$this->updateByCriteria('`status` = ?', $where, array_merge(array($status), $params));
    	return $this;
    }
}
?>
