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
	/**
	 * @var Supplier
	 */
	protected $_supplier;
	/**
	 * The connectors
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
	 * 
	 * @return SupplierConnectorAbstract
	 */
	public static function getInstance(Supplier $supplier)
	{
		$className = $supplier->getConnector();
		if(!isset(self::$_connectors[$supplier->getId()]))
		{
			if(!($sc = new $className($supplier)) instanceof SupplierConn)
				throw new CoreException("$className is NOT a SupplierConn!");
			self::$_connectors[$supplier->getId()] = $sc;
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
			BaseServiceAbastract::getInstance('Product')->removeFromProductBySupplier($product, $this->_supplier);
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
			$product = $this->_importProduct($productList[$index]);
			$products[] = $product;
			$this->_importedProductIds[] = $product->getId();
		}
		else 
		{
			foreach($productList as $child)
			{
				$product = $this->_importProduct($child);
				$products[] = $product;
				$this->_importedProductIds[] = $product->getId();
			}
		}
		return $products;
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
		$langCodes = explode('+', $this->_getAttribute($xml, 'Language'));
		if(count($langs = BaseServiceAbastract::getInstance('Language')->getLangsByCodes($langCodes)) === 0)
			throw new Exception("Invalid lanuage codes: " . implode(', ', $langCodes));
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
	
			$categories = (count($categories) > 0 ? $categories : $this->_importCategories(explode('/', $this->_getAttribute($xml, 'BookType'))));
			//updating the product
			if(($product = BaseServiceAbastract::getInstance('Product')->findProductWithISBNnCno($isbn, $no)) instanceof Product)
			{
				//delete the thumb
				if(!($thumbs = explode(',', $product->getAttribute('image_thumb'))) !== false)
					BaseServiceAbastract::getInstance('Asset')->removeAssets($thumbs);
				//delete the img
				if(!($imgs = explode(',', $product->getAttribute('image'))) !== false)
					BaseServiceAbastract::getInstance('Asset')->removeAssets($imgs);
				//deleting the thumb and image for the product
				BaseServiceAbastract::getInstance('ProductAttribute')->removeAttrsForProduct($product, array('image_thumb', 'image'));
				
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
						$langs,
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
						$langs,
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
					throw new Exception('Can NOT download the image');
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