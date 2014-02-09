<?php
class LC_Local extends LibraryConnectorAbstract
{
	/**
	 * Getting the user information for a user
	 *
	 * @param unknown $username
	 * @param unknown $password
	 *
	 * @return LibraryConnectorUser
	 */
	public function getUserInfo($username, $password)
	{
		$userAccount = BaseServiceAbastract::getInstance('UserAccount')->getUserByUsernameAndPassword($username, $password, $this->getLibrary());
		return LibraryConnectorUser::getUser($this->getLibrary(), $userAccount->getUserName(), $userAccount->getPassword(), $userAccount->getPerson()->getFirstName(), $userAccount->getPerson()->getLastName());
	}
	/**
	 * Checking whether the user exists
	 * 
	 * @param unknown $username
	 * @param unknown $password
	 */
	public function chkUser($username, $password)
	{
		return BaseServiceAbastract::getInstance('UserAccount')->getUserByUsernameAndPassword($username, $password, $this->getLibrary()) instanceof UserAccount;
	}
}