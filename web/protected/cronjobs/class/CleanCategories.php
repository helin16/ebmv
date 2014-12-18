<?php
require_once dirname(__FILE__) . '/../../../bootstrap.php';
abstract class CleanCategories
{
	public static function run()
	{
		$cateMap = self::_getSameCategoies();
		if(count($cateMap) === 0)
			return;
		foreach($cateMap as $cateName => $cateArray)
		{
			if(count($cateArray) <= 1) //nothing to consider
				continue;
			$mergeTo = array_shift($cateArray);
			$mergeFromIds = array_map(create_function('$a', 'return $a->getId()'), $cateArray);
			if(count($mergeFromIds) === 0)
				continue;
			echo 'Start merging( ' . implode(', ', $mergeFromIds) . ') to ' . $mergeTo->getName() . '(' . $mergeTo->getId() . ")\n";
			$sql = 'update category_product set categoryId = ' . $mergeTo->getId() . ' where categoryId in( ' . implode(', ', $mergeFromIds) . ')';
			Dao::getResultsNative($sql);
			Category::updateByCriteria('active = 0', 'id != ' .$mergeTo->getId() . ' AND id in (' . implode(', ', $mergeFromIds) . ')');
			echo "END\n";
		}
	}
	/**
	 * Getting the category map
	 * 
	 * @return Ambigous <multitype:multitype: , Ambigous, multitype:, multitype:BaseEntityAbstract >
	 */
	private static function _getSameCategoies()
	{
		$cateMap = array();
		foreach(Category::getAll() as $category)
		{
			$name = trim($category->getName());
			if(!isset($cateMap[$name]))
				$cateMap[$name] = array();
			$cateMap[$name][] = $category;
		}
		return $cateMap;
	}
}

if (!Core::getUser() instanceof UserAccount)
	Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
CleanCategories::run();