<?php
/**
 * This is the ReAuth Controller - for the Supplier to re-authenticate the host
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 *
 */
class ReAuthController extends TService
{
	const RESULT_CODE_SUCC = 0;
	const RESULT_CODE_FAIL = 1;
	const RESULT_CODE_IMCOMPLETE = 2;
	const RESULT_CODE_OTHER_ERROR = 3;
	
    /**
     * (non-PHPdoc)
     * @see TService::run()
     */
    public function run()
    {
    	
    	$cdKey = $msg = $siteId = $uId = $pwd = $user_name = $user_mobile = $user_email = "";
    	$response = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><Response />');
        try 
        {
        	//add timestamp
        	$now = new UDate();
        	$response->addAttribute('Time', trim($now));
        	$response->addAttribute('TimeZone',trim($now->getTimeZone()->getName()));
        	
        	$request = $this->_parseRequest();
        	$request_Attributes = $request->attributes();
        	$response->addAttribute('CDkey', trim($request_Attributes['CDKey']));
        	
        	$user_Attributes = $request->User->attributes();
        	$user = $response->addChild('User');
        	$user->addAttribute('libraryId', $user_Attributes['SiteID']);
        	$user->addAttribute('LoginName', $user_Attributes['Uid']);
        	$user->addAttribute('Password', $user_Attributes['Pwd']);
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
        $this->getResponse()->write($response->asXML());
    }
    
    private function _parseRequest()
    {
    	$message = file_get_contents('php://input');
    	$message = str_replace('<94>', '"', $message);
    	return new SimpleXMLElement($message);
    	 
    }
}