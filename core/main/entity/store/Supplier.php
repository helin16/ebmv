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
    const MINUTE_REGEX = '/^(\*|[0-9]{1}|[1-5][0-9])$/';
    const HOUR_REGEX = '/^(\*|[0-9]{1}|[1][0-9]|[2][0-3])$/';
    const DOM_REGEX = '/^(\*|[1-9]{1}|[1][0-9]|[2][0-9]|[3][0-1])$/';
    const MONTH_REGEX = '/^(\*|[1-9]{1}|[1][0-2])$/';
    const DOW_REGEX = '/^(\*|[0-7]{1})$/';

	/**
     * The name of the supplier
     * 
     * @var string
     */
    private $name;
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
	    if(self::validateScheduledTime($scheduledTime) === true)
			$this->scheduledTime = $scheduledTime;
			
	    return $this;
	}
	
	/**
	 * Validate the Scheduled time based on the time format of the UNIX crontab
	 *
	 * @param String $scheduledTime
	 * 
	 * @return boolean
	 */
	public static function validateScheduledTime($scheduledTime)
	{
		$errorMessage = 'Valid format should be 1 2 3 4 5 WHERE 1 = Minute (0-59)/*, 2 = Hour (0-23)/*, 3= Day of the Month (1-31)/*, 4 = Month(1-12)/*, 5 = Day of week (0-7)/*';
		$stArray = explode(' ', $scheduledTime);
		if(count($stArray) !== 5)
			throw new Exception($scheduledTime.' is not valid.'.$errorMessage);

		if(preg_match(self::MINUTE_REGEX, trim($stArray[0])) === 0)
			throw new Exception('Minute on scheduled time ['.$scheduledTime.'] is NOT valid.'.$errorMessage);
		if(preg_match(self::HOUR_REGEX, trim($stArray[1])) === 0)
			throw new Exception('Hour on scheduled time ['.$scheduledTime.'] is NOT valid.'.$errorMessage);
		if(preg_match(self::DOM_REGEX, trim($stArray[2])) === 0)
			throw new Exception('Day of Month on scheduled time ['.$scheduledTime.'] is NOT valid.'.$errorMessage);
		if(preg_match(self::MONTH_REGEX, trim($stArray[3])) === 0)
			throw new Exception('Month on scheduled time ['.$scheduledTime.'] is NOT valid.'.$errorMessage);
		if(preg_match(self::DOW_REGEX, trim($stArray[4])) === 0)
			throw new Exception('Day of Week on scheduled time ['.$scheduledTime.'] is NOT valid.'.$errorMessage);
			
		return true;	
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
		DaoMap::setStringType('scheduledTime','varchar', 200);
		parent::__loadDaoMap();
		
		DaoMap::createIndex('name');
		DaoMap::createIndex('supplierLocation');
		
		DaoMap::commit();
	}
}

?>