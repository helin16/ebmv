<?php
/**
 * ProductTypeService
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class ProductTypeService extends BaseServiceAbastract
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("ProductType");
    }
    /**
     * Getting the name of the producttype
     *
     * @param string $name The name we are searching on
     *
     * @return NULL|ProductType
     */
    public function getByName($name)
    {
    	$types = $this->findByCriteria('name = ?', array($name), true, 1, 1);
    	return count($types) > 0 ? $types[0] : null;
    }
}
?>
