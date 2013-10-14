<?php
/**
 * Service for accessing/storing content in shared storage
 *
 * @package    Core
 * @subpackage Service
 * @author     lhe<helin16@gmail.com>
 *
 */
class ContentService extends BaseServiceAbastract
{
	// NOTE: These need to match up with the entries in the contenttype table
	/**
	 * The type of the content for graph
	 * @var int
	 */
	const TYPE_GRAPH  = 1;
	/**
	 * The type of the content for REPORT
	 * @var int
	 */
	const TYPE_REPORT = 2;
	/**
	 * The type of the content for graph
	 * @var int
	 */
	const TYPE_STATIC = 3;
	/**
	 * The type of the content for KNOWLEDGE
	 * @var int
	 */
	const TYPE_KNOWLEDGE = 4;
	/**
	 * The type of the content for BSUITENEWS
	 * @var int
	 */
	const TYPE_BSUITENEWS = 5;
	/**
	 * The type of the content for KNOWLEDGE_BULK
	 * These wont be selectable for the service plan.
	 * 
	 * @var int
	 */
	const TYPE_KNOWLEDGE_BULK = 6;

	const TYPE_ATTACHMENT = 7;
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct('Content');
	}
	/**
	 * Register a file with the content server and get its asset id
	 *
	 * @param int    $type               The type of the content
	 * @param string $filename           The name of the file
	 * @param string $data               The data within that file we are trying to save
	 * @param bool   $generateNewAssetId If true, appends microtime to md5 assetid, and saves to content
	 * 
	 * @return string 32 char MD5 hash
	 */
	public function registerAsset($type, $filename, $data, $generateNewAssetId = true)
	{
	    if(!is_string($data))
	        throw new HydraCoreException(__CLASS__ . '::' . __FUNCTION__ . '() will ONLY take string to save!');
	    
		Dao::prepareNewConnection(Dao::DB_MAIN_SERVER, true); //reconnect to main server
		$contentType = Factory::dao('ContentType')->findById($type);
		$content = new Content();
		$content->setContentType($contentType);
		$content->setFilename($filename);
		$md5 = $type . '::' . $filename . '::' . Core::getUser()->getId();

		if ($generateNewAssetId)
		{
			$md5 .= '::' . microtime();
		}
		
		$assetId = md5($md5);
		$content->setAssetId($assetId);
		$path = $this->_buildSmartPath($assetId);
		$content->setPath($path);
		$content->setMimeType($this->getMimeType($filename));

		if ($generateNewAssetId)  								//we are generating a unqiue assetId every time
		{
			Factory::dao('Content')->save($content);  				//saving the new entry to the content table
		}
		else
		{
			$replaceContent = $this->getContent($assetId); 		//get old content to update updated time etc
			if ($replaceContent instanceof Content)
				Factory::dao('Content')->save($replaceContent);
			else
				Factory::dao('Content')->save($content);  			//saving the new entry to the content table
		}
		// Write data to NAS, create directory
		$dirs = explode("/",$path);
		$p = rtrim($contentType->getPath(), "/");

		foreach ($dirs as $d)
		{
			$p = $p . "/" . $d;
			if(!is_dir($p))
			{
				mkdir($p);
				chmod($p, 0777);				
			}
		}
		
	    $this->replaceAsset($assetId, $data);
		return $assetId;
	}
	/**
	 * Replace an existing asset with a block of data
	 *
	 * @param string $assetId The assetId of the content
	 * @param string $data    The data that we are trying to save
	 * 
	 * @return ContentServer
	 */
	public function replaceAsset($assetId, $data)
	{
		$content = $this->getContent($assetId);
	
		if($content instanceof Content)
		    file_put_contents($content->getFilePath(), $data);
		
		return $this;
	}
	/**
	 * Stream content directly to the browser
	 *
	 * @param string $assetId The assetid of the content
	 */
	public function streamAsset($assetId)
	{
	    $content = $this->getContent($assetId);
	    
		if($content instanceof Content)
		{
    		header('Content-Type: ' . $content->getMimeType());
    		readfile($content->getFilePath());
		}
	}
	/**
	 * Getting the content object
	 * 
	 * @param string $assetId The assetid of the content
	 * 
	 * @return Ambigous <unknown, array(HydraEntity), Ambigous, multitype:, string, multitype:Ambigous <multitype:, multitype:NULL boolean number string mixed > >
	 */
	public function getContent($assetId)
	{
		$content = Factory::dao('Content')->findByCriteria('assetId = ?', array($assetId));
		
		if (!empty($content))
			$content = $content[0];
		return $content;		
	}
	/**
	 * Get a url that links directly to the asset
	 *
	 * @param string $assetId The assetid of the content
	 * 
	 * @return string
	 */
	public function getUrl($assetId)
	{
		return '/contentserver/' . $assetId . '/get';
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
		$content = $this->getContent($assetId);
		
		if(!$content instanceof Content)
		    return;
		// Delete the item from the database
		Dao::execSql('delete from content where assetId=?', array($assetId));
		// Remove the file from the NAS server
		unlink($content->getFilePath());
	}
	/**
	 * Return the path allocated to an asset. This will create them in a "load balanced" path structure
	 *   eg. assetId=1234567 will build a path /1/2/3/4
	 *       paths are always based on the first 4 characters with the entire file in that directory
	 *
	 * @param string $assetId The assetid of the content
	 * 
	 * @return string
	 */
	private function _buildSmartPath($assetId)
	{
		$chars = str_split($assetId);
		$path = '';
		for ($i=0; $i<=4; $i++)
		{
			$path .= '/' . $chars[$i];
		}
		return $path;
	}
	/**
	 * Simple method for detirmining mime type of a file based on file extension
	 * This isn't technically correct, but for our problem domain, this is good enough
	 *
	 * @param string $filename The name of the file
	 * 
	 * @return string
	 */
	private function getMimeType($filename)
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
		{
			$fileSuffix = mime_content_type($filename);
		}

		return "unknown/" . trim($fileSuffix[0], ".");
	}
	/**
	 * Gathering the file information of the content type
	 * 
	 * @param int    $type   The type of the content
	 * @param string $search Deprecated, I guess! I don't know why this one is here!!!!! I hate to waste/miss guide the usage of this function with a unused param!!!
	 * 
	 * @return array
	 * @deprecated Please call ContentServer::getActiveDetails() instead of this function
	 */
	public function getFileDetails($type, $idList = array())
	{
		return $this->getActiveDetails($type, $idList);
	}
	/**
	 * Gathering the file information of the content type and provided array of ids
	 * 
	 * @param int   $type   The type of the content
	 * @param array $idList The array of content ids
	 * 
	 * @return array
	 */
	public function getActiveDetails($type = null, $idList = array())
	{
	    $where = $params = array();
	    if(!is_null($type))
	    {
	        $where[] = 'contentTypeID = ?';
    	    $params[] = $type;
	    }
	    if(is_array($idList) && count($idList) > 0)
	    {
	        $where[]= 'id in (?)';
	        $params[] = implode(',', $idList);
	    }
	    if(count($where) === 0)
	        throw new HydraCoreException('System Error: We need at least one of them: type or idList!');
		$content = Factory::dao('Content')->findByCriteria(implode(' AND ', $where), $params);
		$contentType = array();
		foreach ($content AS $value)
		{
			$contentType[$value->getId()]['created'] = $value->getCreated();
			$contentType[$value->getId()]['filename'] = $value->getFilename();
			$contentType[$value->getId()]['id'] = $value->getId();
			$contentType[$value->getId()]['assetId'] = $value->getAssetId();
		}
		return $contentType;
	}
	/**
	 * Getting the information of the content based on the id
	 * 
	 * @param int    $id     The id of the content
	 * @param string $search Deprecated, I guess! I don't know why this one is here!!!!! I hate to waste/miss guide the usage of this function with a unused param!!!
	 * 
	 * @return array
	 */
	public function getFileDetailsById($id, $search = Null)
	{
		$infoArray = $this->getActiveDetails(null, array($id));
		return (is_array($infoArray) && isset($infoArray[$id])) ? $infoArray[$id] : array();
	}
	/**
	 * get the name of the contenttype based on the content type id
	 * I hate this function, don't ever do it again!!!! --- lin
	 * 
	 * @param int $id The id of the content type
	 * 
	 * @return false|string False - when provided id is not found; otherwise, string
	 */
	public function getLibraryNameByContentTypeId($id)
	{
		$contentType = Factory::dao('ContentType')->findById($id);
		if($contentType instanceof ContentType)
			return $contentType->getType();
		return false;
	}
	/**
	 * getting the content type based on id
	 * 
	 * @param int $id The id of the content
	 * 
	 * @return ContentType
	 */
	public function getContentType($id)
	{
	    return Factory::dao('ContentType')->findById($id);
	}
}

?>