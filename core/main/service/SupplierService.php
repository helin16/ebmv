<?php
/**
 * SupplierService
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class SupplierService extends BaseServiceAbastract
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("Supplier");
    }
    /**
     * Getting the cheapest supplier for a product
     * 
     * @param Product $product The product
     * 
     * @return Supplier
     */
    public function getCheapestSupplier(Product $product)
    {
    	return $product->getSupplier();
    }
}
?>
