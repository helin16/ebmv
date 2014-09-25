<?php
Prado::using('System.Security.IUserManager');
Prado::using('Application.classes.WebUser.WebUser');
/**
 * Manager for Web Users extends TModule implements IUserManager
 *
 */
class WebUserManager extends TModule implements IUserManager
{
	/**
	 * get the Guest Name
	 *
	 * @return unknown
	 */
	public function getGuestName()
	{
		return 'Guest';
	}

	/**
	 * Get the WebUser
	 *
	 * @param unknown_type $username
	 * @return WebUser
	 */
	public function getUser($username=null)
	{
		if($username === null)
			return new WebUser($this);
		
		if(!($userAccount = (Core::getUser() instanceof UserAccount ? Core::getUser(): BaseServiceAbastract::getInstance('UserAccount')->getUserByUsername($username, Core::getLibrary()))) instanceof UserAccount)
			return null;
		
		$user = new WebUser($this);
		$user->setUserAccount($userAccount);
		$user->setName($userAccount->getUsername());
		$user->setIsGuest(false);
		$user->setRoles($userAccount->getRoles());
		return $user;
	}
	
	/**
	 * validate a user providing $username and $password
	 *
	 * @param string $username
	 * @param string $password
	 * @return true, if there is such a userAccount in the database;otherwise, false;
	 */
	public function validateUser($username, $password)
	{
		if(!Core::getUser() instanceof UserAccount)
		{
			if(!($userAccount = UserAccount::login(Core::getLibrary(), $username, $password)) instanceof UserAccount)
				return false;
		}
		return true;
	}
	
	/**
	 * Save a TUser to cookie
	 *
	 * @param unknown_type $cookie
	 */
	public function saveUserToCookie($cookie)
	{
		// TODO: do nothing at this moment,
		//since we don't support cookie-based auth
	}

	/**
	 * Get a TUser from Cookie
	 *
	 * @param unknown_type $cookie
	 * @return unknown
	 */
	public function getUserFromCookie($cookie)
	{
		// TODO: do nothing at this moment,
		//since we don't support cookie-based auth
		return null;
	}
}
?>