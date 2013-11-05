<?php
class WebAuth
{
	const RESULT_CODE_SUCC = 0;
	const RESULT_CODE_FAIL = 1;
	const RESULT_CODE_IMCOMPLETE = 2;
	const RESULT_CODE_OTHER_ERROR = 3;
	/**
	 * Authentication method
	 * 
	 * @param string $CDKey
	 * @param int    $SiteID
	 * @param string $Uid
	 * @param string $Pwd
	 * 
	 * @return string
	 * @soapmethod
	 */
	public function authenticate($CDKey, $SiteID, $Uid, $Pwd)
	{
		//add timestamp
		$now = new UDate();
		$response = new SimpleXMLElement('<Response />');
		$response->addAttribute('Time', trim($now));
		$response->addAttribute('TimeZone',trim($now->getTimeZone()->getName()));
		try
		{
			//check details completion
			if(trim($CDKey) === '' || trim($SiteID) === '' || trim($Uid) === '' || trim($Pwd) === '')
				throw new Exception('Incomplete, more details needed!',self::RESULT_CODE_IMCOMPLETE);
			
			$supplier = $this->_getSupplier($CDkey);
			$user = $this->_getUser($SiteID, $Uid, $Pwd);
			
			$response->addAttribute('CDkey', $CDKey);
			$user = $response->addChild('User');
			$user->addAttribute('libraryId', $SiteID);
			$user->addAttribute('LoginName', $Uid);
			
			$user_name = $user_mobile = $user_email = $msg = '';
			$user->addAttribute('Password', $Pwd);
			$user->addAttribute('Name', $user_name);
			$user->addAttribute('Mobile', $user_mobile);
			$user->addAttribute('Email', $user_email);
			$response->addAttribute('ResultCode', self::RESULT_CODE_SUCC);
			$response->addAttribute('Info', $msg);
		}
		catch (Exception $ex)
		{
			$response->addAttribute('ResultCode', $ex->getCode());
			$response->addAttribute('Info', trim($ex->getMessage()));
		}
		return $response->asXML();
	}
	/**
	 * validating the CDKey
	 * 
	 * @param string $CDkey The secrect
	 * 
	 * @throws Exception
	 * @return Ambigous <Supplier, NULL>
	 */
	private function _getSupplier($CDkey)
	{
		//getting the supplier
		$supplier = BaseServiceAbastract::getInstance('Supplier')->get(1);;
		if(!$supplier instanceof Supplier)
			throw new Exception('Unauthorized connection!',self::RESULT_CODE_OTHER_ERROR);
		//getting the supplier's key
		$keys = explode(',', $supplier->getInfo('skey'));
		if(($key = trim($keys[0])) === '')
			throw new Exception('Unauthorized connection with supplier settings!',self::RESULT_CODE_OTHER_ERROR);
		if(($wantedCDKey = md5($key . $Uid . $SiteID)) !== trim($CDKey))
			throw new Exception('Invalid Connection!',self::RESULT_CODE_FAIL);
		return $supplier;
	}
	private function _getUser($siteId, $username, $password)
	{
		//getting the user
		$userAccount = BaseServiceAbastract::getInstance('UserAccount')->getUserByUsernameAndPassword($username, $password);
		if(!$userAccount instanceof UserAccount)
			throw new Exception('No UserAccount found!', self::RESULT_CODE_FAIL);
		return $userAccount;
	}
}