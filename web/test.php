<?php
require_once 'bootstrap.php';

$result = SupplierConnectorAbstract::getInstance(Supplier::get(12), Library::get(10))->getProductList(1, 10, ProductType::get(3), true);

var_dump($result);

?>