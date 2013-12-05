<?php
class SC_XinHua extends SupplierConnectorAbstract implements SupplierConn
{
	const CODE_SUCC = 0;
	/**
	 * Where to get the product list
	 * @var string
	 */
	private $_wsdlUrl;
	/**
	 * construtor
	 * 
	 * @param Supplier $supplier The supplier
	 * @param Library  $lib      The library
	 */
	public function __construct(Supplier $supplier, Library $lib)
	{
		parent::__construct($supplier, $lib);
		$this->_getImportUrl();
	}
	/**
	 * Getting the import url
	 *
	 * @return string
	 */
	public function _getImportUrl()
	{
		if(trim($this->_wsdlUrl) !== '')
			return $this->_wsdlUrl;
		
		$urls = explode(',', $this->_supplier->getInfo('import_url'));
		$this->_wsdlUrl = ($urls === false ? null : $urls[0]);
		return $this->_wsdlUrl;
	}
	/**
	 * Gettht product List
	 * 
	 * @param ProductType $type The product type we are getting the xml for
	 * 
	 * @throws CoreException
	 * @return SimpleXMLElement
	 */
	public function getProductListInfo(ProductType $type = null)
	{
		$params = array("SiteID" => $this->_lib->getInfo('aus_code'), "Index" => 1, "Size" => 1);
		if($type instanceof ProductType)
			$params['type'] = strtolower(trim($type->getName()));
		$xml = $this->_getFromSoap($this->_wsdlUrl, "GetBookList", $params);
		if(!$xml instanceof SimpleXMLElement)
			throw new CoreException('Can NOT get the pagination information from ' . $wsdl . '!');
		$array = array();
		foreach($xml->attributes() as $key => $value)
			$array[$key] = trim($value);
		return $array;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getProductList()
	 */
	public function getProductList($pageNo = 1, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, ProductType $type = null)
	{
		if(trim($pageSize) === '')
		{
			$pageInfo = $this->getProductListInfo($type);
			$pageSize = $pageInfo['totalRecords'];
		}
		$params = array("SiteID" => $this->_lib->getInfo('aus_code'), "Index" => 1, "Size" => $pageSize);
		if($type instanceof ProductType)
			$params['type'] = strtolower(trim($type->getName()));
		$array = array();
		$xml = $this->_getFromSoap($this->_wsdlUrl, "GetBookList", $params);
		foreach($xml->children() as $childXml)
		{
			$array[] = $childXml;
		}
		return $array;
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
	 * Getting the book shelf
	 * 
	 * @param UserAccount $user
	 * 
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function getBookShelfList(UserAccount $user)
	{
		$username = trim($user->getUserName());
		$libCode = trim($this->_lib->getInfo('aus_code'));
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
	 * (non-PHPdoc)
	 * @see SupplierConnectorAbstract::syncUserBookShelf()
	 */
	public function syncUserBookShelf(UserAccount $user, array $shelfItems)
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
	 * 
	 * @throws CoreException
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function addToBookShelfList(UserAccount $user, Product $product)
	{
		$username = trim($user->getUserName());
		$libCode = trim($this->_lib->getInfo('aus_code'));
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
	 * 
	 * @throws CoreException
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function removeBookShelfList(UserAccount $user, Product $product)
	{
		$username = trim($user->getUserName());
		$libCode = trim($this->_lib->getInfo('aus_code'));
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
		$urlParams = array('SiteID' => $this->_lib->getInfo('aus_code'),
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
		BaseServiceAbastract::getInstance('ProductShelfItem')->borrowItem($user, $product, $this->_lib, $this->_supplier);
		if(trim($xml->Code) !== trim(self::CODE_SUCC))
			throw new Exception("Connector Error: " . trim($xml->Value));
		return trim($xml->Value);
	}
}