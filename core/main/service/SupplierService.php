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
    	$sql = 'select supplierId from supplierprice where active = 1 and productId = ? order by price asc limit 1';
    	$result = Dao::getResultsNative($sql, array($product->getId()));
    	if(count($result) === 0)
    		return null;
    	return $this->get($result[0]['supplierId']);
    }
}
?>
