<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
class ImportProduct
{
	public static function run(array $libCodes = array(), array $supplierIds = array(), $totalRecords = null)
	{
		$totalRecords = trim($totalRecords);
		$fullUpdate = ($totalRecords === '');
		Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(UserAccount::ID_SYSTEM_ACCOUNT));
		try
		{
			$startScript = new UDate();
			self::log( "== Start import script @ " . $startScript . "=============================\r\n");
			
			//loop through each library
			$libraries = self::_getLibs($libCodes);
			self::log( "  == Found " . count($libraries) . " libraries to go through: \r\n");
			foreach($libraries as $lib)
			{
				Core::setLibrary($lib);
				//loop through each supplier
				foreach(self::_getSuppliers($supplierIds) as $supplier)
				{
					self::log( "\r\n  == import from " . $supplier->getName() . "\r\n");
					
					//if there is an error for supplier connector
					try {$script = SupplierConnectorAbstract::getInstance($supplier, Core::getLibrary()); }
					catch(Exception $ex) 
					{
						self::log( "  :: " . $ex->getMessage() . "\r\n");
						self::log( "  :: " . $ex->getTraceAsString() . "\r\n");
						continue;
					}
					
					$types = $script->getImportProductTypes();
					self::log( "  :: Got (" . count($types) . ") types to import:\r\n");
					foreach($types as $type)
					{
						//getting how many record we need to run
						self::log( "  :: start download the xml for "  .$type->getName() ."...");
						$productList = $script->getProductList(1, trim($totalRecords), $type);
						self::log( " downloaded.\r\n");
						
						//process each record
						$childrenCount = count($productList);
						self::log("  :: Start to import (" . $childrenCount . ") products: \r\n");
						for($i = 0; $i< $childrenCount; $i++)
						{
							self::log("\r\n");
							self::log('    -- Importing Product No: ' . $i . " ... \r\n");
							try
							{
								self::log("    -- xml: \r\n");
								self::log("    -- " . ($productList[$i] instanceof SimpleXMLElement ? $productList[$i]->asXml() : $productList[$i]) . "\r\n" );
								$script->importProducts($productList, $i);
								self::log("    -- Done\r\n");
							}
							catch(Exception $ex)
							{
								self::log("ERROR: " . $ex->getMessage());
								continue;
							}
							self::log("\r\n");
						}
						
						//removing the un-imported products
						$ids = $supplier->getProducts($script->getImportedProductIds());
						if($fullUpdate === true && count($ids) > 0)
						{
							self::log( "  :: removing un-imported (" . count($ids) . ") product ids: " . implode(', ', $ids) . "\r\n");
							$script->rmUnImportedProducts();
							self::log( "  :: done removing un-imported products. \r\n");
						}
					}
				}
			}
		}
		catch(Exception $ex)
		{
			self::log($ex->getMessage() . "\r\n");
			self::log($ex->getTraceAsString() . "\r\n");
		}
		$finishScript = new UDate();
		$scriptRunningtime = $finishScript->diff($startScript);
		self::log( "== Finished import script @ " . $finishScript . "(Used: " . $scriptRunningtime->format("%H hours, %I minutes, %S seconds") . ")=============================\r\n");
	}
	private static function _getSuppliers($supplierIds = null)
	{
		if(!is_array($supplierIds))
			throw new Exception("System Error: supplids has to be a array!");
		if($supplierIds === null || count($supplierIds) === 0)
			return BaseServiceAbastract::getInstance('Supplier')->findAll();
		return BaseServiceAbastract::getInstance('Supplier')->findByCriteria('id in (' . implode(', ', array_fill(0, count($supplierIds), '?')) . ')', $supplierIds);
	}
	private static function _getLibs($libCodes = null)
	{
		if(!is_array($libCodes))
			throw new Exception("System Error: lib has to be a array!");
		if($libCodes === null || count($libCodes) === 0)
			return BaseServiceAbastract::getInstance('Library')->findAll();
		return BaseServiceAbastract::getInstance('Library')->getLibsFromCodes($libCodes);
	}
	public function log($msg)
	{
		fwrite(STDOUT, $msg);
	}
}

//checking usage
if ($argc != 4)
	die("Usage: ImportProduct siteCode(37,werew,121fd|all) supplierids(1,2,3|all) totalrecords(30|all)\r\n");

$libCodes = (($libCodes = trim($argv[1])) === 'all' ? array() : explode(',', str_replace(' ', '', $libCodes)));
$supplierIds = (($supplierIds = trim($argv[2])) === 'all' ? array() : explode(',', str_replace(' ', '', $supplierIds)));
$totalrecords = (($totalrecords = trim($argv[3])) === 'all' ? null : $totalrecords);

ImportProduct::log("== Params ===================================================\r\n");
ImportProduct::log("== Site Codes: '" . implode("', '", $libCodes). "'\r\n");
ImportProduct::log("== Supplier IDS: " . implode(', ', $supplierIds). "\r\n");
ImportProduct::log("== Total Records: '" . $totalrecords. "'\r\n");
ImportProduct::log("=============================================================\r\n");

ImportProduct::run($libCodes, $supplierIds, $totalrecords);