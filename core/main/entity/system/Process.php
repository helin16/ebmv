<?php
/** Process Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Process extends BaseEntityAbstract
{
	/**
	 * The processId 
	 * 
	 * @var int
	 */
	private $processId;
	/**
	 * The starting time
	 * 
	 * @var Udate
	 */
	private $start;
	/**
	 * The terminate time
	 * 
	 * @var Udate
	 */
	private $end;
	/**
	 * The lifespan in second
	 * 
	 * @var int
	 */
	private $lifespan;
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
	 * getter for processId
	 *
	 * @return
	 */
	public function getProcessId()
	{
		return $this->processId;
	}
	/**
	 * Setter for processId
	 *
	 * @return Process
	 */
	public function setProcessId($processId)
	{
		$this->processId = $processId;
		return $this;
	}
	/**
	 * getter for start
	 *
	 * @return UDate
	 */
	public function getStart()
	{
		return $this->start;
	}
	/**
	 * Setter for start
	 *
	 * @return Process
	 */
	public function setStart($start)
	{
		$this->start = $start;
		return $this;
	}
	/**
	 * getter for end
	 *
	 * @return UDate
	 */
	public function getEnd()
	{
		return $this->end;
	}
	/**
	 * Setter for end
	 *
	 * @return Process
	 */
	public function setEnd($end)
	{
		$this->end = $end;
		return $this;
	}
	/**
	 * getter for lifespan
	 *
	 * @return int
	 */
	public function getLifespan()
	{
		return $this->lifespan;
	}
	/**
	 * Setter for lifespan
	 *
	 * @return Process
	 */
	public function setLifespan($lifespan)
	{
		$this->lifespan = $lifespan;
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
	 * @return Process
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
	 * @param string $value The type of the process
	 * 
	 * @return Process
	 */
	public function setType($value) 
	{
	    $this->type = $value;
	    return $this;
	}
	/**
	 * Process creation
	 * 
	 * @param int $processId
	 * @param UDate $start
	 * @param string $lifespan
	 * @param string $end
	 * @param string $comments
	 * @param string $type
	 * @return Process
	 */
	public static function create($processId, UDate $start, $lifespan, $end = '', $comments = '', $type = '')
	{
		$entity = new Process();
		$entity->setProcessId($processId)
			->setStart($start)
			->setLifespan($lifespan)
			->setEnd($end)
			->setComments($comments)
			->setType($type)
			->save();
		return $entity;
	}
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'ps');
	
		DaoMap::setIntType('processId', 'INT', '10');
		DaoMap::setDateType('start', 'DATETIME');
		DaoMap::setDateType('end', 'DATETIME');
		DaoMap::setIntType('lifespan', 'INT', '255');
		DaoMap::setStringType('comments', 'VARCHAR', '255');
		DaoMap::setStringType('type', 'VARCHAR', '50');
		
		parent::__loadDaoMap();
	
		DaoMap::createIndex('processId');
		DaoMap::createIndex('start');
		DaoMap::createIndex('lifespan');
		
		DaoMap::commit();
	}
}