<?php
/**
 * Service for accessing/storing content in shared storage
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class AssetService extends BaseServiceAbastract
{
    private $_assetRootPath = '';
	/**
	 * constructor
	 */
	public function __construct($rootPath = '')
	{
		parent::__construct('Asset');
		$this->_assetRootPath = ($rootPath === '' ? dirname(__FILE__) : $rootPath);
	}
	/**
	 * Setting the root path
	 * 
	 * @param string $rootPath The root path of the assets
	 * 
	 * @return AssetService
	 */
	public function setRootPath($rootPath)
	{
	    $this->_assetRootPath = $rootPath;
	    return $this;
	}
	/**
	 * Register a file with the Asset server and get its asset id
	 *
	 * @param string $filename The name of the file
	 * @param string $data     The data within that file we are trying to save
	 * 
	 * @return string 32 char MD5 hash
	 */
	public function registerAsset($filename, $dataOrFile)
	{
	    if(!is_string($dataOrFile) && (!is_file($dataOrFile)))
	        throw new CoreException(__CLASS__ . '::' . __FUNCTION__ . '() will ONLY take string to save!');
	    
	    $assetId = md5($filename . '::' . Core::getUser()->getId() .  '::' . microtime());
	    $path = $this->_getSmartPath($assetId);
	    $this->_copyToAssetFolder($path, $dataOrFile);
		$asset = new Asset();
		$asset->setFilename($filename)
		    ->setAssetId($assetId)
		    ->setMimeType(self::getMimeType($filename))
		    ->setPath($path);
		$this->save($asset);
		return $asset->getAssetId();
	}
	/**
	 * Getting the smart parth
	 * 
	 * @param string $assetId The asset id
	 * 
	 * @return string
	 */
	private function _getSmartPath($assetId)
	{
	    $now = new UDate();
	    $year = $now->format('Y');
	    if(!is_dir($yearDir = trim($this->_assetRootPath .DIRECTORY_SEPARATOR . $year)))
	    {
	        mkdir($yearDir);
	        chmod($yearDir, 0777);
	    }
	    $month = $now->format('m');
	    if(!is_dir($monthDir = trim($yearDir .DIRECTORY_SEPARATOR . $month)))
	    {
	        mkdir($monthDir);
	        chmod($monthDir, 0777);
	    }
	    return $monthDir . DIRECTORY_SEPARATOR . $assetId;	    
	}
	/**
	 * Remove an asset from the content server
	 *
	 * @param array $assetIds The assetids of the content
	 *
	 * @return bool
	 */
	public function removeAssets(array $assetIds)
	{
		if(count($assetIds) === 0)
			return $this;
		
		$where = "assetId in (" . implode(', ', array_fill(0, count($assetIds), '?')) . ")";
		$params = $assetIds;
		foreach($this->findByCriteria($where, $assetIds) as $asset)
		{
		    // Remove the file from the NAS server
		    $file = trim($asset->getPath());
		    if(file_exists($file))
		    	unlink($file);
		}
		// Delete the item from the database
		Dao::deleteByCriteria(EntityDao::getInstance($this->_entityName)->getQuery(), $where, $params);
		return $this;
	}
	/**
	 * copy the provided file or data into the new path
	 * 
	 * @param string $filename   The new filename
	 * @param string $dataOrFile the file or data
	 * 
	 * @return number|boolean
	 */
	private function _copyToAssetFolder($newFile, $dataOrFile)
	{
	    if(!is_file($dataOrFile))
	        return file_put_contents($newFile, $dataOrFile);
	    return rename($dataOrFile, $newFile);
	}
}

?>