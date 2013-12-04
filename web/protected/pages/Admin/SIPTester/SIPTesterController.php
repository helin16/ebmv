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
    		if(!isset($testData['url']) || trim($testData['url']) === '')
    			throw new Exception("URL needed!");
    		$urls = parse_url($url = trim($testData['url']));
    		if(!isset($urls['host']) || trim($urls['host']) === '')
    			throw new Exception("Invalid url for host!");
    		$host = trim($urls['host']);
    		if(!isset($urls['port']) || trim($urls['port']) === '')
    			throw new Exception("Invalid url for port!");
    		$port = trim($urls['port']);
    		
    		if(!isset($testData['varname']) || trim($testData['varname']) === '')
    			throw new Exception("varname needed!");
    		if(!isset($testData['patron']) || trim($testData['patron']) === '')
    			throw new Exception("patron needed!");
    		if(!isset($testData['patronpwd']) || trim($testData['patronpwd']) === '')
    			throw new Exception("patronpwd needed!");
    		
    		$varName = trim($testData['varname']);
    		$patron = trim($testData['patron']);
    		$patronpwd = trim($testData['patronpwd']);
    		
    		$result['logs'] = array();
    		$sip = new SIP2();
    		$result['logs'][] = 'Initialising SIP object ...';
    		$sip->hostname = $host;
    		$result['logs'][] = ':: Assigin the host: ' . $host;
    		$sip->port  = $port;
    		$result['logs'][] = ':: Assigin the port: ' . $port;
    		
    		// connect to SIP server
    		$result = $sip->connect();
    		$result['logs'][] = '';
    		$result['logs'][] = 'Initialiszing the connection to: ' . $url;
    		
    		//send selfcheck status message
    		$result['logs'][] = ':: Getting the self checking status msg';
    		$in = $mysip->msgSCStatus();
    		$result['logs'][] = ':: passing the self checking with status: ' . $in;
    		$result = $mysip->parseACSStatusResponse($mysip->get_message($in));
    		$result['logs'][] = ':: done: ' . print_r($result, true);
    		
    		//Use result to populate SIP2 setings
    		//	(In the real world, you should check for an actual value
    		//	before trying to use it... but this is a simple example)
    		$mysip->AO = $result[$varName]['AO'][0]; /* set AO to value returned */
    		$result['logs'][] = ':: Using the result to check the AO of variable: ' . $mysip->AO;
    		$mysip->AN = $result[$varName]['AN'][0]; /* set AN to value returned */
    		$result['logs'][] = ':: Using the result to check the AN of variable: ' . $mysip->AN;
    		
    		// Identify a patron
    		$mysip->patron = $patron;
    		$mysip->patronpwd = $patronpwd;
    		$result['logs'][] = 'Identifying the patron: ' . $mysip->patron . ' | ' . $mysip->patronpwd;
    		
    		// Get Charged Items Raw response
    		$in = $mysip->msgPatronInformation('charged');
    		$result['logs'][] = 'Get Charged Items Raw response: ' . $in;
    		
    		// parse the raw response into an array
    		$result['logs'][] = 'Parse the raw response into an array: ';
			$result = $mysip->parsePatronInfoResponse( $mysip->get_message($in) );
    		$result['logs'][] = ':: Get result:' . print_r($result, true);
    	}
    	catch(Exception $ex)
    	{
    		$errors[] = $ex->getMessage();
    	}
    	$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
    }
}

?>