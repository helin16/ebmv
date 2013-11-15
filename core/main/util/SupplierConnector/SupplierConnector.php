<?php
class SupplierConnector
{
	const CODE_SUCC = 0;
	/**
	 * @var Supplier
	 */
	private $_supplier;
	/**
	 * Where to get the product list
	 * @var string
	 */
	private $_wsdlUrl;
	/**
	 * The connectors
	 * @var array
	 */
	private static $_connectors = array();
	/**
	 * singleton getter
	 * 
	 * @param Supplier $supplier The supplier
	 * 
	 * @return SupplierConnector
	 */
	public static function getInstance(Supplier $supplier)
	{
		$className = __CLASS__;
		if(!isset($_connectors[$supplier->getId()]))
			self::$_connectors[$supplier->getId()] = new $className($supplier);
		return self::$_connectors[$supplier->getId()];
	}
	/**
	 * construtor
	 * @param Supplier $supplier The supplier
	 */
	public function __construct(Supplier $supplier)
	{
		$this->_supplier = $supplier;
		$urls = explode(',', $this->_supplier->getInfo('import_url'));
		$this->_wsdlUrl = $urls[0];
	}
	/**
	 * Getting the import url
	 * 
	 * @return string
	 */
	public function getImportUrl()
	{
		return $this->_wsdlUrl;
	}
	/**
	 * Gettht product List
	 * 
	 * @throws CoreException
	 * @return SimpleXMLElement
	 */
	public function getProductListInfo()
	{
		$xml = $this->_getFromSoap($this->_wsdlUrl, "GetBookList", array("SiteID" => Config::get('site', 'code'), "Index" => 1, "Size" => 1));
		if(!$xml instanceof SimpleXMLElement)
			throw new CoreException('Can NOT get the pagination information from ' . $wsdl . '!');
		$array = array();
		foreach($xml->attributes() as $key => $value)
			$array[$key] = trim($value);
		return $array;
	}
	/**
	 * Getting xml product list
	 * 
	 * @param number $pageNo   The page no
	 * @param number $pageSize the page size
	 * 
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function getProductList($pageNo = 1, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE)
	{
		return $this->_getFromSoap($this->_wsdlUrl, "GetBookList", array("SiteID" => Config::get('site', 'code'), "Index" => $pageNo, "Size" => $pageSize));
	}
	/**
	 * Parsing the Xml file
	 *
	 * @param string $filePath The path of the downloaded file
	 * @param int    $index    Which product of the file to import
	 *
	 * @throws CoreException
	 * @return array
	 */
	public function importProductFromXml(SimpleXMLElement $xml, $index = null)
	{
		$children  = $xml->children();
		if (trim ( $index ) !== '')
			return array($this->_importProduct($children[$index]));
		
		$products = array ();
		foreach($xml->children() as $child)
		{
			$products [] = $this->_importProduct( $child );
		}
		return $products;
	}
	/**
	 * Getting the xml response form the soup server
	 *
	 * @param string $wsdl     The WSDL for the soup
	 * @param int    $siteId   The site id
	 * @param int    $pageNo   The pageno
	 * @param int    $pageSize The pageSize
	 *
	 * @return NULL|SimpleXMLElement
	 */
	private function _getFromSoap($wsdl, $funcName, $params = array(), $resultTagName = null)
	{
		$client = new SoapClient($wsdl, array('exceptions' => true, 'encoding'=>'utf-8', 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
		$result = $client->$funcName($params);
		$resultTagName = (trim($resultTagName) === '' ? $funcName . 'Result' : $resultTagName);
		if(!isset($result->$resultTagName) || !isset($result->$resultTagName->any) || trim($result->$resultTagName->any) === '')
			return null;
		try 
		{
			$xml = new SimpleXMLElement($result->$resultTagName->any);
			return $xml;
		}
		catch (Exception $ex)
		{
			throw new Exception("Error for getting \$result->$resultTagName->any: " . $result);
		}
	}
	/**
	 * Importing the product
	 * 
	 * @param SimpleXMLElement $xml        The xml of the product list
	 * @param array            $categories The array of the categories a product should be in
	 * 
	 * @throws Exception
	 * @return unknown
	 */
	protected function _importProduct(SimpleXMLElement $xml, array $categories = array())
	{
		//list($defaultLang, $defaultType) = $this->_getDefaulLangNType(); 
		if(!($lang = BaseServiceAbastract::getInstance('Language')->getLangByCode($this->_getAttribute($xml, 'Language'))) instanceof Language)
			throw new Exception("Invalid lanuage code: " . $this->_getAttribute($xml, 'Language'));
		if(!($type = BaseServiceAbastract::getInstance('ProductType')->getByName(strtolower(trim($xml->getName())))) instanceof ProductType)
			throw new Exception("Invalid ProductType: " . strtolower(trim($xml->getName())));
		
		$transStarted = false;
		try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true; }
		try
		{
			if(($isbn = $this->_getAttribute($xml, 'Isbn')) === '')
				throw new Exception('No ISBN provided!');
			if(($no = $this->_getAttribute($xml, 'NO')) === '')
				$no = 0;
	
			$categories = (count($categories) > 0 ? $categories : $this->_importCategories($xml));
			//updating the product
			if(($product = BaseServiceAbastract::getInstance('Product')->findProductWithISBNnCno($isbn, $no)) instanceof Product)
			{
				//delete the thumb
				if(!($thumbs = explode(',', $product->getAttribute('image_thumb'))) !== false)
					BaseServiceAbastract::getInstance('Asset')->removeAssets($thumbs);
				//delete the img
				if(!($imgs = explode(',', $product->getAttribute('image'))) !== false)
					BaseServiceAbastract::getInstance('Asset')->removeAssets($imgs);
				
				$product = BaseServiceAbastract::getInstance('Product')->updateProduct($product,
						$this->_getAttribute($xml, 'BookName'),
						$this->_getAttribute($xml, 'Author'),
						$isbn,
						$this->_getAttribute($xml, 'Press'),
						$this->_getAttribute($xml, 'PublicationDate'),
						$this->_getAttribute($xml, 'Words'),
						$categories,
						$this->_importImage($this->_getAttribute($xml, 'FrontCover')),
						$this->_getAttribute($xml, 'Introduction'),
						$no,
						$this->_getAttribute($xml, 'Cip'),
						$lang,
						$type
				);
			}
			//creating new product
			else
			{
				$product = BaseServiceAbastract::getInstance('Product')->createProduct(
						$this->_getAttribute($xml, 'BookName'),
						$this->_getAttribute($xml, 'Author'),
						$isbn,
						$this->_getAttribute($xml, 'Press'),
						$this->_getAttribute($xml, 'PublicationDate'),
						$this->_getAttribute($xml, 'Words'),
						$categories,
						$this->_importImage($this->_getAttribute($xml, 'FrontCover')),
						$this->_getAttribute($xml, 'Introduction'),
						$no,
						$this->_getAttribute($xml, 'Cip'),
						$lang,
						$type
				);
			}
			BaseServiceAbastract::getInstance('Product')->addSupplier($product, $this->_supplier, $this->_getAttribute($xml, 'Price'));
			if($transStarted === false)
				Dao::commitTransaction();
			return $product;
		}
		catch(Exception $ex)
		{
			if($transStarted === false)
				Dao::rollbackTransaction();
			throw $ex;
		}
	}
	/**
	 * Getting the default language and product type for a supplier
	 * 
	 * @return multitype:Language ProductType
	 */
	private function _getDefaulLangNType()
	{
		$defaultLangIds = explode(',', $this->_supplier->getInfo('default_lang_id'));
		$defaultTypeIds = explode(',', $this->_supplier->getInfo('default_product_type_id'));
		return array(BaseServiceAbastract::getInstance('Language')->get($defaultLangIds[0]), BaseServiceAbastract::getInstance('ProductType')->get($defaultTypeIds[0]));
	}
	/**
	 * Importing the categories
	 *
	 * @param SimpleXMLElement $xml The category xml
	 *
	 * @return array
	 */
	public function _importCategories(SimpleXMLElement $xml)
	{
		$transStarted = false;
		try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true; }
		try
		{
			$categoryNames = explode('/', $this->_getAttribute($xml, 'BookType'));
			$cateogories = array();
			foreach($categoryNames as $index => $name)
			{
				$cateogories[$index] = BaseServiceAbastract::getInstance('Category')->updateCategory($name, (isset($cateogories[$index - 1]) && $cateogories[$index - 1] instanceof Category) ? $cateogories[$index - 1] : null);
			}
			if($transStarted === false)
				Dao::commitTransaction();
			return array_filter($cateogories);
		}
		catch(Exception $ex)
		{
			if($transStarted === false)
				Dao::rollbackTransaction();
			throw $ex;
		}
	}
	/**
	 * importing the image file
	 *
	 * @param string $imageUrl The url of the image
	 *
	 * @return string the asssetid
	 */
	protected function _importImage($imageUrl)
	{
		$transStarted = false;
		try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true;}
		try
		{
			if(($imageUrl = trim($imageUrl)) === '')
				return '';
			
			$tmpDir = explode(',', $this->_supplier->getInfo('default_img_dir'));
			$tmpDir = $tmpDir[0];
			if(!is_dir($tmpDir))
				mkdir($tmpDir);
			$paths = parse_url($imageUrl);
			$paths = explode('/', $paths['path']);
			$tmpFile = $this->downloadFile($imageUrl, $tmpDir . DIRECTORY_SEPARATOR . md5($imageUrl));
			$assetId = BaseServiceAbastract::getInstance('Asset')->setRootPath($tmpDir)->registerAsset(end($paths), $tmpFile);
	
			if($transStarted === false)
				Dao::commitTransaction();
			return $assetId;
		}
		catch(Exception $ex)
		{
			if($transStarted === false)
				Dao::rollbackTransaction();
			throw $ex;
		}
	}
	/**
	 * download the url to a local file
	 *
	 * @param string $url       The url
	 * @param string $localFile The local file path
	 *
	 * @return string The local file path
	 */
	public function downloadFile($url, $localFile, $timeout = null)
	{
		$timeout = trim($timeout);
		$fp = fopen($localFile, 'w+');
		$options = array(
				CURLOPT_FILE    => $fp,
				CURLOPT_TIMEOUT =>  (!is_numeric($timeout) ? 8*60*60 : $timeout), // set this to 8 hours so we dont timeout on big files
				CURLOPT_URL     => $url
		);
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		curl_exec($ch);
		fclose($fp);
		curl_close($ch);
		return $localFile;
	}
	/**
	 * read from a url
	 * 
	 * @param string $url     The url
	 * @param int    $timeout The timeout in seconds
	 * 
	 * @return mixed
	 */
	public static function readUrl($url, $timeout = null)
	{
		$timeout = trim($timeout);
		$options = array(
				CURLOPT_RETURNTRANSFER => 1, 
				CURLOPT_TIMEOUT =>  (!is_numeric($timeout) ? 8*60*60 : $timeout), // set this to 8 hours so we dont timeout on big files
				CURLOPT_URL     => $url
		);
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$data =curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	/**
	 * Getting the value of the attribute
	 *
	 * @param SimpleXMLElement $xml           The xml element
	 * @param string           $attributeName The attr name
	 *
	 * @return string
	 */
	protected function _getAttribute(SimpleXMLElement $xml, $attributeName)
	{
		return (isset($xml->$attributeName) && ($attribute = trim($xml->$attributeName)) !== '') ? $attribute : '';
	}
	/**
	 * Getting the book shelf
	 * 
	 * @param UserAccount $user
	 * @param Library     $lib
	 * 
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function getBookShelfList(UserAccount $user, Library $lib)
	{
		$username = trim($user->getUserName());
		$libCode = trim($lib->getInfo('aus_code'));
		$params = array("SiteID" => $libCode, 
					"Uid" => $username, 
					"Pwd" => trim($user->getPassword()), 
					'CDKey' => StringUtilsAbstract::getCDKey($this->_supplier->getInfo('skey'), $username, $libCode));
		$xml = $this->_getFromSoap($this->_wsdlUrl, "GetBookShelfList", $params);
		if(trim($xml['Code']) !== trim(self::CODE_SUCC))
			throw new Exception($xml['Value']);
		return $xml;
	}
	/**
	 * Synchronize user's bookshelf
	 * 
	 * @param UserAccount      $user
	 * @param SimpleXMLElement $xml
	 * 
	 * @return SupplierConnector
	 */
	public function syncUserBookShelf(UserAccount $user, SimpleXMLElement $xml)
	{
		$transStarted = false;
		try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true;}
		try
		{
			foreach ($xml->BookShelfList->children() as $bookXml)
				$this->syncShelfItem($user, trim($bookXml['Isbn']), trim($bookXml['NO']), trim($bookXml['BorrowTime']), trim($bookXml['State']));
			if($transStarted === false)
				Dao::commitTransaction();
			return $this;
		}
		catch(Exception $ex)
		{
			if($transStarted === false)
				Dao::rollbackTransaction();
			throw $ex;
		}
	}
	/**
	 * Synchronizing an indivdual product with supplier
	 * 
	 * @param UserAccount $user
	 * @param string      $isbn
	 * @param string      $no
	 * @param string      $borrowTime
	 * @param string      $status
	 * 
	 * @return SupplierConnector
	 */
	public function syncShelfItem(UserAccount $user, $isbn, $no, $borrowTime, $status)
	{
		$product = BaseServiceAbastract::getInstance('Product')->findProductWithISBNnCno($isbn, $no, $this->_supplier);
		if($product instanceof Product)
			BaseServiceAbastract::getInstance('ProductShelfItem')->syncShelfItem($user, $product, $borrowTime, $status);
		return $this;
	}
	/**
	 * Adding a product to the user's bookshelf
	 * 
	 * @param UserAccount $user
	 * @param Product     $product
	 * @param Library     $lib
	 * 
	 * @throws CoreException
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function addToBookShelfList(UserAccount $user, Product $product, Library $lib)
	{
		$username = trim($user->getUserName());
		$libCode = trim($lib->getInfo('aus_code'));
		$params = array("SiteID" => $libCode,
				'Isbn' => trim($product->getAttribute('isbn')),
				'NO' => trim($product->getAttribute('cno')),
				"Uid" => $username,
				"Pwd" => trim($user->getPassword()),
				'CDKey' => StringUtilsAbstract::getCDKey($this->_supplier->getInfo('skey'), $username, $libCode));
		$xml = $this->_getFromSoap($this->_wsdlUrl, "AddToBookShelf", $params);
		if(trim($xml['Code']) !== trim(self::CODE_SUCC))
			throw new Exception("Connector Error: " .$xml->Value);
		return $xml;
	}
	/**
	 * Removing a product from the book shelf
	 * 
	 * @param UserAccount $user
	 * @param Product     $product
	 * @param Library     $lib
	 * 
	 * @throws CoreException
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function removeBookShelfList(UserAccount $user, Product $product, Library $lib)
	{
		$username = trim($user->getUserName());
		$libCode = trim($lib->getInfo('aus_code'));
		$params = array("SiteID" => $libCode,
				'Isbn' => trim($product->getAttribute('isbn')),
				'NO' => trim($product->getAttribute('cno')),
				"Uid" => $username,
				"Pwd" => trim($user->getPassword()),
				'CDKey' => StringUtilsAbstract::getCDKey($this->_supplier->getInfo('skey'), $username, $libCode));
		$xml = $this->_getFromSoap($this->_wsdlUrl, "RemoveFromBookShelf", $params);
		if(trim($xml['Code']) !== trim(self::CODE_SUCC))
			throw new Exception("Connector Error: " . $xml->Value);
		return $xml;
	}
	/**
	 * Getting the download url for a book
	 * 
	 * @param Product     $product The product we are trying to get the url for
	 * @param UserAccount $user    Who wants to download it
	 * 
	 * @throws Exception
	 */
	public function getDownloadUrl(Product $product, UserAccount $user)
	{
		$downloadUrl = trim($this->_supplier->getInfo('download_url'));
		$urlParams = array('SiteID' => Config::get('site', 'code'),
				'Isbn' => $product->getAttribute('isbn'),
				'NO' => $product->getAttribute('cno'),
				'Format' => 'xml',
				'Uid' => $user->getUserName(),
				'Pwd' => $user->getPassword()
		);
		$url = $downloadUrl . '?' . http_build_query($urlParams);
		$result = self::readUrl($url);
		try
		{
			$xml = new SimpleXMLElement($result);
		}
		catch(Exception $ex)
		{
		}
		BaseServiceAbastract::getInstance('ProductShelfItem')->borrowItem($user, $product, Core::getLibrary(), $this->_supplier);
		if(trim($xml->Code) !== trim(self::CODE_SUCC))
			throw new Exception("Connector Error: " . trim($xml->Value));
		return trim($xml->Value);
	}
}