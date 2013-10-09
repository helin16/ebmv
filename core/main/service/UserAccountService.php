<?php
/**
 * UserAccount service
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class UserAccountService extends BaseService
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
     * @param string $username    The username string
     * @param string $password    The password string
     *
     * @throws AuthenticationException
     * @throws Exception
     * @return Ambigous <BaseEntityAbstract>|NULL
     */
    public function getUserByUsernameAndPassword($username, $password)
    {
        $query = EntityDao::getInstance($this->_entityName)->getQuery();
        $query->eagerLoad('UserAccount.roles', DaoQuery::DEFAULT_JOIN_TYPE, 'r');
        $userAccounts = $this->findByCriteria("`UserName` = :username AND `Password` = :password AND r.id != :roleId", array('username' => $username, 'password' => sha1($password), 'roleId' => Role::ID_GUEST), false, 1, 2);
        if(count($userAccounts) === 1)
            return $userAccounts[0];
        else if(count($userAccounts) > 1)
            throw new AuthenticationException("Multiple Users Found!Contact you administrator!");
        else
            throw new AuthenticationException("No User Found!");
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
    public function getUserByUsername($username)
    {
        $query = EntityDao::getInstance($this->_entityName)->getQuery();
        $query->eagerLoad('UserAccount.roles', DaoQuery::DEFAULT_JOIN_TYPE, 'r');
        $userAccounts = $this->findByCriteria("`UserName` = :username  AND r.id != :roleId ", array('username' => $username, 'roleId' => Role::ID_GUEST), false, 1, 2);
        if(count($userAccounts) === 1)
            return $userAccounts[0];
        else if(count($userAccounts) > 1)
            throw new AuthenticationException("Multiple Users Found!Contact you administrator!");
        else
            throw new AuthenticationException("No User Found!");
    }
}
?>
