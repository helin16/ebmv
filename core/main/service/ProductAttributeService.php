<?php
/**
 * ProductAttribute service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class ProductAttributeService extends BaseService
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("ProductAttribute");
    }
    
    /**
     * Enter description here...
     *
     * @param Product $product
     * @param ProductAttributeType $type
     * @param unknown_type $pageNumber
     * @param unknown_type $pageSize
     * @param unknown_type $orderByParams
     * @return unknown
     */
    public function getAttributeForProductAndType(Product $product, ProductAttributeType $type, $pageNumber = null, $pageSize = 30, $orderByParams = array())
    {
    	return $this->findByCriteria("productId = ? AND typeId = ?", array($product->getId(), $type->getId()), true, $pageNumber, $pageSize, $orderByParams);
    }
}
?>