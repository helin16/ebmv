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
	public function __construct()
	{
		$this->_importScript = new ProductImportScript('/tmp/');
	}
	
	public function run($url)
	{
		try
		{
			$xml = $this->_downloadXML($url);
			$this->_importPrducts($xml);
		}
		catch(Exception $ex)
		{
			echo $ex->getMessage();
			return;
		}
	}
	
	private function _downloadXML($url)
	{
		$errors = array();
		$filePath = $this->_importScript->getDataFromSoup($url, Config::get('site', 'id'), true, 1, 1000, $errors)->getTmpFile();
		if(count(array_keys($errors)) > 0)
			throw new Exception('Error Page Index: ' . implode(', ', array_keys($errors)));
		if(strlen($content = file_get_contents($filePath)) === 0)
			throw new Exception('Empty Url found!');
		$xml = simplexml_load_file($filePath);
		return $xml;
	}
	
	private function _importPrducts(SimpleXMLElement $xml)
	{
		$this->_importScript->importProduct($xml);
	}
}

$script = new ImportProduct();
$script->run();