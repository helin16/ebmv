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
}
?>
