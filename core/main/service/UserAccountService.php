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
        $userAccounts = $this->findByCriteria("`UserName` = :username AND `Password` = :password", array('username' => $username, 'password' => sha1($password)));
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
        $userAccounts = $this->findByCriteria("`UserName` = :username ", array('username' => $username));
        if(count($userAccounts) === 1)
            return $userAccounts[0];
        else if(count($userAccounts) > 1)
            throw new AuthenticationException("Multiple Users Found!Contact you administrator!");
        else
            throw new AuthenticationException("No User Found!");
    }
}
?>
