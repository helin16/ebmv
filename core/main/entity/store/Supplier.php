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
     * The username of the supplier
     * 
     * @var string
     */
    private $username;
    
    /**
     * The password of the supplier
     * 
     * @var string
     */
    private $password;
    
    /**
     * The scheduledTime of the supplier
     * 
     * @var string
     */
    private $scheduledTime;
    
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
	 * Getter for the username
	 * 
	 * @return string
	 */
	public function getUsername()
	{
	    return $this->username;
	}
	
	/**
	 * Setter for 
	 * 
	 * @param string $username The username of supplier
	 * 
	 * @return Supplier
	 */
	public function setUsername($username)
	{
	    $this->username = $username;
	    return $this;
	}
	
	/**
	 * Getter for the password
	 * 
	 * @return string
	 */
	public function getPassword()
	{
	    return $this->password;
	}
	
	/**
	 * Setter for 
	 * 
	 * @param string $password The password for supplier
	 * 
	 * @return Supplier
	 */
	public function setPassword($password)
	{
	    $this->password = $password;
	    return $this;
	}
	
	/**
	 * Getter for the scheduledTime
	 * 
	 * @return string
	 */
	public function getScheduledTime()
	{
	    return $this->scheduledTime;
	}
	
	/**
	 * Setter for 
	 * 
	 * @param string $scheduledTime The schedule time on when the cron is going to run for supplier
	 * 
	 * @return Supplier
	 */
	public function setScheduledTime($scheduledTime)
	{
	    $this->scheduledTime = $scheduledTime;
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
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'supp');
		DaoMap::setStringType('name','varchar', 200);
		DaoMap::setStringType('supplierLocation','varchar', 200);
		DaoMap::setStringType('username','varchar', 200);
		DaoMap::setStringType('password','varchar', 200);
		DaoMap::setStringType('scheduledTime','varchar', 200);
		parent::__loadDaoMap();
		
		DaoMap::createIndex('name');
		DaoMap::createIndex('supplierLocation');
		
		DaoMap::commit();
	}
}

?>