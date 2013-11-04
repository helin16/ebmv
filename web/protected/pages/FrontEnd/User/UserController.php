<?php
/**
 * This is the user details page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class UserController extends FrontEndPageAbstract
{
	public function onLoad($param)
	{
		if(!Core::getUser() instanceof UserAccount)
			$this->Response->redirect('/login.html');
	}
	public function logout($sender, $params)
	{
		$auth = $this->getApplication()->Modules['auth'];
		$auth->logout();
		$this->Response->Redirect("/");
	}
}