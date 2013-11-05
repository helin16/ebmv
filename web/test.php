<?php
require 'bootstrap.php';

$username = 'test_user';
$libCode = '37';
$supplier = BaseServiceAbastract::getInstance('Supplier')->get(1);

$user = BaseServiceAbastract::getInstance('UserAccount')->get(1);
$lib = BaseServiceAbastract::getInstance('Library')->get(1);
$script = SupplierConnector::getInstance($supplier);
$result = $script->getBookShelfList($user, $lib);
var_dump($result);