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
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct('Asset');
	}
	/**
	 * Register a file with the Asset server and get its asset id
	 *
	 * @param string $filename The name of the file
	 * @param string $data     The data within that file we are trying to save
	 * 
	 * @return string 32 char MD5 hash
	 */
	public function registerAsset($filename, $data)
	{
	    if(!is_string($data))
	        throw new CoreException(__CLASS__ . '::' . __FUNCTION__ . '() will ONLY take string to save!');
	    
		$asset = new Asset();
		$asset->setFilename($filename);
		$md5 = $filename . '::' . Core::getUser()->getId() .  '::' . microtime();
		$assetId = md5($md5);
		$asset->setAssetId($assetId);
		$asset->setMimeType(self::getMimeType($filename));
		$asset->setData($data);
		$this->save($asset);
		return $assetId;
	}
	/**
	 * Getting the Asset object
	 * 
	 * @param string $assetId The assetid of the content
	 * 
	 * @return Ambigous <unknown, array(HydraEntity), Ambigous, multitype:, string, multitype:Ambigous <multitype:, multitype:NULL boolean number string mixed > >
	 */
	public function getAsset($assetId)
	{
		$content = $this->findByCriteria('assetId = ?', array($assetId), false, 1, 1);
		return count($content) === 0 ? null : $content[0];
	}
	/**
	 * Remove an asset from the content server
	 *
	 * @param string $assetId The assetid of the content
	 * 
	 * @return bool
	 */
	public function removeAsset($assetId)
	{
		EntityDao::getInstance($this->_entityName)->deleteByCriteria('assetId = ?', array($assetId));
		return $this;
	}
	/**
	 * Simple method for detirmining mime type of a file based on file extension
	 * This isn't technically correct, but for our problem domain, this is good enough
	 *
	 * @param string $filename The name of the file
	 * 
	 * @return string
	 */
	public static function getMimeType($filename)
	{
        preg_match("|\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);

        switch(strtolower($fileSuffix[1]))
        {
            case "js" :
                return "application/x-javascript";

            case "json" :
                return "application/json";

            case "jpg" :
            case "jpeg" :
            case "jpe" :
                return "image/jpg";

            case "png" :
            case "gif" :
            case "bmp" :
            case "tiff" :
                return "image/".strtolower($fileSuffix[1]);

            case "css" :
                return "text/css";

            case "xml" :
                return "application/xml";

            case "doc" :
            case "docx" :
                return "application/msword";

            case "xls" :
            case "xlt" :
            case "xlm" :
            case "xld" :
            case "xla" :
            case "xlc" :
            case "xlw" :
            case "xll" :
                return "application/vnd.ms-excel";

            case "ppt" :
            case "pps" :
                return "application/vnd.ms-powerpoint";

            case "rtf" :
                return "application/rtf";

            case "pdf" :
                return "application/pdf";

            case "html" :
            case "htm" :
            case "php" :
                return "text/html";

            case "txt" :
                return "text/plain";

            case "mpeg" :
            case "mpg" :
            case "mpe" :
                return "video/mpeg";

            case "mp3" :
                return "audio/mpeg3";

            case "wav" :
                return "audio/wav";

            case "aiff" :
            case "aif" :
                return "audio/aiff";

            case "avi" :
                return "video/msvideo";

            case "wmv" :
                return "video/x-ms-wmv";

            case "mov" :
                return "video/quicktime";

            case "zip" :
                return "application/zip";

            case "tar" :
                return "application/x-tar";

            case "swf" :
                return "application/x-shockwave-flash";

            default :
        }
        		
		if(function_exists("mime_content_type"))
			$fileSuffix = mime_content_type($filename);

		return "unknown/" . trim($fileSuffix[0], ".");
	}
}

?>