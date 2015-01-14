<?php
/**
 * This is the app Service
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 *
 */
class Controller extends TService
{
    /**
     * (non-PHPdoc)
     * @see TService::run()
     */
    public function run()
    {
        $response = $this->getResponse();
        $response->appendHeader('Content-Type: application/json');
        try 
        {
            $method = '_' . ((isset($this->Request['method']) && trim($this->Request['method']) !== '') ? trim($this->Request['method']) : '');
            if(!method_exists($this, $method))
                throw new Exception('No such a method: ' . $method . '!');
          	
            $results = $this->$method($_REQUEST);
            $response->write(json_encode($results));
        }
        catch (Exception $ex)
        {
	        $response->write(json_encode(array('error' => $ex->getMessage())));
        }
    }
    
    private function _authenticate($params)
    {
    	$username = $params['username'];
    	$password = $params['password']; //sha1() encrypted
    	$userAccount = UserAccount::getUserByUsernameAndPassword($username, $password, false);
    	if($userAccount instanceof UserAccount)
    		throw new Exception('Invalide user');
    	$token = md5($username . $password . trim(new UDate()));
    	UserAccount::create('', $token, $userAccount);
    	return $token;
    }
    private function _checkToken($param)
    {
    	$token = $params['token'];
    	$userAccount = UserAccount::getUserByUsernameAndPassword('', $token, false);
    	if($userAccount instanceof UserAccount)
    		throw new Exception('Invalide user');
    	return $userAccount;
    }
}