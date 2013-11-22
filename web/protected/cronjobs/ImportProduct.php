<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
class ImportProduct
{
	public static function run($totalRecords = null, array $supplierIds = array())
	{
		$totalRecords = trim($totalRecords);
		Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(UserAccount::ID_SYSTEM_ACCOUNT));
		try
		{
			foreach(self::_getSuppliers($supplierIds) as $supplier)
			{
				echo "== Start import script @ " . new UDate() . " for " . $supplier->getName() . "=============================\n";
				try {$script = SupplierConnectorAbstract::getInstance($supplier); }
				catch(Exception $ex) 
				{
					echo $ex->getMessage() . "\n";
					echo $ex->getTraceAsString() . "\n";
					continue;
				}
				echo "Start to download xml file from supplier.\n";
					if($totalRecords === '')
					{
						$pageInfo = $script->getProductListInfo();
						$totalRecords = $pageInfo['totalRecords'];
					}
					$productList = $script->getProductList(1, $totalRecords);
				echo "xml downloaded.\n";
				
				$childrenCount = count($productList);
				echo "Start to import (" . $childrenCount . ") products: \n";
				for($i = 0; $i< $childrenCount; $i++)
				{
					echo 'Importing Product No: ' . $i . ' ... ';
					$script->importProducts($productList, $i);
					echo "Done\n";
				}
				echo "Finished importing (" . $childrenCount . ") products: \n";
				echo "== Finished import script  @ " . new UDate() . "=============================\n";
			}
		}
		catch(Exception $ex)
		{
			echo $ex->getMessage() . "\n";
			echo $ex->getTraceAsString() . "\n";
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

ImportProduct::run();