<?php
/**
 * This is the loginpage
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class HomeController extends TPage  
{
    /**
     * (non-PHPdoc)
     * @see TControl::onLoad()
     */
	public function onLoad($param)
	{
	    $this->getPage()->getClientScript()->registerStyleSheetFile('loginCss', $this->publishAsset(__CLASS__ . '.css'));
	    if(Core::getUser() instanceof UserAccount)
	        $this->Response->redirect('/');
	}
	/**
     * Validates whether the username and password are correct.
     * This method responds to the TCustomValidator's OnServerValidate event.
     * 
     * @param mixed $sender    The event sender
     * @param mixed $parameter The event parameters
     */
    public function validateUser($sender,$param)
    {
    	$authManager=$this->Application->getModule('auth');
    	try
    	{
			if($authManager->login($this->username->Text, $this->password->Text))
				$this->Response->redirect('/');
    	}
    	catch(AuthenticationException $ex)
    	{
    	}
    }
}
?>