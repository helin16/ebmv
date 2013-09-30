<?php
/**
 * User Entity - storing the session data in the database
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class User extends BaseEntityAbstract
{
    /**
     * The session ID
     *
     * @var string
     */
    private $username;
    /**
     * The session data
     *
     * @var string
     */
    private $password;
    /**
     * Getter of the username
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    /**
     * Setter for the username
     * 
     * @param string $Username The username of the user
     * 
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    /**
     * Getter for the password
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Setter for the password
     * 
     * @param string $password The password
     * 
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::loadDaoMap()
     */
    public function loadDaoMap()
    {
        DaoMap::begin($this, 'ua');
        DaoMap::setStringType('username', 'varchar', 20);
        DaoMap::setStringType('password', 'varchar', 32);
        parent::loadDaoMap();
        DaoMap::commit();
    }
}