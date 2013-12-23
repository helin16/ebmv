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
	 * The ID of the admin library site
	 */
	const ID_ADMIN_LIB = 1;
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
	 * The infor this library has
	 *
	 * @var multiple:LibraryInfo
	 */
	protected $infos;
	/**
	 * registry of infos
	 * 
	 * @var array
	 */
	private $_info = array();
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
		$this->loadOneToMany('userAccounts');
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
	 * Getter for the LibraryInfo
	 * @return multiple:LibraryInfo
	 */
	public function getInfos()
	{
		$this->loadOneToMany('infos');
		return $this->infos;
	}
	/**
	 * Setter for the LibraryInfo
	 * 
	 * @param array $infos The LibraryInfo
	 * 
	 * @return Library
	 */
	public function setInfos($infos)
	{
		$this->infos = $infos;
		return $this;
	}
	/**
	 * Getting the info
	 *
	 * @param string $typeCode  The code of the LibraryInfoType
	 * @param string $separator The separator of the returned attributes, in case there are multiple
	 * @param bool   $reset     Forcing the function to fetch values from the database
	 *
	 * @return Ambigous <>
	 */
	public function getInfo($typeCode, $separator = ',', $reset = false)
	{
		if(!isset($this->_info[$typeCode]) || $reset === true)
		{
			$sql = 'select group_concat(lib.value separator ?) `value` from libraryinfo lib inner join libraryinfotype libt on (libt.id = lib.typeId and libt.code = ?) where lib.active = 1 and lib.libraryId = ?';
			$result = Dao::getSingleResultNative($sql, array($separator, $typeCode, $this->getId()), PDO::FETCH_ASSOC);
			$this->_info[$typeCode] = $result['value'];
		}
		return $this->_info[$typeCode];
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJson()
	 */
	public function getJson($extra = array(), $reset = false)
	{
		$array = array();
		if(!$this->isJsonLoaded($reset))
		{
			$infoArray = array();
			$sql = "select distinct libInfo.id `infoId`, libInfo.value `infoValue`, libInfoType.id `typeId`, libInfoType.name `typeName` from libraryinfo libInfo inner join libraryinfotype libInfoType on (libInfo.typeId = libInfoType.id) where libInfo.libraryId = ?";
			$result = Dao::getResultsNative($sql, array($this->getId()), PDO::FETCH_ASSOC);
			foreach($result as $row)
			{
				if(!isset($infoArray[$row['typeId']]))
					$infoArray[$row['typeId']] = array();
				$infoArray[$row['typeId']][] = array("id" => $row['infoId'], "value" => $row["infoValue"], "type" => array("id" => $row["typeId"], "name" => $row["typeName"]));
			}
			$array['info'] = $infoArray;
		}
		return parent::getJson($array, $reset);
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
		DaoMap::setOneToMany("infos", "LibraryInfo","lib_info");
		parent::__loadDaoMap();

		DaoMap::createIndex('name');
		DaoMap::commit();
	}
}

?>