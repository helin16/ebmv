<?php
class SC_TW extends SupplierConnectorAbstract implements SupplierConn
{
	const CODE_SUCC = 200;
	/**
	 * Where to get the product list
	 * @var string
	 */
	private $_importUrl;
	/**
	 * The library's code
	 * 
	 * @var string
	 */
	private $_libCode = '';
	/**
	 * construtor
	 * 
	 * @param Supplier $supplier The supplier
	 * @param Library  $lib      The library
	 */
	public function __construct(Supplier $supplier, Library $lib)
	{
		parent::__construct($supplier, $lib);
		$this->_libCode = $this->_lib->getInfo('aus_code');
		$this->_getImportUrl();
	}
	/**
	 * Getting the import url
	 *
	 * @return string
	 */
	public function _getImportUrl()
	{
		if(trim($this->_importUrl) !== '')
			return $this->_importUrl;
		
		$urls = explode(',', $this->_supplier->getInfo('import_url'));
		$this->_importUrl = str_replace('{SiteID}', $this->_libCode, ($urls === false ? null : $urls[0]));
		return $this->_importUrl;
	}
	/**
	 * Gettht product List
	 * 
	 * @throws CoreException
	 * @return SimpleXMLElement
	 */
	public function getProductListInfo(ProductType $type = null)
	{
		$xml = $this->_getXmlFromUrl($this->_importUrl, 1, 1, $type);
		if(!$xml instanceof SimpleXMLElement)
			throw new CoreException('Can NOT get the pagination information from ' . $this->_importUrl . '!');
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
		$array = array();
		$xml = $this->_getXmlFromUrl($this->_importUrl, $pageNo, $pageSize, $type);
		foreach($xml->children() as $childXml)
		{
			$array[] = $childXml;
		}
		return $array;
	}
	/**
	 * Getting the xml from url
	 * 
	 * @param string      $url
	 * @param int         $pageNo
	 * @param int         $pageSize
	 * @param ProductType $type
	 * @param string      $format
	 * 
	 * @return SimpleXMLElement
	 */
	private function _getXmlFromUrl($url, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, ProductType $type = null, $format = 'xml')
	{
		$params = array('format' => $format, 'size' => $pageSize, 'index' => $pageNo);
		if($type instanceof ProductType)
			$params['type'] = strtolower(trim($type->getName()));
		$result = $this->readUrl($url . '?' . http_build_query($params), 120000);
		return new SimpleXMLElement($result);
	}
	/**
	 * Getting the token for session
	 * 
	 * @param bool $forceNew Whether force to renew token
	 * 
	 * @return string
	 */
	private function _getToken($forceNew = false)
	{
		if($forceNew === false && isset($_SESSION['supplier_token']) && isset($_SESSION['supplier_token'][$this->_supplier->getId()]) && ($token = trim($_SESSION['supplier_token'][$this->_supplier->getId()])) !== '')
			return $token;
		
		$urls = explode(',', $this->_supplier->getInfo('signin_url'));
		$url = str_replace('{SiteID}', $this->_libCode, ($urls === false ? null : $urls[0]));
		throw new Exception(__FUNCTION__ . " in " . __CLASS__ . ' is still under construction!');
		$token = '';
		$_SESSION['supplier_token'][$this->_supplier->getId()] = $token;
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
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConnectorAbstract::syncUserBookShelf()
	 */
	public function syncUserBookShelf(UserAccount $user, array $shelfItems)
	{
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
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getOnlineReadUrl()
	 */
	public function getOnlineReadUrl(Product $product, UserAccount $user)
	{
		$token = $this->_getToken();
	}
}