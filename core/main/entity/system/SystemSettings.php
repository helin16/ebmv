<?php
/**
 * SystemSettings
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class SystemSettings extends BaseEntityAbstract
{
	const TYPE_DEFAULT_BORROW_LIMIT = 'def_borrow_limit';
	const TYPE_DEFAULT_MAX_LOAN_TIME = 'def_max_borrow_time';
	/**
	 * The value of the setting
	 * 
	 * @var string
	 */
	private $value;
	/**
	 * The type of the setting
	 * 
	 * @var string
	 */
	private $type;
	/**
	 * The description
	 * 
	 * @var string
	 */
	private $description;
	/**
	 * The cache
	 * 
	 * @var array
	 */
	private static $_cache = array();
	/**
	 * Getting Settings Object
	 * 
	 * @param string $type The type string
	 * 
	 * @return String
	 */
	public static function getSettings($type)
	{
		if(!isset(self::$_cache[$type]))
		{
			$settings = EntityDao::getInstance(__CLASS__)->findByCriteria('type=?', array($type), 1, 1);
			self::$_cache[$type] = trim(count($settings) === 0 ? '' : $settings[0]->getValue());
		}
		return self::$_cache[$type];
	}
	/**
	 * adding a new Settings Object
	 * 
	 * @param string $type The type string
	 */
	public static function addSettings($type, $value)
	{
		$class = __CLASS__;
		$settings = EntityDao::getInstance($class)->findByCriteria('type=?', array($type), 1, 1);
		$setting = ((count($settings) === 0 ? new $class() : $settings[0]));
		$setting->setType($type);
		$setting->setValue($value);
		$setting->setActive(true);
		EntityDao::getInstance($class)->save($setting);
		self::$_cache[$type] = $value;
	}
	/**
	 * Removing Settings Object
	 * 
	 * @param string $type The type string
	 */
	public static function removeSettings($type)
	{
		EntityDao::getInstance(__CLASS__)->updateByCriteria('set active = 0', 'type = ?', array($type));
	}
	/**
	 * Getter for value
	 *
	 * @return int
	 */
	public function getValue() 
	{
	    return $this->value;
	}
	/**
	 * Setter for value
	 *
	 * @param sting $value The value
	 *
	 * @return SystemSettings
	 */
	public function setValue($value) 
	{
	    $this->value = $value;
	    return $this;
	}
	/**
	 * Getter for type
	 *
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}
	/**
	 * Setter for type
	 *
	 * @param sting $type The type
	 *
	 * @return SystemSettings
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}
	/**
	 * Getter for description
	 *
	 * @return string
	 */
	public function getDescription() 
	{
	    return $this->description;
	}
	/**
	 * Setter for description
	 *
	 * @param string $value The description
	 *
	 * @return SystemSettings
	 */
	public function setDescription($value) 
	{
	    $this->description = $value;
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'syssettings');
	
		DaoMap::setStringType('type','varchar', 50);
		DaoMap::setStringType('value','varchar', 255);
		DaoMap::setStringType('description','varchar', 100);
	
		parent::__loadDaoMap();
	
		DaoMap::createUniqueIndex('type');
		DaoMap::commit();
	}
}