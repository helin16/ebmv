<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
class ImportProduct
{
	public static function run(array $libCodes = array(), array $supplierIds = array(), $totalRecords = null)
	{
		$totalRecords = trim($totalRecords);
		Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(UserAccount::ID_SYSTEM_ACCOUNT));
		try
		{
			$startScript = new UDate();
			fwrite(STDOUT,  "== Start import script @ " . $startScript . "=============================\r\n");
			//loop through each library
			Dao::$debug = true;
			$libraries = self::_getLibs($libCodes);
			Dao::$debug = false;
			fwrite(STDOUT,  "  == Found " . count($libraries) . " libraries to go through: \r\n");
			
			foreach($libraries as $lib)
			{
				//loop through each supplier
				foreach(self::_getSuppliers($supplierIds) as $supplier)
				{
					fwrite(STDOUT,  "\r\n  == import from " . $supplier->getName() . "\r\n");
					
					//if there is an error for supplier connector
					try {$script = SupplierConnectorAbstract::getInstance($supplier); }
					catch(Exception $ex) 
					{
						fwrite(STDOUT,  "  :: " . $ex->getMessage() . "\r\n");
						fwrite(STDOUT,  "  :: " . $ex->getTraceAsString() . "\r\n");
						continue;
					}
					
					//getting how many record we need to run
					if($totalRecords === '')
					{
						$pageInfo = $script->getProductListInfo();
						$totalRecords = $pageInfo['totalRecords'];
					}
					fwrite(STDOUT,  "  :: start download the xml ...");
					$productList = $script->getProductList(1, $totalRecords);
					fwrite(STDOUT,  " downloaded.\r\n");
					
					//process each record
					$childrenCount = count($productList);
					fwrite(STDOUT, "  :: Start to import (" . $childrenCount . ") products: \r\n");
					for($i = 0; $i< $childrenCount; $i++)
					{
						fwrite(STDOUT, '    -- Importing Product No: ' . $i . ' ... ');
						try
						{
							$script->importProducts($productList, $i);
							fwrite(STDOUT, "Done");
						}
						catch(Exception $ex)
						{
							fwrite(STDOUT, "ERROR: " . $ex->getMessage());
							continue;
						}
						fwrite(STDOUT, "\r\n");
					}
				}
			}
		}
		catch(Exception $ex)
		{
			fwrite(STDOUT, $ex->getMessage() . "\r\n");
			fwrite(STDOUT, $ex->getTraceAsString() . "\r\n");
		}
		$finishScript = new UDate();
		$scriptRunningtime = $finishScript->diff($startScript);
		fwrite(STDOUT,  "== Finished import script @ " . $finishScript . "(Used: " . $scriptRunningtime->format("%H hours, %I minutes, %S seconds") . ")=============================\r\n");
	}
	private static function _getSuppliers($supplierIds = null)
	{
		if(!is_array($supplierIds))
			throw new Exception("System Error: supplids has to be a array!");
		if($supplierIds === null || count($libCodes) === 0)
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
}


//checking usage
if ($argc != 4)
	die("Usage: ImportProduct siteCode(37,werew,121fd|all) supplierids(1,2,3|all) totalrecords(30|all)\r\n");

$libCodes = (($libCodes = trim($argv[1])) === 'all' ? array() : explode(',', str_replace(' ', '', $libCodes)));
$supplierIds = (($supplierIds = trim($argv[2])) === 'all' ? array() : explode(',', str_replace(' ', '', $supplierIds)));
$totalrecords = (($totalrecords = trim($argv[3])) === 'all' ? null : $totalrecords);

fwrite(STDOUT, "== Params ===================================================\r\n");
fwrite(STDOUT, "== Site Codes: '" . implode("', '", $siteCodes). "'\r\n");
fwrite(STDOUT, "== Supplier IDS: " . implode(', ', $supplierIds). "\r\n");
fwrite(STDOUT, "== Total Records: '" . $totalrecords. "'\r\n");
fwrite(STDOUT, "=============================================================\r\n");

ImportProduct::run($libCodes, $supplierIds, $totalrecords);