<?php
/**
 * The mid data container for the library connector
 *
 * @package    Core
 * @subpackage Utils
 * @author     lhe<helin16@gmail.com>
 */
class LibraryConnectorUser
{
	/**
	 * The username
	 * 
	 * @var string
	 */
	private $_username;
	/**
	 * The password for this user
	 * 
	 * @var string
	 */
	private $_password;
	/**
	 * The library
	 * @var Library
	 */
	private $_library;
	/**
	 * The cache for the static getUser function
	 * 
	 * @var array
	 */
	private static $_cache;
	/**
	 * Getting the LibraryConnectorUser
	 * 
	 * @param unknown $username
	 * @param unknown $password
	 * 
	 * @return LibraryConnectorUser
	 */
	public static function getUser(Library $lib, $username, $password){
		$key = md5($lib->getId() . $username . $password);
		if(!isset($_cache[$key]))
		{
			$class = trim(get_called_class());
			$user = new $class;
			$user->setUsername($username);
			$user->setPassword($password);
			self::$_cache[$key] = $user;
		}
		return self::$_cache[$key];
	}
	/**
	 * Getter for username
	 *
	 * @return string
	 */
	public function getUsername() 
	{
	    return $this->_username;
	}
	/**
	 * Setter for username
	 *
	 * @param string $value The username
	 *
	 * @return string
	 */
	public function setUsername($value) 
	{
	    $this->_username = $value;
	    return $this;
	}
	/**
	 * Getter for _password
	 *
	 * @return string
	 */
	public function getPassword() 
	{
	    return $this->_password;
	}
	/**
	 * Setter for _password
	 *
	 * @param unkown $value The _password
	 *
	 * @return LibraryConnectorUser
	 */
	public function setPassword($value) 
	{
	    $this->_password = $value;
	    return $this;
	}
	/**
	 * Getter for _library
	 *
	 * @return Library
	 */
	public function getLibrary() 
	{
	    return $this->_library;
	}
	/**
	 * Setter for _library
	 *
	 * @param unkown $value The _library
	 *
	 * @return LibraryConnectorUser
	 */
	public function setLibrary(Library $value) 
	{
	    $this->_library = $value;
	    return $this;
	}
}