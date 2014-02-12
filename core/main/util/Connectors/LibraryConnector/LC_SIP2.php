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
	public static function getPersonInfo($username, $password)
	{
		$key = md5($username . $password);
		var_dump(__FUNCTION__ . '1');
		if(!isset(self::$_cache[$key]))
		{
		var_dump(__FUNCTION__ . '2');
		var_dump($this->_lib);
		var_dump($this->_lib->getId());
			$library = $this->_lib;
		var_dump(__FUNCTION__ . '4');
			$hostInfo = $library->getInfo('sip2_host');
		var_dump(__FUNCTION__ . '5');
			$hosts = explode(':', str_replace(' ', '', $hostInfo));
			var_dump($hosts);
			$result = BmvComSIP2::getSIP($hosts[0], isset($hosts[1]) ? $hosts[1] : null)->getPatronInfo($username, $password);
			$pInfo = array();
			if(strtoupper(trim($result['variable']['BL'])) === 'Y' && strtoupper(trim($result['variable']['CQ'])) === 'Y')
			{
				$names = explode(' ', trim($result['variable']['AE']));
				$lastName = array_pop($names);
				$firstName = implode(' ', $names);
				$pInfo = LibraryConnectorUser::getUser($library, $username, $password, $firstName, $lastName);
			}
			self::$_cache[$key] = $pInfo;
		var_dump(__FUNCTION__ . '3');
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
		return LC_SIP2::getPersonInfo($username, $password);
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
		return ($pInfo = LC_SIP2::getPersonInfo($username, $password)) instanceof LibraryConnectorUser;
	}
}