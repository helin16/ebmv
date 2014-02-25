<?php
/**
 * LibraryOwnsService
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class LibraryOwnsService extends BaseServiceAbastract
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("LibraryOwns");
    }
    /**
     * Updating the libraryowns
     * 
     * @param Product         $product
     * @param Library         $lib
     * @param int             $avail
     * @param int             $total
     * @param LibraryOwnsType $type
     * 
     * @return LibraryOwnsService
     */
    public function updateLibOwns(Product $product, Library $lib, $avail, $total, LibraryOwnsType $type = null)
    {
    	$where = 'productId = ? and libraryId = ?';
    	$params = array($product->getId(), $lib->getId());
    	if($type instanceof LibraryOwnsType)
    	{
    		$where .= ' AND typeId = ?';
    		$params[] = $type->getId();
    	}
    	$this->updateByCriteria('avail = ?, total = ?', $where, $params);
    	return $this;
    }
}
?>
