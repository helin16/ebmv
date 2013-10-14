<?php
/**
 * ProductAttribute service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class ProductAttributeService extends BaseServiceAbastract
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
    /**
     * update the product attribute, when exsits; otherwise create one
     * 
     * @param Product              $product   The product
     * @param ProductAttributeType $type      The product type
     * @param string               $attribute The attribute content
     * @return Ambigous <BaseEntity, BaseEntityAbstract>
     */
    public function updateAttributeForProduct(Product $product, ProductAttributeType $type, $attribute)
    {
        if(count($atts = $this->getAttributeForProductAndType($product, $type, 1, 1)) === 0)
            $attr = new ProductAttribute(); 
        else
            $attr = $atts[0];
        
        $attr->setType($type)
            ->setProduct($product)
            ->setAttribute($attribute);
        return $this->save($attr);
    }
}
?>
