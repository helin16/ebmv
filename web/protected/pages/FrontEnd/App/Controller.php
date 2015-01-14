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
            if(($version = (isset($this->Request['version']) ? trim($this->Request['version']) : '')) === '')
            	throw new Exception('Version needed.');
            $entity = ((isset($this->Request['entity']) && trim($this->Request['entity']) !== '') ? trim($this->Request['entity']) : '');
            if($entity !== 'getToken') {
            	$base = dirname(__FILE__). DIRECTORY_SEPARATOR . $version;
            	if(!is_dir($base))
            		throw new Exception('Invalid version passed in.');
            	require_once $base. DIRECTORY_SEPARATOR . 'bootstrap.php';
	            $result = $this->_callController($entity, $_REQUEST);
            } else {
	            $results  = $this->_getToken($_REQUEST);
            }
            $response->write(json_encode($results));
        }
        catch (Exception $ex)
        {
	        $response->write(json_encode(array('error' => $ex->getMessage())));
        }
    }
    /**
     * Calls a contoller
     * 
     * @param unknown $version
     * @param unknown $entity
     * @param unknown $params
     * 
     * @throws Exception
     * @return mixed  The result from contoller
     */
    private function _callController($entity, $params)
    {
    	if(($method = (isset($this->Request['method']) ? trim($this->Request['method']) : '')) === '')
    		throw new Exception('method needed.');
    	$class = $entity . 'Controller';
    	try {
    		if(!class_exists($class))
    			throw new Exception($entity . ' NOT Exsitis!');
    	} catch (Exception $e) {
    		throw new Exception($entity . ' NOT Exsitis!');
    	}
    	try {
    		$controller = new $class();
    		if(!method_exists($controller, $method))
    			throw new Exception($method . ' NOT Exsitis!');
    	} catch (Exception $e) {
    		throw new Exception($method . ' NOT Exsitis!');
    	}
    	return $controller->$method($params);
    }
    /**
     * Getting the token
     * 
     * @param unknown $params
     * 
     * @throws Exception
     * @return string
     */
    private function _getToken($params)
    {
    	if(($username = (isset($params['username']) ? trim($params['username']) : '')) === '')
    		throw new Exception('username needed.');
    	if(($password = (isset($params['password']) ? trim($params['password']) : '')) === '')
    		throw new Exception('password needed.');
    	$userAccount = UserAccount::getUserByUsernameAndPassword($username, $password, false);
    	if($userAccount instanceof UserAccount)
    		throw new Exception('Invalide user');
    	$token = md5($username . $password . trim(new UDate()));
    	UserAccount::create('', $token, $userAccount);
    	return array('token', $token);
    }
}