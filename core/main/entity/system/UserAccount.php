<?php
/**
 * UserAccount Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class UserAccount extends BaseEntityAbstract
{
    /**
     * The id of the GUEST account
     * 
     * @var int
     */
    const ID_GUEST_ACCOUNT = 1;
    /**
     * The id of the system account
     * 
     * @var int
     */
    const ID_SYSTEM_ACCOUNT = 100;
    /**
     * The username
     *
     * @var string
     */
    private $username;
    /**
     * The password
     *
     * @var string
     */
    private $password;
    /**
     * The person
     *
     * @var Person
     */
    protected $person;
    /**
     * The roles that this person has
     *
     * @var array
     */
    protected $roles;
    /**
     * The library the user is belonging to
     * 
     * @var Library
     */
    protected $library;
    /**
     * getter UserName
     *
     * @return String
     */
    public function getUserName()
    {
        return $this->username;
    }
    /**
     * Setter UserName
     *
     * @param String $UserName The username
     *
     * @return UserAccount
     */
    public function setUserName($UserName)
    {
        $this->username = $UserName;
        return $this;
    }
    /**
     * getter Password
     *
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Setter Password
     *
     * @param string $Password The password
     *
     * @return UserAccount
     */
    public function setPassword($Password)
    {
        $this->password = $Password;
        return $this;
    }
    /**
     * getter Person
     *
     * @return Person
     */
    public function getPerson()
    {
        $this->loadManyToOne("person");
        return $this->person;
    }
    /**
     * Setter Person
     *
     * @param Person $Person The person that this useraccount belongs to
     *
     * @return UserAccount
     */
    public function setPerson(Person $Person)
    {
        $this->person = $Person;
        return $this;
    }
    /**
     * getter Roles
     *
     * @return Roles
     */
    public function getRoles()
    {
        $this->loadManyToMany("roles");
        return $this->roles;
    }
    /**
     * setter Roles
     *
     * @param array $Roles The roles that this user has
     *
     * @return UserAccount
     */
    public function setRoles(array $Roles)
    {
        $this->roles = $Roles;
        return $this;
    }
    /**
     * Getter for the library
     * 
     * @return Library
     */ 
    public function getLibrary() 
    {
        return $this->library;
    }
    /**
     * Setter for the library
     * 
     * @param Library $value The library the user belongs to
     * 
     * @return UserAccount
     */
    public function setLibrary(Library $value) 
    {
        $this->library = $value;
        return $this;
    }
    /**
     * Cleanup all inactive product shelf items for the current user
     * 
     * @return UserAccount
     */
    public function deleteInactiveShelfItems()
    {
    	$sql = "select p.productId from productshelfitem p left join product pro on (pro.id = p.productId and pro.active = 1) where pro.id is null;";
    	$productIds = array_map(create_function('$a', 'return trim($a[0]);'), Dao::getResultsNative($sql, array(), PDO::FETCH_NUM));
    	if(count($productIds) > 0)
    		Dao::deleteByCriteria(new DaoQuery('ProductShelfItem'), 'productId in (' . implode(', ', $productIds) . ')  AND ownerId = ' . $this->getId());
    	Dao::deleteByCriteria(new DaoQuery('ProductShelfItem'), 'active = 0 AND ownerId = ' . $this->getId());
    	return $this;
    }
    /**
     * Getting the bookshelfitems for a user
     * 
     * @param int   $pageNo
     * @param int   $pageSize
     * @param array $orderby
     * 
     * @return array
     */
    public function getBookShelfItem($pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderby = array())
    {
    	if(intval($this->getId()) === self::ID_GUEST_ACCOUNT)
    		return array();
    	$this->deleteInactiveShelfItems();
    	return EntityDao::getInstance('ProductShelfItem')->findByCriteria('ownerId = ?', array($this->getId()), $pageNo, $pageSize, $orderby);
    }
    /**
     * Getting the count of bookshelfitem for this user
     * 
     * @return int
     */
    public function countBookShelfItem()
    {
    	if(intval($this->getId()) === self::ID_GUEST_ACCOUNT)
    		return 0;
    	$this->deleteInactiveShelfItems();
    	return EntityDao::getInstance('ProductShelfItem')->countByCriteria('ownerId = ? and active = ? ', array($this->getId(), 1));
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__toString()
     */
    public function __toString()
    {
        return $this->getUserName();
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'ua');
        DaoMap::setStringType('username', 'varchar', 100);
        DaoMap::setStringType('password', 'varchar', 40);
        DaoMap::setManyToOne("person", "Person", "p");
        DaoMap::setManyToMany("roles", "Role", DaoMap::LEFT_SIDE, "r", false);
        DaoMap::setManyToOne('library', 'Library', 'lib');
        parent::__loadDaoMap();
        
        DaoMap::createIndex('username');
        DaoMap::createIndex('password');
        DaoMap::commit();
    }
     
}

?>
