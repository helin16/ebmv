<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
class CleanupAssets
{
	public static function run()
	{
		self::_log(__FUNCTION__, '== start @ ' . new UDate());
		
		//find out all the assets in the table that are not used by the system
		self::_log(__FUNCTION__, '== find out all the assets in the table that are not used by the system');
		$usedAssetIds = self::_getAllUnusedAssets();
		self::_log(__FUNCTION__, '	:: Got');
		self::_log(__FUNCTION__, '	' . print_r($usedAssetIds, true));
		
		self::_log(__FUNCTION__, '== remove those assets from DB and files');
		//removing all the unused assets from files and DB
		self::_removeAsset($usedAssetIds);
		
		//removing all zombie files
		self::_log(__FUNCTION__, '== remove all zombie files');
		self::_rmAllUnusedAssetsFiles();
		
		self::_log(__FUNCTION__, '== Finished @ ' . new UDate());
	}
	private function _log($functName, $msg)
	{
		echo $functName . ': ' . $msg;
	}
	/**
	 * Getting all the unused asset record in DB
	 * 
	 * @return array
	 */
	private static function _getAllUnusedAssets()
	{
		$sql = "select ass.assetId, ass.path from asset ass
			left join productattribute att on (att.attribute = ass.assetId and att.typeId IN (" . ProductAttributeType::ID_IMAGE . ", " . ProductAttributeType::ID_IMAGE_THUMB ."))
			where att.id is null";
		$return = array();
		foreach(Dao::getResultsNative($sql) as $row)
			$return[] = $row['assetId'];
		return $return;
	}
	/**
	 * removing all the assets from db and files
	 * 
	 * @param array $assetIds The array of assetId
	 */
	private static function _removeAsset(array $assetIds)
	{
		BaseServiceAbastract::getInstance('Asset')->removeAssets($assetIds);
	}
	/**
	 * removing all zombie files
	 */
	private static function _rmAllUnusedAssetsFiles()
	{
		$sql = "select value from supplierinfo where typeId = " . SupplierInfoType::ID_IMAGE_LOCATION;
		foreach(Dao::getResultsNative($sql) as $row)
			self::_rmZombieFiles($row['value']);
	}
	/**
	 * removing all zombie files under the root path
	 * 
	 * @param string $rootPath
	 */
	private static function _rmZombieFiles($rootPath)
	{
		foreach(glob($rootPath . DIRECTORY_SEPARATOR . '*', GLOB_BRACE) as $file)
		{
			if(is_file($file))
			{
				$assetId = basename($file);
				if(!self::_checkAssetExsitsDb($assetId))
					unlink($file);
			}
			else if(is_dir($file))
				self::_rmZombieFiles($file);
		}
	}
	/**
	 * Checking whether the assetId exsits in DB
	 * 
	 * @param string $assetId The assetId
	 * 
	 * @return boolean
	 */
	private static function _checkAssetExsitsDb($assetId)
	{
		$sql = 'select id from asset where assetId = ?';
		$result = Dao::getResultsNative($sql, array($assetId), PDO::FETCH_NUM);
		return count($result) > 0;
	}
}

CleanupAssets::run();