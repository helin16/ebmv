<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
class ImportProduct
{
	/**
	 * @var SupplierConnector
	 */
	private $_importScript;
	/**
	 * constructor
	 * @throws Exception
	 */
	public function __construct(Supplier $supplier)
	{
		$this->_importScript = new SupplierConnector($supplier);
	}

	public function run()
	{
		try
		{
			echo "== Start import script @ " . new UDate() . "=============================\n";
			$xml = $this->_downloadXML();
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

	private function _downloadXML()
	{
		$errors = array();
		echo "Start to download xml file from : " . $this->_importScript->getImportUrl() . " by soap.\n";
		$pageInfo = $this->_importScript->getProductListInfo();
		$xml = $this->_importScript->getProductList(1, $pageInfo['totalRecords']);
		echo "xml downloaded.\n";
		return $xml;
	}

	private function _importPrducts(SimpleXMLElement $xml)
	{
		$childrenCount = count($xml->children());
		echo "Start to import (" . $childrenCount . ") products: \n";
		for($i = 0; $i< $childrenCount; $i++)
		{
			echo 'Importing Product No: ' . $i . ' ... ';
			$this->_importScript->importProductFromXml($xml, $i);
			echo "Done\n";
		}
		echo "Finished importing (" . $childrenCount . ") products: \n";
	}
}

Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(UserAccount::ID_SYSTEM_ACCOUNT));
foreach(BaseServiceAbastract::getInstance('Supplier')->findAll() as $supplier)
{
	$script = new ImportProduct($supplier);
	$script->run();
}