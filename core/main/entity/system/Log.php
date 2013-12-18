<?php
/** Log Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Log extends BaseEntityAbstract
{
	/**
	 * caching the transid
	 * 
	 * @var string
	 */
	private static $_transId = '';
	/**
	 * The id of the entity
	 * 
	 * @var int
	 */
	private $entityId;
	/**
	 * The entity name
	 * 
	 * @var string
	 */
	private $entityName;
	/**
	 * The content of the log
	 * 
	 * @var string
	 */
	private $msg;
	/**
	 * The comments of the log
	 * 
	 * @var string
	 */
	private $comments;
	/**
	 * The type of the log
	 * 
	 * @var string
	 */
	private $type;
	/**
	 * The identifier of that transation
	 * 
	 * @var string
	 */
	private $transId;
	/**
	 * Getter for entityId
	 */
	public function getEntityId() 
	{
	    return $this->entityId;
	}
	/**
	 * Setter of the log
	 * 
	 * @param idt $value The id of entity
	 * 
	 * @return Log
	 */
	public function setEntityId($value) 
	{
	    $this->entityId = $value;
	    return $this;
	}
	/**
	 * Getter for the entity name
	 * 
	 * @return string
	 */
	public function getEntityName() 
	{
	    return $this->entityName;
	}
	/**
	 * Setter for the entity name
	 * 
	 * @param string $value The name of the entity
	 * 
	 * @return Log
	 */
	public function setEntityName($value) 
	{
	    $this->entityName = $value;
	    return $this;
	}
	/**    
	 * Getter for the Msg
	 * 
	 * @return string
	 */
	public function getMsg() 
	{
	    return $this->msg;
	}
	/**
	 * Setter for the msg
	 * 
	 * @param string $value The log content
	 * 
	 * @return Log
	 */
	public function setMsg($value) 
	{
	    $this->msg = $value;
	    return $this;
	}
	/**
	 * Getter for the comments
	 * 
	 * @return string
	 */
	public function getComments() 
	{
	    return $this->comments;
	}
	/**
	 * Setter for the comments
	 * 
	 * @param string $value The comments
	 * 
	 * @return Log
	 */
	public function setComments($value)
	{
	    $this->comments = $value;
	    return $this;
	}
	/**
	 * Getter for the type
	 * 
	 * @return string
	 */
	public function getType() 
	{
	    return $this->type;
	}
	/**
	 * Setter for the type
	 * 
	 * @param string $value The type of the log
	 * 
	 * @return Log
	 */
	public function settype($value) 
	{
	    $this->type = $value;
	    return $this;
	}
	/**
	 * Getter for the transId
	 * 
	 * @return string
	 */
	public function getTransId() 
	{
	    return $this->transId;
	}
	/**
	 * Setter for the transId
	 * 
	 * @param string $value The transId
	 * 
	 * @return Log
	 */
	public function settransId($value) 
	{
	    $this->transId = $value;
	    return $this;
	}
	/**
	 * Logging
	 * 
	 * @param int    $entityId
	 * @param string $entityName
	 * @param string $msg
	 * @param string $type
	 * @param string $comments
	 */
	public static function logging($entityId, $entityName, $msg, $type, $comments = '')
	{
		$className = __CLASS__;
		$log = new $className();
		$log->setEntityId(self::_getTransId());
		$log->setEntityId($entityId);
		$log->setEntityName($entityName);
		$log->setMsg($msg);
		$log->settype($type);
		$log->setComments($comments);
		EntityDao::getInstance($className)->save($log);
	}
	/**
	 * Getting the transid
	 * 
	 * @param string $salt The salt of making the trans id
	 * 
	 * @return string
	 */
	private static function _getTransId($salt = '')
	{
		if(trim(self::$_transId) === '')
			self::$_transId = StringUtilsAbstract::getRandKey($salt);
		return self::$_transId;
	}
	/**
	 * Logging the entity
	 * 
	 * @param BaseEntityAbstract $entity
	 * @param string             $msg
	 * @param string             $type
	 * @param string             $comments
	 */
	public static function LogEntity(BaseEntityAbstract $entity, $msg, $type, $comments = '')
	{
		self::logEntity($entity->getId(), get_class($entity), $msg, $type, $comments);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'log');
		
		DaoMap::setStringType('transId','varchar', 100);
		DaoMap::setIntType('entityId');
		DaoMap::setStringType('entityName','varchar', 100);
		DaoMap::setStringType('msg','LONGTEXT');
		DaoMap::setStringType('comments','varchar', 255);
		DaoMap::setStringType('type','varchar', 100);
		
		parent::__loadDaoMap();
		
		DaoMap::createIndex('transId');
		DaoMap::createIndex('entityId');
		DaoMap::createIndex('entityName');
		DaoMap::createIndex('type');
		
		DaoMap::commit();
	}
}