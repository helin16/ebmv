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
		//check details completion
		if(trim($CDKey) === '' || trim($SiteID) === '' || trim($Uid) === '' || trim($Pwd) === '')
		{
			$response->addAttribute('ResultCode', self::RESULT_CODE_IMCOMPLETE);
			$response->addAttribute('Info', 'Incomplete, more details needed!');
			return $response->asXML();
		}
		//check user
		if(trim($SiteID) !== '37' || trim($Uid) !== 'test_user' || trim($Pwd) === 'test_pass')
		{
			$response->addAttribute('ResultCode', self::RESULT_CODE_FAIL);
			$response->addAttribute('Info', 'No such a user!');
			return $response->asXML();
		}
		
		$response->addAttribute('CDkey', $CDKey);
		$user = $response->addChild('User');
		$user->addAttribute('libraryId', $SiteID);
		$user->addAttribute('LoginName', $Uid);
		
		try
		{
			
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
			$response->addAttribute('ResultCode', self::RESULT_CODE_OTHER_ERROR);
			$response->addAttribute('Info', trim($ex->getMessage()));
		}
		return $response->asXML();
	}
	/**
	 * @param string $params
	 * 
	 * @return string
	 * @soapmethod
	 */
	public function helloWorld($params)
	{
		return 'hello, world!';
	}
}