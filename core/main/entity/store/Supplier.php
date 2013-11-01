<?php
/**
 * Supplier Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Supplier extends BaseEntityAbstract
{
	/**
     * The name of the supplier
     * 
     * @var string
     */
    private $name;
    /**
     * The suppliedLocation
     * 
     * @var string
     */
    private $suppliedLocation;
	
	/**
	 * Getter for the title
	 * 
	 * @return string
	 */
	public function getName()
	{
	    return $this->name;
	}
	
	/**
	 * Setter for 
	 * 
	 * @param string $name The name of supplier
	 * 
	 * @return Supplier
	 */
	public function setName($name)
	{
	    $this->name = $name;
	    return $this;
	}
	/**
	 * Getter for the supplierLocation
	 * 
	 * @return string
	 */
	public function getSupplierLocation()
	{
	    return $this->suppliedLocation;
	}
	
	/**
	 * Setter for 
	 * 
	 * @param string $supplierLocation The location of the supplier
	 * 
	 * @return Supplier
	 */
	public function setSupplierLocation($supplierLocation)
	{
	    $this->suppliedLocation = $supplierLocation;
	    return $this;
	}
	/**
	 * Getting the info
	 *
	 * @param string $typeCode  The code of the SupplierInfoType
	 * @param string $separator The separator of the returned attributes, in case there are multiple
	 *
	 * @return Ambigous <>
	 */
	public function getInfo($typeCode, $separator = ',')
	{
		$sql = 'select group_concat(si.value separator ?) `info` from supplierinfo si inner join supplierinfotype sit on (sit.id = si.typeId and sit.code = ?) where si.active = 1 and si.supplierId = ?';
		$result = Dao::getSingleResultNative($sql, array($separator, $typeCode, $this->getId()), PDO::FETCH_ASSOC);
		return $result['info'];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'supp');
		DaoMap::setStringType('name','varchar', 200);
		DaoMap::setStringType('supplierLocation','varchar', 200);
		parent::__loadDaoMap();
		
		DaoMap::createIndex('name');
		DaoMap::createIndex('supplierLocation');
		
		DaoMap::commit();
	}
}

?>