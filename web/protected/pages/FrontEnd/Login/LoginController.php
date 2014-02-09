<?php
class LoginController extends FrontEndPageAbstract
{
    protected function _getEndJs()
    {
        $js = parent::_getEndJs();
        $js .= 'pageJs.setCallbackId("login", "' . $this->loginBtn->getUniqueID() . '");';
        return $js;
    }
    
    public function login($sender, $params)
    {
        $errors = $results = array();
        try 
        {
            if(!isset($params->CallbackParameter->username) || ($username = trim($params->CallbackParameter->username)) === '')
                throw new Exception('username not provided!');
            if(!isset($params->CallbackParameter->password) || ($password = trim($params->CallbackParameter->password)) === '')
                throw new Exception('password not provided!');
            
            $authManager=$this->getApplication()->getModule('auth');
            if(!$authManager->login($username, $password))
            	throw new Exception('Invalid username or password!');
            if(Core::getRole() instanceof Role && trim(Core::getRole()->getId()) === trim(role::ID_ADMIN))
            	$results['url'] = '/admin/';
            else
            	$results['url'] = '/user.html';
        }
        catch(Exception $ex)
        {
        	$errors[] = $ex->getMessage();
        }
        $params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
    }
}