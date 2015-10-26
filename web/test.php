<?php
require_once 'bootstrap.php';

$result = SupplierConnectorAbstract::getInstance(Supplier::get(12), Library::get(10))->getProductListInfo(ProductType::get(3));

var_dump($result);

?>