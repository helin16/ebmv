<?php
require_once 'bootstrap.php';
$product = Product::get(18621);

$result = SupplierConnectorAbstract::getInstance($product->getSupplier(), Library::get(10))->updateProduct($product);

var_dump($result);

?>