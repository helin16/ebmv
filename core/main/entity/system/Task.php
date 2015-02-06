<?php
/** Task Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Task extends BaseEntityAbstract
{
	/**
	 * The name 
	 * 
	 * @var string
	 */
	private $name;
	/**
	 * If task if done
	 * 
	 * @var bool
	 */
	private $done;
	/**
	 * Retry count
	 * 
	 * @var int
	 */
	private $retry;
	/**
	 * The path
	 * 
	 * @var string
	 */
	private $path;
	/**
	 * The comments of the log
	 * 
	 * @var string
	 */
	private $comments;
	/**
	 * getter for name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	/**
	 * Setter for name
	 *
	 * @return Task
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}
	/**
	 * getter for retry
	 *
	 * @return int
	 */
	public function getRetry()
	{
		return $this->retry;
	}
	/**
	 * Setter for retry
	 *
	 * @return Task
	 */
	public function setRetry($retry)
	{
		$this->retry = $retry;
		return $this;
	}
	/**
	 * getter for done
	 *
	 * @return bool
	 */
	public function getDone()
	{
		return $this->done;
	}
	/**
	 * Setter for done
	 *
	 * @return Task
	 */
	public function setDone($done)
	{
		$this->done = $done;
		return $this;
	}
	/**
	 * getter for path
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}
	/**
	 * Setter for path
	 *
	 * @return Task
	 */
	public function setPath($path)
	{
		$this->path = $path;
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
	public function retry()
	{
		$this->setRetry($this->retry + 1)->save();
		return $this;
	}
	/**
	 * 
	 * @param string $path
	 * @param bool $done
	 * @param int $retry
	 * @param string $name
	 * @param string $comments
	 * @return Task
	 */
	public static function create($path, $done, $retry = 0, $name = '', $comments = '')
	{
		$exist = Task::getAllByCriteria('path = ?', array($path), true, 1, 1, array('id'=> 'desc'));
		$entity = (count($exist) > 0 && $exist[0] instanceof Task) ? $exist[0] : new Task();
		$entity->setPath($path)
			->setDone($done)
			->setRetry($retry)
			->setName($name)
			->setComments($comments)
			->save();
		return $entity;
	}
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'tsk');
	
		DaoMap::setStringType('name', 'VARCHAR', '50');
		DaoMap::setStringType('path', 'VARCHAR', '255');
		DaoMap::setBoolType('done');
		DaoMap::setIntType('retry', 'int', 10);
		DaoMap::setStringType('comments', 'VARCHAR', '255');
		
		parent::__loadDaoMap();
	
		DaoMap::createIndex('path');
		DaoMap::createIndex('done');
		DaoMap::createIndex('retry');
		
		DaoMap::commit();
	}
}