<?php 
require_once dirname(__FILE__) . '/../../../bootstrap.php';
abstract class DownloadCatalogFromSupplier
{
	public static function run($supplierIds) {
		
		foreach($supplierIds as $supplierId) {
			if(!($supplier = Supplier::get($supplierId)) instanceof Supplier)
				continue;
			$script = self::_getSupplierScript(Supplier::get($supplierId), Library::get(1));
			if(!$script instanceof SupplierConn)
				continue;
			$script->downloadCatalog(ProductType::get(1), 1000);
		}
	}
	
	private static function _getSupplierScript(Supplier $supplier, Library $lib) {
		//if there is an error for supplier connector
		try {return SupplierConnectorAbstract::getInstance($supplier, $lib); }
		catch(Exception $ex)
		{
			self::_log( "  :: " . $ex->getMessage() . ". Trace: " . $ex->getTraceAsString(), __FUNCTION__);
			return null;
		}
	}
	/**
	 * Loging the messages
	 *
	 * @param unknown $msg
	 * @param unknown $script
	 *
	 */
	public static function log($msg, $funcName, $comments = '') {
		echo $msg . "\r\n";
		Log::logging(Library::get(Library::ID_ADMIN_LIB), 0, 'ImportProduct', $msg, Log::TYPE_PIMPORT, $comments,  $funcName);
	}
}

//checking usage
if ($argc != 2)
	die("Usage: DownloadCatalog supplierids(1,2,3|all)\r\n");
$supplierIds = (($supplierIds = trim($argv[1])) === 'all' ? array_map(create_function('$a', 'return $a->getId();'), Supplier::getAll()) : explode(',', str_replace(' ', '', $supplierIds)));

echo "== Start downloading categories from " . ($startScript = new UDate()) . " ===================================================\n\r";
echo "== Supplier IDS: " . implode(', ', $supplierIds) . "\n\r";
if (!Core::getUser() instanceof UserAccount)
	Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
DownloadCatalogFromSupplier::run($supplierIds);
echo "== Finished Importing @ " . ($finishScript = new UDate()) . ", Took " . ($finishScript->diff($startScript)->format('%r %y yrs, %m mths, %d days, %h hrs, %i mins, %s secs')) . "  ========================================================\n\r";
echo "\n\r\n\r\n\r\n\r\n\r";



?>