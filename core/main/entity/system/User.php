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
    private $_username;
    /**
     * The session data
     *
     * @var string
     */
    private $_password;
    /**
     * Getter of the username
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
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
        $this->_username = $username;
        return $this;
    }
    /**
     * Getter for the password
     */
    public function getPassword()
    {
        return $this->_password;
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
        $this->_password = $password;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'ua');
        DaoMap::setStringType('_username', 'varchar', 20);
        DaoMap::setStringType('_password', 'varchar', 32);
        parent::__loadDaoMap();
        DaoMap::commit();
    }
}