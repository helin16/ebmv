<?php
class AppControllerAbstract
{
	
	protected function _checkToken($params)
	{
		if(($token = (isset($params['token']) ? trim($params['token']) : '')) === '')
			throw new Exception('token needed.');
		$userAccount = UserAccount::getUserByUsernameAndPassword('', $token, false);
		if($userAccount instanceof UserAccount)
			throw new Exception('Invalid user');
		return $userAccount;
	}
}