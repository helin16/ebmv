<?php
/**
 * The soap authentication server for web services
 * 
 * @package    Web
 * @subpackage Class
 * @author     lhe<helin16@gmail.com>
 */
class WebAuth
{
	/**
	 * Result code for success
	 * @var int
	 */
	const RESULT_CODE_SUCC = 0;
	/**
	 * Result code for fail
	 * @var int
	 */
	const RESULT_CODE_FAIL = 1;
	/**
	 * Result code for imcomplete
	 * @var int
	 */
	const RESULT_CODE_IMCOMPLETE = 2;
	/**
	 * Result code for other error
	 * @var int
	 */
	const RESULT_CODE_OTHER_ERROR = 3;
	/**
	 * Authentication method
	 * 
	 * @param string $CDKey  The scecret key
	 * @param int    $SiteID The library code
	 * @param string $Uid    The username
	 * @param string $Pwd    The hashed password
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
			//get the supplier
			$supplier = $this->_getSupplier($CDKey, $Uid, $SiteID);
			//get the User
			$user = $this->_getUser($SiteID, $Uid, $Pwd);
			
			$response->addAttribute('CDkey', $CDKey);
			$user = $response->addChild('User');
			$user->addAttribute('libraryId', $SiteID);
			$user->addAttribute('LoginName', $Uid);
			
			$user_mobile = $user_email = '';
			$user->addAttribute('Password', $Pwd);
			$user->addAttribute('Name', trim($user->getPerson()));
			$user->addAttribute('Mobile', $user_mobile);
			$user->addAttribute('Email', $user_email);
			$response->addAttribute('ResultCode', self::RESULT_CODE_SUCC);
			$response->addAttribute('Info', '');
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
	private function _getSupplier($CDKey, $Uid, $SiteID)
	{
		//getting the supplier
		$suppliers = BaseServiceAbastract::getInstance('Supplier')->findAll();
		foreach($suppliers as $supplier)
		{
			//getting the supplier's key
			$keys = explode(',', $supplier->getInfo('skey'));
			if(count($keys) === 0 || ($key = trim($keys[0])) === '')
				continue;
			
			$wantedCDKey = strtolower(StringUtilsAbstract::getCDKey($key, $Uid, $SiteID));
			if($wantedCDKey === strtolower(trim($CDKey)))
				return $supplier;
		}
		throw new Exception('Invalid Connection!', self::RESULT_CODE_FAIL);
	}
	/**
	 * Getting the user
	 * 
	 * @param string $libCode
	 * @param string $username
	 * @param string $password
	 * 
	 * @throws Exception
	 * @return UserAccount
	 */
	private function _getUser($libCode, $username, $password)
	{
		$lib = BaseServiceAbastract::getInstance('Library')->getLibFromCode($libCode);
		if (!$lib instanceof Library)
			throw new Exception('No Such a Site/Library!', self::RESULT_CODE_FAIL);
		//getting the user
		try
		{
			$userAccount = BaseServiceAbastract::getInstance('UserAccount')->getUserByUsernameAndPassword($username, $password, $lib, true);
			return $userAccount;
		}
		catch(Exception $ex)
		{
			throw new Exception($ex->getMessage(), self::RESULT_CODE_FAIL);
		}
	}
}