<?php
/**
 * ProductAttributeType service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class ProductAttributeTypeService extends BaseService
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("ProductAttributeType");
    }
    /**
     * Getting the product type by code
     * 
     * @param string $code The code we are searching on
     * 
     * @return NULL|ProductAttributeType
     */
    public function getTypeByCode($code)
    {
        $types = $this->findByCriteria('code = ?', array($code), true, 1, 1);
        return count($types) > 0 ? $types[0] : null;
    }
}
?>
