<?php
class ImportProduct
{
	const FLAG_START = 'Import Start';
	const FLAG_END = 'Import END';
	/**
	 * Getting the trans id from the log
	 * 
	 * @param string $salt
	 * 
	 * @return string
	 */
	public static function getLogTransId($salt = '')
	{
		return Log::getTransKey($salt);
	}
	/**
	 * The runner
	 * 
	 * @param array  $libCodes
	 * @param array  $supplierIds
	 * @param string $totalRecords
	 * 
	 * @return string
	 */
	public static function run(array $libCodes = array(), array $supplierIds = array(), $totalRecords = null)
	{
		$totalRecords = trim($totalRecords);
		$fullUpdate = ($totalRecords === '');
		
		if(!Core::getUser() instanceof UserAccount)
			Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(UserAccount::ID_SYSTEM_ACCOUNT));
		try
		{
			$startScript = new UDate();
			self::log( "== Start import script @ " . $startScript . "=============================", self::FLAG_START);
			
			//loop through each library
			$libraries = self::_getLibs($libCodes);
			self::log( "  == Found " . count($libraries) . " libraries to go through:");
			foreach($libraries as $lib)
			{
				Core::setLibrary($lib);
				//loop through each supplier
				foreach(self::_getSuppliers($supplierIds) as $supplier)
				{
					self::log( "== import from " . $supplier->getName());
					
					//if there is an error for supplier connector
					try {$script = SupplierConnectorAbstract::getInstance($supplier, Core::getLibrary()); }
					catch(Exception $ex) 
					{
						self::log( "  :: " . $ex->getMessage() . ". Trace: " . $ex->getTraceAsString());
						continue;
					}
					
					$types = $script->getImportProductTypes();
					self::log( "  :: Got (" . count($types) . ") types to import:\r\n");
					foreach($types as $type)
					{
						//getting how many record we need to run
						self::log( "  :: start download the xml for "  .$type->getName() ."...");
						$productList = $script->getProductList(1, trim($totalRecords), $type);
						self::log( " downloaded.");
						
						//process each record
						$childrenCount = count($productList);
						self::log("  :: Start to import (" . $childrenCount . ") products:");
						for($i = 0; $i< $childrenCount; $i++)
						{
							self::log('    -- Importing Product No: ' . $i . " ... ");
							try
							{
								self::log("    -- xml: " . ($productList[$i] instanceof SimpleXMLElement ? $productList[$i]->asXml() : $productList[$i]) );
								$script->importProducts($productList, $i);
								self::log("    -- Done");
							}
							catch(Exception $ex)
							{
								self::log("ERROR: " . $ex->getMessage() . ', Trace: ' . $ex->getTraceAsString());
								continue;
							}
						}
						
						//removing the un-imported products
// 						$ids = $supplier->getProducts($script->getImportedProductIds());
// 						if($fullUpdate === true && count($ids) > 0)
// 						{
// 							self::log( "  :: removing un-imported (" . count($ids) . ") product ids: " . implode(', ', $ids), $script);
// 							$script->rmUnImportedProducts();
// 							self::log( "  :: done removing un-imported products.", $script);
// 						}
					}
				}
			}
		}
		catch(Exception $ex)
		{
			self::log('Import Script Error: ' . $ex->getMessage() . '. Trace: ' . $ex->getTraceAsString());
		}
		$finishScript = new UDate();
		$scriptRunningtime = $finishScript->diff($startScript);
		self::log( "== Finished import script @ " . $finishScript . "(Used: " . $scriptRunningtime->format("%H hours, %I minutes, %S seconds") . ")=============================", self::FLAG_END);
		return self::getLogTransId();
	}
	/**
	 * Getting the suppliers
	 * 
	 * @param string $supplierIds
	 * 
	 * @throws Exception
	 * @return Ambigous <Ambigous, multitype:, multitype:BaseEntityAbstract >
	 */
	private static function _getSuppliers($supplierIds = null)
	{
		if(!is_array($supplierIds))
			throw new Exception("System Error: supplids has to be a array!");
		if($supplierIds === null || count($supplierIds) === 0)
			return BaseServiceAbastract::getInstance('Supplier')->findAll();
		return BaseServiceAbastract::getInstance('Supplier')->findByCriteria('id in (' . implode(', ', array_fill(0, count($supplierIds), '?')) . ')', $supplierIds);
	}
	/**
	 * getting the libraries
	 * 
	 * @param string $libCodes
	 * @throws Exception
	 * @return Ambigous <Ambigous, multitype:, multitype:BaseEntityAbstract >
	 */
	private static function _getLibs($libCodes = null)
	{
		if(!is_array($libCodes))
			throw new Exception("System Error: lib has to be a array!");
		if($libCodes === null || count($libCodes) === 0)
			return BaseServiceAbastract::getInstance('Library')->findAll();
		return BaseServiceAbastract::getInstance('Library')->getLibsFromCodes($libCodes);
	}
	/**
	 * Loging the messages
	 * 
	 * @param unknown $msg
	 * @param unknown $script
	 * 
	 */
	public static function log($msg, $comments = '')
	{
		Log::logging(BaseServiceAbastract::getInstance('Library')->get(Library::ID_ADMIN_LIB), 0, 'ImportProduct', $msg, Log::TYPE_PIMPORT, $comments,  'ImportProduct');
	}
	
	public static function showLogs($lineBreaker = "\r\n")
	{
		$where = 'transId = ? and funcName = ?';
		$logs = BaseServiceAbastract::getInstance('Log')->findByCriteria($where, array(self::getLogTransId(), 'ImportProduct'));
		foreach($logs as $log)
		{
			echo $log->getMsg() . $lineBreaker;
		}
	}
}
