<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';

//checking usage
if ($argc < 4 || $argc > 5)
		die("Usage: ImportProduct siteCode(37,werew,121fd|all) supplierids(1,2,3|all) totalrecords(30|all) typeIds(1,2,3)[optional]\r\n");

if (!Core::getUser() instanceof UserAccount)
	Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

echo "== Cleanup Assets ===================================================\n\r";
CleanupAssets::run();
echo "== Done with Assets ===================================================\n\r";
echo "\n\r\n\r\n\r\n\r\n\r";


$libCodes = (($libCodes = trim($argv[1])) === 'all' ? array() : explode(',', str_replace(' ', '', $libCodes)));
$supplierIds = (($supplierIds = trim($argv[2])) === 'all' ? array() : explode(',', str_replace(' ', '', $supplierIds)));
$totalrecords = (($totalrecords = trim($argv[3])) === 'all' ? null : $totalrecords);
$typeIds = array();
if(isset($argv[4]) && is_array($someTypeIds = explode(',', trim($argv[4]))) && count($someTypeIds) >0)
    $typeIds = $someTypeIds;

echo "== Params ===================================================\n\r";
echo "== Site Codes: '" . implode("', '", $libCodes) . "\n\r";
echo "== Supplier IDS: " . implode(', ', $supplierIds) . "\n\r";
echo "== TYPE IDS: " . implode(', ', $typeIds) . "\n\r";
echo "== Total Records: '" . $totalrecords . "\n\r";
echo "== Starting Importing @ " . trim(new UDate()) . "========================================================\n\r";
ImportProduct::run($libCodes, $supplierIds, $totalrecords, $typeIds);
echo "== Finished Importing @ " . trim(new UDate()) . "========================================================\n\r";
echo "\n\r\n\r\n\r\n\r\n\r";


echo "== Cleanup Assets ===================================================\n\r";
CleanupAssets::run();
echo "== Done with Assets ===================================================\n\r";
