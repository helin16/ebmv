<?php
class SupplierConnectorAbstract
{
	/**
	 * @var Supplier
	 */
	protected $_supplier;
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
		$className = $supplier->getConnector();
		if(!isset($_connectors[$supplier->getId()]))
		{
			if(!($sc = new $className($supplier)) instanceof SupplierConn)
				throw new CoreException("$className is NOT a SupplierConn!");
			self::$_connectors[$supplier->getId()] = new $className($supplier);
		}
		return self::$_connectors[$supplier->getId()];
	}
	/**
	 * construtor
	 * @param Supplier $supplier The supplier
	 */
	public function __construct(Supplier $supplier)
	{
		$this->_supplier = $supplier;
	}
	/**
	 * Getting the import url
	 * 
	 * @return string
	 */
	public function getImportUrl()
	{
		$urls = explode(',', $this->_supplier->getInfo('import_url'));
		return $urls === false ? null : $urls[0];
	}
	/**
	 * Gettht product List
	 * 
	 * @throws CoreException
	 * @return SimpleXMLElement
	 */
	public function getProductListInfo()
	{
		throw new Exception(__FUNCTION__ . "() in class " . get_class($this) . " need to be overide to start!");
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
		throw new Exception(__FUNCTION__ . "() in class " . get_class($this) . " need to be overide to start!");
	}
	/**
	 * Parsing the Xml file
	 *
	 * @param string $productList The list of product from supplier
	 * @param int    $index       Which product of the file to import
	 *
	 * @throws CoreException
	 * @return array
	 */
	public function importProduct($productList, $index = null)
	{
		throw new Exception(__FUNCTION__ . "() in class " . get_class($this) . " need to be overide to start!");
	}
	/**
	 * Importing a single product
	 * 
	 * @param Mixed $productInfo The product information for a single product
	 * @param array $categories  The array of the categories a product should be in
	 * 
	 * @throws Exception
	 * @return unknown
	 */
	protected function _importProduct($productInfo, array $categories = array())
	{
		throw new Exception(__FUNCTION__ . "() in class " . get_class($this) . " need to be overide to start!");
	}
	/**
	 * Getting the book shelf
	 * 
	 * @param UserAccount $user The current user
	 * @param Library     $lib  Which library the user has been assigned to 
	 * 
	 * @return Mixed The ProductShelfItem array
	 */
	public function getBookShelfList(UserAccount $user, Library $lib)
	{
		throw new Exception(__FUNCTION__ . "() in class " . get_class($this) . " need to be overide to start!");
	}
	/**
	 * Synchronize user's bookshelf from supplier to local
	 * 
	 * @param UserAccount $user       The library current user
	 * @param array       $shelfItems The
	 * 
	 * @return SupplierConnector
	 */
	public function syncUserBookShelf(UserAccount $user, array $shelfItems)
	{
		throw new Exception(__FUNCTION__ . "() in class " . get_class($this) . " need to be overide to start!");
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
		throw new Exception(__FUNCTION__ . "() in class " . get_class($this) . " need to be overide to start!");
	}
	/**
	 * Removing a product from the book shelf
	 * 
	 * @param UserAccount $user
	 * @param Product     $product
	 * @param Library     $lib
	 * 
	 * @throws CoreException
	 * @return mixed
	 */
	public function removeBookShelfList(UserAccount $user, Product $product, Library $lib)
	{
		throw new Exception(__FUNCTION__ . "() in class " . get_class($this) . " need to be overide to start!");
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
		throw new Exception(__FUNCTION__ . "() in class " . get_class($this) . " need to be overide to start!");
	}
	/**
	 * Getting the default language and product type for a supplier
	 * 
	 * @return multitype:Language ProductType
	 */
	protected function _getDefaulLangNType()
	{
		$defaultLangIds = explode(',', $this->_supplier->getInfo('default_lang_id'));
		$defaultTypeIds = explode(',', $this->_supplier->getInfo('default_product_type_id'));
		return array(BaseServiceAbastract::getInstance('Language')->get($defaultLangIds[0]), BaseServiceAbastract::getInstance('ProductType')->get($defaultTypeIds[0]));
	}
	/**
	 * Importing the categories
	 *
	 * @param array $categoryNames The list of the category names
	 *
	 * @return array
	 */
	protected function _importCategories(array $categoryNames)
	{
		$transStarted = false;
		try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true; }
		try
		{
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
			$tmpFile = self::downloadFile($imageUrl, $tmpDir . DIRECTORY_SEPARATOR . md5($imageUrl));
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
	public static function downloadFile($url, $localFile, $timeout = null)
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
}