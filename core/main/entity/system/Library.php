<?php
/** Library Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Library extends BaseEntityAbstract
{
	/**
	 * The name of the Library
	 *
	 * @var string
	 */
	private $name;
	/**
	 * The userAccounts that the userAccounts are belongin to
	 *
	 * @var multiple:UserAccount
	 */
	protected $userAccounts;
	/**
	 * getter Name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	/**
	 * setter Name
	 *
	 * @param string $Name The name of the role
	 *
	 * @return Role
	 */
	public function setName($Name)
	{
		$this->name = $Name;
		return $this;
	}
	/**
	 * Getter for the Useraccounts
	 * @return multiple:UserAccount
	 */
	public function getUserAccounts()
	{
		$this->loadManyToMany('userAccounts');
		return $this->userAccounts;
	}
	/**
	 * Setter for the useraccounts
	 * 
	 * @param array $userAccounts The user acocunts
	 * 
	 * @return Library
	 */
	public function setUserAccounts($userAccounts)
	{
		$this->userAccounts = $userAccounts;
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'lib');
		DaoMap::setStringType('name', 'varchar', 255);
		DaoMap::setOneToMany("userAccounts", "UserAccount","ua");
		parent::__loadDaoMap();

		DaoMap::createIndex('name');
		DaoMap::commit();
	}
}

?>