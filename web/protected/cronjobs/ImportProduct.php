<?php
require_once dirname(__FILE__) . '/../main/bootstrap.php';
class ImportProduct
{
	/**
	 * @var ProductImportScript
	 */
	private $_importScript;
	/**
	 * constructor
	 * @throws Exception
	 */
	public function __construct($tmpDir)
	{
		$this->_importScript = new ProductImportScript($tmpDir);
	}
	
	public function run($url)
	{
		try
		{
			echo "== Start import script @ " . new UDate() . "=============================\n";
			$xml = $this->_downloadXML($url);
			$this->_importPrducts($xml);
			echo "== Finished import script  @ " . new UDate() . "=============================\n";
		}
		catch(Exception $ex)
		{
			echo $ex->getMessage() . "\n";
			echo $ex->getTraceAsString() . "\n";
			return;
		}
	}
	
	private function _downloadXML($url)
	{
		$errors = array();
		echo "Start to download xml file from : " . $url . " by soap.\n";
		$filePath = $this->_importScript->getDataFromSoup($url, Config::get('site', 'id'), true, 1, 1000, $errors)->getTmpFile();
		if(count(array_keys($errors)) > 0)
			throw new Exception('Error Page Index: ' . implode(', ', array_keys($errors)));
		if(strlen($content = file_get_contents($filePath)) === 0)
			throw new Exception('Empty Url found!');
		echo "xml downloaded to:" . $filePath . ".\n";
		$xml = simplexml_load_file($filePath);
		return $xml;
	}
	
	private function _importPrducts(SimpleXMLElement $xml)
	{
		$childrenCount = count($xml->children());
		echo "Start to import (" . $childrenCount . ") products: \n";
		$this->_importScript->parseXmltoProduct($xml);
		echo "Finished importing (" . $childrenCount . ") products: \n";
	}
}

Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(100));
$script = new ImportProduct(dirname(__FILE__) . '/../../assets');
$script->run("http://au.xhestore.com/AULibService.asmx?wsdl");