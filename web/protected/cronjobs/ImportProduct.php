<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
class ImportProduct
{
	public static function run($totalRecords = null)
	{
		$totalRecords = trim($totalRecords);
		Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(UserAccount::ID_SYSTEM_ACCOUNT));
		try
		{
			foreach(BaseServiceAbastract::getInstance('Supplier')->findAll() as $supplier)
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
}

ImportProduct::run();