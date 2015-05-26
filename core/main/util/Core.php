<?php
/**
 * Global core settings and operations, This is for runtime only
 *
 * @package    Core
 * @subpackage Utils
 * @author     lhe<helin16@gmail.com>
 */
abstract class Core
{
    /**
     * The storage for the Core at the runtime level
     *
     * @var array
     */
	private static $_storage = array('userId' => '', 'roleId' => '', 'origPass' => '');
	/**
	 * @var Library
	 */
	private static $_lib = null;
    /**
     * Setting the role in the core
     *
     * @param Role $role The role
     */
	public static function setRole(Role $role, $origPass = '')
	{
		self::setUser(self::getUser(), $role, $origPass);
	}
	/**
	 * removing core role
	 */
	public static function rmRole()
	{
	    self::$_storage['role'] = null;
	}
	/**
	 * Set the active user on the core for auditing purposes
	 *
	 * @param UserAccount $userAccount The useraccount
	 * @param Role        $role        The role
	 */
	public static function setUser(UserAccount $userAccount, Role $role = null, $origPass = '')
	{
		self::$_storage['userId'] = $userAccount->getId();
		self::$_storage['roleId'] = $role instanceof Role ? $role->getId() : '';
		self::$_storage['origPass'] = $origPass;
	}
	/**
	 * removing core user
	 */
	public static function rmUser()
	{
	    self::$_storage['userId'] = '';
	    self::rmRole();
	}
	/**
	 * Get the current user set against the System for auditing purposes
	 *
	 * @return UserAccount
	 */
	public static function getUser()
	{
		return UserAccount::get(self::$_storage['userId']);
	}
	/**
	 * Get the current user role set against the System for Dao filtering purposes
	 *
	 * @return Role
	 */
	public static function getRole()
	{
		return Role::get(self::$_storage['roleId']);
	}
    /**
     * serialize all the components in core
     *
     * @return string
     */
	public static function serialize()
	{
		return serialize(self::$_storage);
	}
	/**
	 * unserialize all the components and store them in Core
	 *
	 * @param string $string The serialized core storage string
	 */
	public static function unserialize($string)
	{
		self::$_storage = unserialize($string);
		Core::setUser(UserAccount::get(self::$_storage['userId']), Role::get(self::$_storage['roleId']), self::$_storage['origPass']);
		return self::$_storage;
	}
	/**
	 * Getting the current library
	 *
	 * @return Library
	 */
	public static function getLibrary()
	{
		return self::$_lib;
	}
	/**
	 * The url of the library
	 *
	 * @param Library $lib The library
	 *
	 * @return Library
	 */
	public static function setLibrary(Library $lib)
	{
		self::$_lib = $lib;
		return self::$_lib;
	}
	/**
	 * Getting the current encrypted Pass
	 *
	 * @return string
	 */
	public static function getOrigPass()
	{
		return self::$_storage['origPass'];
	}
	/**
	 * The url of the library
	 *
	 * @param string $origPass The origPass
	 *
	 * @return string
	 */
	public static function setOrigPass($origPass)
	{
		self::$_storage['origPass'] = $origPass;
		return self::$_storage['origPass'];
	}
}

?>