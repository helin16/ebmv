<?php
class UserAccountController extends AppControllerAbstract
{
	public function getUsers($params)
	{
		$this->_checkToken($params);
	}
}