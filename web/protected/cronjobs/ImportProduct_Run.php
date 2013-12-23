<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
// //checking usage
// if ($argc != 4)
	// 	die("Usage: ImportProduct siteCode(37,werew,121fd|all) supplierids(1,2,3|all) totalrecords(30|all)\r\n");

	// $libCodes = (($libCodes = trim($argv[1])) === 'all' ? array() : explode(',', str_replace(' ', '', $libCodes)));
	// $supplierIds = (($supplierIds = trim($argv[2])) === 'all' ? array() : explode(',', str_replace(' ', '', $supplierIds)));
	// $totalrecords = (($totalrecords = trim($argv[3])) === 'all' ? null : $totalrecords);

	// ImportProduct::log("== Params ===================================================\r\n");
	// ImportProduct::log("== Site Codes: '" . implode("', '", $libCodes). "'\r\n");
	// ImportProduct::log("== Supplier IDS: " . implode(', ', $supplierIds). "\r\n");
	// ImportProduct::log("== Total Records: '" . $totalrecords. "'\r\n");
	// ImportProduct::log("=============================================================\r\n");

ImportProduct::run(array(37), array(1, 2), 2);
ImportProduct::showLogs("<br />");