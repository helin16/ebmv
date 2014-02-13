<?php
class LC_SIP2 extends LibraryConnectorAbstract implements LibraryConn
{
	private static $_cache = array();
	/**
	 * Getting the library user info
	 * 
	 * @param unknown $username
	 * @param unknown $password
	 * 
	 * @return multitype:
	 */
	public static function getPersonInfo(Library $library, $username, $password)
	{
		$key = md5($username . $password);
		if(!isset(self::$_cache[$key]))
		{
			$hostInfo = $library->getInfo('sip2_host');
			$hosts = explode(':', str_replace(' ', '', $hostInfo));
			$result = BmvComSIP2::getSIP($hosts[0], isset($hosts[1]) ? $hosts[1] : null)->getPatronInfo($username, $password);
			$pInfo = array();
			if(strtoupper(trim($result['variable']['BL'][0])) === 'Y' && strtoupper(trim($result['variable']['CQ'][0])) === 'Y')
			{
				$names = explode(' ', trim($result['variable']['AE'][0]));
				$lastName = array_pop($names);
				$firstName = implode(' ', $names);
				$pInfo = LibraryConnectorUser::getUser($library, $username, sha1($password), $firstName, $lastName);
			}
			self::$_cache[$key] = $pInfo;
		}
		return self::$_cache[$key];
	}
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
		return LC_SIP2::getPersonInfo($this->getLibrary(), $username, $password);
	}
	/**
	 * Checking whether the user exists
	 *
	 * @param unknown $username
	 * @param unknown $password
	 *
	 * @return bool
	*/
	public function chkUser($username, $password)
	{
		return ($pInfo = LC_SIP2::getPersonInfo($this->getLibrary(), $username, $password)) instanceof LibraryConnectorUser;
	}
}