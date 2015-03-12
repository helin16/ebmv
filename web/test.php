<?php
require_once 'bootstrap.php';
class test
{
	private static $fromLocalDB = false;
	/**
	 * login for the library reader
	 *
	 * @param Library $lib
	 * @param unknown $username
	 * @param unknown $password
	 */
	public static function login(Library $lib, $libCardNo, $password)
	{

		if (! Core::getUser () instanceof UserAccount)
			Core::setUser ( UserAccount::get ( UserAccount::ID_SYSTEM_ACCOUNT ) );
		if(self::$fromLocalDB === true)
		{
			$userAccount = UserAccount::getUserByUsernameAndPassword($libCardNo, $password, $lib);
			// check whether the library has the user or not
			if (! $userAccount instanceof UserAccount)
				throw new CoreException ( 'Invalid login please contact ebmv admin!' );
		}
		else
		{
			// check whether the library has the user or not
			if (! LibraryConnectorAbstract::getScript ($lib)->chkUser ( $libCardNo, $password ))
				throw new CoreException ( 'Invalid login please contact your library!' );

			// get the information from the library system
			$userInfo = LibraryConnectorAbstract::getScript ($lib)->getUserInfo ( $libCardNo, $password );
			// check whether our local db has the record already
			if (($userAccount = UserAccount::getUserByUsername ( $libCardNo, $lib )) instanceof UserAccount) {
				$person = $userAccount->getPerson();
				$userAccount = UserAccount::updateUser ( $userAccount, $lib, $userInfo->getUsername (), $userInfo->getPassword (), null, Person::createNudpatePerson( $userInfo->getFirstName (), $userInfo->getLastName(), $person ) );
			} else {		// we need to create a user account from blank
				$userAccount = UserAccount::createUser ( $lib, $userInfo->getUsername (), $userInfo->getPassword (), Role::get(Role::ID_READER), Person::createNudpatePerson( $userInfo->getFirstName (), $userInfo->getLastName() ) );
			}
		}

		$role = null;
		if (! Core::getRole () instanceof Role)
		{
			if (count ( $roles = $userAccount->getRoles () ) > 0)
				$role = $roles [0];
		}
		Core::setUser($userAccount, $role);
		return $userAccount;
	}
}
$userAccount = test::login(Library::get(3), '11380047', '1234');
var_dump($userAccount);

?>