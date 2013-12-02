<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
class ImportProduct
{
	public static function run(array $supplierIds = array(), array $siteIds = array(), $totalRecords = null)
	{
		$totalRecords = trim($totalRecords);
		Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(UserAccount::ID_SYSTEM_ACCOUNT));
		try
		{
			foreach(self::_getSuppliers($supplierIds) as $supplier)
			{
				fwrite(STDOUT,  "== Start import script @ " . new UDate() . " for " . $supplier->getName() . "=============================\r\n");
				try {$script = SupplierConnectorAbstract::getInstance($supplier); }
				catch(Exception $ex) 
				{
					echo $ex->getMessage() . "\n";
					echo $ex->getTraceAsString() . "\n";
					continue;
				}
				fwrite(STDOUT,  "Start to download xml file from supplier.\r\n");
					if($totalRecords === '')
					{
						$pageInfo = $script->getProductListInfo();
						$totalRecords = $pageInfo['totalRecords'];
					}
					$productList = $script->getProductList(1, $totalRecords);
				fwrite(STDOUT,  "xml downloaded.\r\n");
				
				$childrenCount = count($productList);
				fwrite(STDOUT, "Start to import (" . $childrenCount . ") products: \r\n");
				for($i = 0; $i< $childrenCount; $i++)
				{
					fwrite(STDOUT, 'Importing Product No: ' . $i . ' ... ');
					try
					{
						$script->importProducts($productList, $i);
					}
					catch(Exception $ex)
					{
						fwrite(STDOUT, $ex->getMessage());
						continue;
					}
					fwrite(STDOUT, "Done\r\n");
				}
				fwrite(STDOUT, "Finished importing (" . $childrenCount . ") products: \r\n");
				fwrite(STDOUT, "== Finished import script  @ " . new UDate() . "=============================\r\n");
			}
		}
		catch(Exception $ex)
		{
			fwrite(STDOUT, $ex->getMessage() . "\r\n");
			fwrite(STDOUT, $ex->getTraceAsString() . "\r\n");
			return;
		}
	}
	private static function _getSuppliers($supplierIds = array())
	{
		if(!is_array($supplierIds))
			throw new Exception("System Error: supplids has to be a array!");
		if(count($supplierIds) === 0)
			return BaseServiceAbastract::getInstance('Supplier')->findAll();
		return BaseServiceAbastract::getInstance('Supplier')->findByCriteria('id in (' . implode(', ', array_fill(0, count($supplierIds), '?')) . ')', $supplierIds);
	}
}


//checking usage
if ($argc != 4)
	die("Usage: ImportProduct supplierids(1,2,3|all) siteCode(37,werew,121fd|all) totalrecords(30|all)");

foreach ($argv as $k => $v) {
	fwrite(STDOUT, $k+1 . ': ' . $v . "\r\n");
}

// ImportProduct::run(10, array(1));