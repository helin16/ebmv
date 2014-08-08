<?php
class SIPTesterController extends AdminPageAbstract 
{
	/**
	 * The selected Menu Item name
	 *
	 * @var string
	 */
	public $menuItemCode = 'testsip';
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'pageJs.resultDivId = "resultdiv";';
		$js .= 'pageJs.setCallbackId("testSIP", "' . $this->testBtn->getUniqueID() . '");';
		return $js;
	}
    /**
     * (non-PHPdoc)
     * @see CrudPageAbstract::getItems()
     */
    public function testSIP($sender, $param)
    {
    	$result = $errors = $supplierArray = array();
    	try
    	{
    		$testData = json_decode(json_encode($param->CallbackParameter->testdata), true);
    		
    		if(!isset($testData['Server']) || ($server = trim($testData['Server'])) === '')
    			throw new Exception("Server needed!");
    		
    		$urls = parse_url($server);
    		if(!isset($urls['host']) || ($host = trim($urls['host'])) === '')
    			throw new Exception("Invalid url for host!");
    		if(!isset($urls['port']) || ($port = trim($urls['port'])) === '')
    			throw new Exception("Invalid url for port!");
    		
    		if(!isset($testData['patron']) || ($patron = trim($testData['patron'])) === '')
    			throw new Exception("patron needed!");
    		if(!isset($testData['patronpwd']) || ($patronpwd = trim($testData['patronpwd'])) === '')
    			throw new Exception("patronpwd needed!");
    		$mysiplocation = !isset($_REQUEST['siplocation']) ? '' : trim($_REQUEST['siplocation']);
    		
    		$i = 0;
    		$logs = array();
    		$mysip = new SIP2();
    		$logs[$i++]['title'] = 'Initialising SIP object ...';
    		$logs[$i]['info'] = array();
    		// Set host name
    		$mysip->hostname = $host;
    		$logs[$i]['info'][] = ':: Assigin the host: ' . $host;
    		$mysip->port = $port;
    		$logs[$i]['info'][] = ':: Assigin the port: ' . $port;
    		// Identify a patron
    		$mysip->patron = $patron;
    		$logs[$i]['info'][] = ':: Assigin the patron: ' . $patron;
    		$mysip->patronpwd = $patronpwd;
    		$logs[$i]['info'][] = ':: Assigin the patronpwd: ' . $patronpwd;
    		$mysip->scLocation = $mysiplocation;
    		$logs[$i]['info'][] = ':: Assigin the scLocation: ' . $mysiplocation;
    		
    		// connect to SIP server
    		$logs[$i++]['title'] = 'Initialiszing the connection to: ' . $server;
    		$result = $mysip->connect();
    		$logs[$i]['info'] = array();
    		$logs[$i]['info'][] = ':: Got Results: ';
    		$logs[$i]['info'][] = print_r($result, true);
    		
    		// login into SIP server
    		$logs[$i++]['title'] = 'login into SIP server:' . $server;
    		$logs[$i]['info'] = array();
			$in = $mysip->msgLogin($mysip->patron, $mysip->patronpwd);
    		$logs[$i]['info'][] = ':: login response from server: ';
    		$logs[$i]['info'][] = print_r($in, true);
    		$rawResp = $mysip->get_message($in);
    		$result = $mysip->parseLoginResponse($rawResp);
    		$logs[$i]['info'][] = ':: RAW Response: ' . $rawResp;
    		$logs[$i]['info'][] = ':: Formatted Response: ';
    		$logs[$i]['info'][] = print_r($result, true);
    		
    		// selfcheck status mesage
    		$logs[$i++]['title'] = 'Requesting Self-checking:';
    		$logs[$i]['info'] = array();
    		$in = $mysip->msgSCStatus();
    		$logs[$i]['info'][] = ':: Self check response from server: ';
    		$logs[$i]['info'][] = print_r($in, true);
    		$rawResp = $mysip->get_message($in);
    		$result = $mysip->parseACSStatusResponse($rawResp);
    		$logs[$i]['info'][] = ':: RAW Response: ' . $rawResp;
    		$logs[$i]['info'][] = ':: Formatted Response: ';
    		$logs[$i]['info'][] = print_r($result, true);
    		
    		//getting AO & AN
    		$logs[$i]['info'][] = ':: Trying to assign AO: ';
    		if(isset($result['variable']['AO']) && isset($result['variable']['AO'][0]))
    		{
    			$mysip->AO = $result['variable']['AO'][0]; /* set AO to value returned */
    			$logs[$i]['info'][] = ':: GOT AO: ' . $mysip->AO;
    		}
    		$logs[$i]['info'][] = ':: Trying to assign AN: ';
    		if(isset($result['variable']['AN']) && isset($result['variable']['AN'][0]))
    		{
    			$mysip->AN = $result['variable']['AN'][0]; /* set AN to value returned */
    			$logs[$i]['info'][] = ':: GOT AN: ' . $mysip->AN;
    		}
    		
    		// Get Charged Items Raw response
    		$logs[$i++]['title'] = ' Get Charged Items Raw response:';
    		$logs[$i]['info'] = array();
    		$in = $mysip->msgPatronInformation('none');
    		$logs[$i]['info'][] = ':: Get Response for PatronInformation: ';
    		$logs[$i]['info'][] =  print_r($in, true);
    		$rawResp = $mysip->get_message($in);
    		$logs[$i]['info'][] = ':: RAW Response: ' . $rawResp;
    		$logs[$i]['info'][] = ':: Formatted Response: ';
    		$result = $mysip->parsePatronInfoResponse($rawResp);
    		$logs[$i]['info'][] = print_r($result, true);
    		
    		$result['logs'] = $logs;
    	}
    	catch(Exception $ex)
    	{
    		$errors[] = $ex->getMessage();
    	}
    	$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
    }
}

?>