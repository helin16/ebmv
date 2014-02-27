<?php
class CleanupAssets
{
	public static function run()
	{
		self::_log(__FUNCTION__, '== start @ ' . new UDate());
		try
		{
			
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
		}
		catch(Exception $ex)
		{
			self::_log(__FUNCTION__, '** Error: ' . $ex->getMessage());
			self::_log(__FUNCTION__, '   ' . $ex->getTraceAsString());
		}
		self::_log(__FUNCTION__, '== Finished @ ' . new UDate());
	}
	private function _log($functName, $msg)
	{
		echo $functName . ': ' . $msg . "\n\r";
	}
	/**
	 * Getting all the unused asset record in DB
	 * 
	 * @return array
	 */
	private static function _getAllUnusedAssets()
	{
		$return = array();
		$sql = "select distinct att.attribute from productattribute att where att.typeId IN (?, ?)";
		$usedAssetIds = array_map(create_function('$a', 'return trim($a[0]);'), Dao::getResultsNative($sql, array(ProductAttributeType::ID_IMAGE, ProductAttributeType::ID_IMAGE_THUMB), PDO::FETCH_NUM));
		
		$sql = "select distinct ass.assetId from asset ass";
		foreach(Dao::getResultsNative($sql, array(), PDO::FETCH_NUM) as $row)
		{
			if(!in_array(trim($row[0]), $usedAssetIds))
				$return[] = trim($row[0]);
		}
		return $return;
	}
	/**
	 * removing all the assets from db and files
	 * 
	 * @param array $assetIds The array of assetId
	 */
	private static function _removeAsset(array $assetIds)
	{
		self::_log(__FUNCTION__, '  :: removing assetIds: ');
		self::_log(__FUNCTION__, '  :: ' . implode("\n\r      ", $assetIds));
		BaseServiceAbastract::getInstance('Asset')->removeAssets($assetIds);
		self::_log(__FUNCTION__, '  :: finish removing assetIds: ');
	}
	/**
	 * removing all zombie files
	 */
	private static function _rmAllUnusedAssetsFiles()
	{
		$sql = "select value from supplierinfo where typeId = " . SupplierInfoType::ID_IMAGE_LOCATION;
		$totalFiles = array();
		foreach(Dao::getResultsNative($sql) as $row)
			self::_rmZombieFiles($row['value'], $totalFiles);
		self::_log(__FUNCTION__, '  :: ' . count($totalFiles) . ' file(s) tested!');
	}
	/**
	 * removing all zombie files under the root path
	 * 
	 * @param string $rootPath
	 */
	private static function _rmZombieFiles($rootPath, array &$totalFiles)
	{
		self::_log(__FUNCTION__, '  :: == removing files under: ' . $rootPath);
		foreach(glob($rootPath . DIRECTORY_SEPARATOR . '*', GLOB_BRACE) as $file)
		{
			if(is_file($file))
			{
				$totalFiles[] = $file;
				$assetId = basename($file);
				//self::_log(__FUNCTION__, '  :: == Got file(' . $assetId .') : ' . $file);
				if(!self::_checkAssetExsitsDb($assetId))
				{
					self::_log(__FUNCTION__, '  :: == removing file : ' . $file);
					unlink($file);
				}
			}
			else if(is_dir($file))
				self::_rmZombieFiles($file, $totalFiles);
		}
		self::_log(__FUNCTION__, '  :: == finished removing files under: ' . $rootPath);
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