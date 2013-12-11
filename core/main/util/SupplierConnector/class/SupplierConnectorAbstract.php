<?php
/**
 * Supplier connector script for suppliers
 *
 * @package    Core
 * @subpackage Utils
 * @author     lhe<helin16@gmail.com>
 */
class SupplierConnectorAbstract
{
	const CURL_TIMEOUT = 120000;
	/**
	 * @var Supplier
	 */
	protected $_supplier;
	/**
	 * The library we are dealing with
	 * 
	 * @var Library
	 */
	protected $_lib;
	/**
	 * The connectors
	 * 
	 * @var array
	 */
	protected static $_connectors = array();
	/**
	 * The id of the imported products
	 * 
	 * @var array
	 */
	protected $_importedProductIds = array();
	/**
	 * singleton getter
	 * 
	 * @param Supplier $supplier The supplier
	 * @param Library  $lib      The library
	 * 
	 * @return SupplierConnectorAbstract
	 */
	public static function getInstance(Supplier $supplier, Library $lib)
	{
		$className = $supplier->getConnector();
		$key = trim($supplier->getId() . "+" . $lib->getId());
		if(!isset(self::$_connectors[$supplier->getId()]))
		{
			if(!($sc = new $className($supplier, $lib)) instanceof SupplierConn)
				throw new SupplierConnectorException("$className is NOT a SupplierConn!");
			self::$_connectors[$key] = $sc;
		}
		return self::$_connectors[$key];
	}
	/**
	 * construtor
	 * @param Supplier $supplier The supplier
	 * @param Library  $lib      The library
	 */
	public function __construct(Supplier $supplier, Library $lib)
	{
		$this->_supplier = $supplier;
		$this->_lib = $lib;
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
	 * @return multitype:ProductType
	 */
	public function getImportProductTypes()
	{
		$importTypeIds = explode(',', $this->_supplier->getInfo('stype_ids'));
		return BaseServiceAbastract::getInstance('ProductType')->findByCriteria('id in (' . implode(', ', $importTypeIds) . ')', array());
	}
	/**
	 * resetting the imported product ids
	 * 
	 * @return SupplierConnectorAbstract
	 */
	public function resetImportedProductIds()
	{
		$this->_importedProductIds = array();
		return $this;
	}
	/**
	 * Getting the imported product ids
	 * 
	 * @return multitype:int
	 */
	public function getImportedProductIds()
	{
		return $this->_importedProductIds;
	}
	/**
	 * removing all the unimported products, if the supplier not giving us that information anymore, then we treated it as an remove from our system
	 * 
	 * @param bool $resetImportedPids Whether we reset the imported product ids after removing
	 * 
	 * @return SupplierConnectorAbstract
	 */
	public function rmUnImportedProducts($resetImportedPids = true)
	{
		$unImportedProducts = $this->_supplier->getProducts($this->_importedProductIds);
		foreach($unImportedProducts as $product)
		{
			$product->setActive(false);
			BaseServiceAbastract::getInstance('Product')->save($product);
		}
		if($resetImportedPids === true)
			$this->resetImportedProductIds();
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::importProducts()
	 */
	public function importProducts($productList, $index = null)
	{
		$products = array ();
		if (trim ( $index ) !== '')
		{
			$product = $this->_importProduct(SupplierConnectorProduct::getProduct($productList[$index]));
			$products[] = $product;
			$this->_importedProductIds[] = $product->getId();
		}
		else 
		{
			foreach($productList as $child)
			{
				$product = $this->_importProduct(SupplierConnectorProduct::getProduct($child));
				$products[] = $product;
				$this->_importedProductIds[] = $product->getId();
			}
		}
		return $products;
	}
	/**
	 * 
	 * Importing the product
	 *
	 * @param SupplierConnectorProduct $productInfo The SupplierConnectorProduct object
	 * 
	 * @throws SupplierConnectorException
	 * @return unknown
	 */
	protected function _importProduct(SupplierConnectorProduct $productInfo)
	{
		$transStarted = false;
		try { Dao::beginTransaction();} catch (Exception $ex) {$transStarted = true; }
		try
		{
			$infoArray = $productInfo->getArray();
			if(count($langs = BaseServiceAbastract::getInstance('Language')->getLangsByCodes($infoArray['languageCodes'])) === 0)
				throw new SupplierConnectorException("Invalid lanuage codes: " . implode(', ', $infoArray['languageCodes']));
			if(!($type = BaseServiceAbastract::getInstance('ProductType')->getByName($infoArray['productTypeName'])) instanceof ProductType)
				throw new SupplierConnectorException("Invalid ProductType: " . $infoArray['productTypeName']);
			
			//getting the categories
			if(count($infoArray['categories']) >0)
				$categories = $this->_importCategories($infoArray['categories']);
			
			
			//downloading images
			$imgs = array();
			foreach(array_unique($infoArray['attributes']['image_thumb']) as $imgUrl)
				$imgs[] = $this->_importImage($imgUrl);
			$infoArray['attributes']['image_thumb'] = $imgs;
			
			//updating the product
			if(($product = BaseServiceAbastract::getInstance('Product')->findProductWithISBNnCno($infoArray['attributes']['isbn'][0], $infoArray['attributes']['cno'][0], $this->_supplier)) instanceof Product)
			{
				//remove all categoies
				$product->removeAllCategories();
				
				//delete the thumb
				if(!($thumbs = explode(',', $product->getAttribute('image_thumb'))) !== false)
					BaseServiceAbastract::getInstance('Asset')->removeAssets($thumbs);
				//delete the img
				if(!($imgs = explode(',', $product->getAttribute('image'))) !== false)
					BaseServiceAbastract::getInstance('Asset')->removeAssets($imgs);
				//deleting the thumb and image for the product
				BaseServiceAbastract::getInstance('ProductAttribute')->removeAttrsForProduct($product, array('image_thumb', 'image'));
				$product = BaseServiceAbastract::getInstance('Product')->updateProduct($product, $infoArray['title'], $type, $this->_supplier, $categories, $langs, $infoArray['attributes']); 
			}
			//creating new product
			else
				$product = BaseServiceAbastract::getInstance('Product')->createProduct($infoArray['title'], $type, $this->_supplier, $categories, $langs, $infoArray['attributes']);
			
			//added the library
			$product->updateLibrary($this->_lib, $infoArray['copies']['onlineRead']['avail'], $infoArray['copies']['onlineRead']['total'], $infoArray['copies']['download']['avail'], $infoArray['copies']['download']['total']);
			
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
				$this->_mkDir($tmpDir);
			
			$paths = parse_url($imageUrl);
			$paths = explode('/', $paths['path']);
			$tmpFile = self::downloadFile($imageUrl, $tmpDir . DIRECTORY_SEPARATOR . md5($imageUrl));
			//checking whether the file is an image
			try 
			{ 
				if (($size = getimagesize($tmpFile)) === false)
					throw new SupplierConnectorException('Can NOT download the image');
			}
			catch(Exception $e) {return null;}
			
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
	 * Making sure all the path has been made where it should be
	 * 
	 * @param string $dir The wanted path
	 * 
	 * @return SupplierConnectorAbstract
	 */
	protected function _mkDir($dir)
	{
		$tmpDirs = explode('/', $dir);
		for($i = 1; $i <= count($tmpDirs); $i++)
		{
			$tmpD = implode('/', array_slice($tmpDirs, 0, $i));
			if(trim($tmpD) !== '' && !is_dir($tmpD))
				mkdir($tmpD);
		}
		return $this;
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
	/**
	 * read from a url
	 * 
	 * @param string $url     The url
	 * @param int    $timeout The timeout in seconds
	 * @param array  $data    The data we are POSTING
	 * 
	 * @return mixed
	 */
	public static function readUrl($url, $timeout = null, array $data = array())
	{
		$timeout = trim($timeout);
		$options = array(
				CURLOPT_RETURNTRANSFER => true, 
				CURLOPT_TIMEOUT =>  (!is_numeric($timeout) ? 8*60*60 : $timeout), // set this to 8 hours so we dont timeout on big files
				CURLOPT_URL     => $url
		);
		if(count($data) > 0)
		{
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = http_build_query($data);
		}
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$data =curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}