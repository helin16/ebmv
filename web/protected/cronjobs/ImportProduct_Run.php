<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';

//checking usage
if ($argc != 4)
		die("Usage: ImportProduct siteCode(37,werew,121fd|all) supplierids(1,2,3|all) totalrecords(30|all)\r\n");

if (!Core::getUser() instanceof UserAccount)
	Core::setUser(BaseServiceAbastract::getInstance('UserAccount')->get(UserAccount::ID_SYSTEM_ACCOUNT));

$libCodes = (($libCodes = trim($argv[1])) === 'all' ? array() : explode(',', str_replace(' ', '', $libCodes)));
$supplierIds = (($supplierIds = trim($argv[2])) === 'all' ? array() : explode(',', str_replace(' ', '', $supplierIds)));
$totalrecords = (($totalrecords = trim($argv[3])) === 'all' ? null : $totalrecords);

ImportProduct::log("== Params ===================================================", 'load');
ImportProduct::log("== Site Codes: '" . implode("', '", $libCodes), 'load');
ImportProduct::log("== Supplier IDS: " . implode(', ', $supplierIds), 'load');
ImportProduct::log("== Total Records: '" . $totalrecords, 'load');
ImportProduct::log("=============================================================", 'load');
ImportProduct::run($libCodes, $supplierIds, $totalrecords);
