<?php
require_once dirname(__FILE__) . '/../main/bootstrap.php';

class Fix
{
	public static function run()
	{
		$array = self::_getProducts();
		var_dump($array);
	}
	
	private static function _getProducts()
	{
		$sql = "select productId, attribute, typeId from productattribute where active = 1 and typeId in (?, ?)";
		$result = Dao::getResultsNative($sql, array(ProductAttributeType::ID_ISBN, ProductAttributeType::ID_CNO));
		$array = array();
		foreach($result as $row)
		{
			$productId = $row['productId'];
			if(!isset($array[$productId]))
				$array[$productId] = array('isbn' => '', 'cno' => '', 'ids' => array());
			if(intval($row['typeId']) === ProductAttributeType::ID_ISBN)
				$array[$productId]['isbn'] = trim($row['attribute']);
			else if(intval($row['typeId']) === ProductAttributeType::ID_CNO)
				$array[$productId]['cno'] = trim($row['attribute']);
			
			$array[$productId]['ids'][] = $productId;
			$array[$productId]['ids'] = array_unique($array[$productId]['ids']);
		}
		return $array;
	}
}

Fix::run();