<?php
/**
 * UserAccount service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class UserAccountService extends BaseServiceAbastract
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("UserAccount");
    }
    /**
     * Getting UserAccount
     *
     * @param string  $username The username string
     * @param string  $password The password string
     * @param Library $library  The library the user belongs to
     *
     * @throws AuthenticationException
     * @throws Exception
     * @return Ambigous <BaseEntityAbstract>|NULL
     */
    public function getUserByUsernameAndPassword($username, $password, Library $library = null, $noHashPass = false)
    {
    	$library = ($library instanceof Library ? $library : Core::getLibrary());
    	if(!$library instanceof Library)
    		throw new CoreException('System Error: Invalid Library in system settings!');
        $query = EntityDao::getInstance($this->_entityName)->getQuery();
        $query->eagerLoad('UserAccount.roles', DaoQuery::DEFAULT_JOIN_TYPE, 'r');
        $query->eagerLoad('UserAccount.library', DaoQuery::DEFAULT_JOIN_TYPE, 'lib');
        $userAccounts = $this->findByCriteria("`UserName` = :username AND `Password` = :password AND r.id != :roleId and lib.id = :libId", array('username' => $username, 'password' => ($noHashPass === true ? $password : sha1($password)), 'roleId' => Role::ID_GUEST, 'libId' => $library->getId()), false, 1, 2);
        if(count($userAccounts) === 1)
            return $userAccounts[0];
        if(count($userAccounts) > 1)
            throw new AuthenticationException("Multiple Users Found!Contact you administrator!");
        return null;
    }
    /**
     * Getting UserAccount by username
     *
     * @param string $username    The username string
     *
     * @throws AuthenticationException
     * @throws Exception
     * @return Ambigous <BaseEntityAbstract>|NULL
     */
    public function getUserByUsername($username, Library $library = null)
    {
    	$library = ($library instanceof Library ? $library : Core::getLibrary());
    	if(!$library instanceof Library)
    		throw new CoreException('System Error: Invalid Library in system settings!');
        $query = EntityDao::getInstance($this->_entityName)->getQuery();
        $query->eagerLoad('UserAccount.roles', DaoQuery::DEFAULT_JOIN_TYPE, 'r');
        $query->eagerLoad('UserAccount.library', DaoQuery::DEFAULT_JOIN_TYPE, 'lib');
        $userAccounts = $this->findByCriteria("`UserName` = :username  AND r.id != :roleId and lib.id = :libId", array('username' => $username, 'roleId' => Role::ID_GUEST, 'libId' => $library->getId()), false, 1, 2);
        if(count($userAccounts) === 1)
            return $userAccounts[0];
        else if(count($userAccounts) > 1)
            throw new AuthenticationException("Multiple Users Found!Contact you administrator!");
        else
            return null;
    }
    /**
     * Creating a new useraccount
     * 
     * @param string $username
     * @param string $password
     * @param Role   $role
     * @param Person $person
     * 
     * @return UserAccount
     */
    public function createUser(Library $lib, $username, $password, Role $role, Person $person)
    {
    	if($this->getUserByUsername($username, $lib) instanceof UserAccount)
    		throw new EntityException('System Error: trying to create a username with the same id:' . $username . '!');
    	$userAccount = new UserAccount();
    	$userAccount->setUserName($username);
    	$userAccount->setPassword($password);
    	$userAccount->setPerson($person);
    	$userAccount->setLibrary($lib);
    	$this->save($userAccount);
    	$this->saveManyToMany($role, $userAccount);
    	return $this->get($userAccount->getId());
    }
    /**
     * Updating an useraccount
     * 
     * @param UserAccount $userAccount
     * @param string      $username
     * @param string      $password
     * @param Role        $role
     * @param Person      $person
     * 
     * @return Ambigous <BaseEntity, BaseEntityAbstract>
     */
    public function updateUser(UserAccount &$userAccount, Library $lib, $username, $password, array $allRoles = null, Person $newPerson = null)
    {
    	$person = $userAccount->getPerson();
    	//user wants to update the person
    	if($newPerson instanceof Person)
    	{
	    	$newPerson->setId($person->getId());
	    	BaseServiceAbastract::getInstance('Person')->save($newPerson);//update the old person with all the information from newPerson
	    	$userAccount = $this->get($userAccount->getId());//refresh object;
	    	$person = $newPerson;
    	}
    	$userAccount->setUserName($username);
    	$userAccount->setPassword($password);
    	$userAccount->setPerson($person);
    	$userAccount->setLibrary($lib);
    	$this->save($userAccount);
    	
    	//if we are trying to update the roles too!
    	if($allRoles !== null)
    	{
	    	//format the roles
	    	$wantedRoles = array();
	    	foreach($allRoles as $role)
	    	{
	    		$wantedRoles[$role->getId()] = $role;
	    	}
	    	
	    	//clear all roles
	    	$oldRoles = array();
	    	foreach($userAccount->getRoles() as $role)
	    	{
	    		//we need to delete this old role
	    		if(!array_key_exists($role->getId(), $wantedRoles))
	    			$this->delManyToMany($role, $userAccount);
	    		$wantedRoles[$role->getId()] = null;
	    	}
	    	$wantedRoles = array_filter($wantedRoles);
	    	
	    	//need to create whatever has left after the loop
	    	foreach($wantedRoles as $role)
	    	{
	    		$this->saveManyToMany($role, $userAccount);
	    	}
    	}
    	
    	$userAccount = $this->get($userAccount->getId()); //refersh the object
    	return $userAccount;
    }
    /**
     * login for the library reader
     * 
     * @param Library $lib
     * @param unknown $username
     * @param unknown $password
     */
    public function login(Library $lib, $libCardNo, $password)
    {
    	try
    	{
	    	$transStarted = false;
	    	try{Dao::beginTransaction();} catch(Exception $ex) {$transStarted = true;}
	    	
	    	if(!Core::getUser() instanceof UserAccount)
	    		Core::setUser($this->get(UserAccount::ID_SYSTEM_ACCOUNT));
	    	
	    	//check whether the library has the user or not
	    	if(!$lib->getConnectorScript()->chkUser($libCardNo, $password))
	    		throw new CoreException('Invalid login please contact your library!');
	    	
	    	//get the information from the library system
	    	$userInfo = $lib->getConnectorScript()->getUserInfo($libCardNo, $password);
	    	
	    	//check whether our local db has the record already
	    	if (($userAccount = $this->getUserByUsername($libCardNo, $lib)) instanceof UserAccount)
	    	{
	    		$person = $userAccount->getPerson();
	    		$userAccount = $this->updateUser($userAccount, 
	    				$lib,
	    				$userInfo->getUsername(), 
	    				$userInfo->getPassword(), 
	    				null, 
	    				BaseServiceAbastract::getInstance('Person')->updatePerson($userInfo->getFirstName(), $userInfo->getLastName(), $person)
	    		);
	    	}
	    	else //we need to create a user account from blank
	    	{
	    		$userAccount = $this->createUser(
	    				$lib,
	    				$userInfo->getUsername(), 
	    				$userInfo->getPassword(), 
	    				BaseServiceAbastract::getInstance('Role')->get(Role::ID_READER),
	    				BaseServiceAbastract::getInstance('Person')->updatePerson($userInfo->getFirstName(), $userInfo->getLastName())
	    		);
	    	}
	    	
	    	$role = null;
	    	if(!Core::getRole() instanceof Role)
	    	{
	    		if(count($roles = $userAccount->getRoles()) > 0)
	    			$role = $roles[0];
	    	}
	    	Core::setUser($userAccount, $role);
	    	
	    	if($transStarted === false)
	    		Dao::commitTransaction();
	    	return $userAccount;
    	}
    	catch(Exception $ex)
    	{
    		if($transStarted === false)
    			Dao::rollbackTransaction();
    		throw $ex;
    	}
    }
}
?>
