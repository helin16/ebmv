<?php
class SC_TW extends SupplierConnectorAbstract implements SupplierConn
{
	const CODE_SUCC = 100;
	const CODE_TOKEN_INVALID = 310;
	const CODE_TOKEN_EXPIRED = 900;
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
		$this->_importUrl = trim($urls === false ? null : $urls[0]);
		return $this->_importUrl;
	}
	private function _formatURL($url, $methodName)
	{
		return trim(str_replace('{method}', $methodName, str_replace('{SiteID}', $this->_libCode, $url)));
	}
	/**
	 * Gettht product List
	 * 
	 * @throws CoreException
	 * @return SimpleXMLElement
	 */
	public function getProductListInfo(ProductType $type = null)
	{
		$importUrl = $this->_formatURL($this->_importUrl, 'SyncBooks');
		$xml = $this->_getXmlFromUrl($importUrl, 1, 1, $type);
		if(!$xml instanceof SimpleXMLElement)
			throw new SupplierConnectorException('Can NOT get the pagination information from ' . $importUrl . '!');
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
		$importUrl = $this->_formatURL($this->_importUrl, 'SyncBooks');
		$xml = $this->_getXmlFromUrl($importUrl, $pageNo, $pageSize, $type);
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
		$result = $this->readUrl($url . '?' . http_build_query($params), SupplierConnectorAbstract::CURL_TIMEOUT);
		return new SimpleXMLElement($result);
	}
	/**
	 * validating the token
	 *
	 * @throws SupplierConnectorException
	 *
	 * @return string
	 */
	private function _validToken(UserAccount $user)
	{
		try
		{
			return $this->_getToken($user);
		}
		catch(Exception $e)
		{
			if($e instanceof SupplierConnectorException && in_array(trim($e->getCode()), array(self::CODE_TOKEN_EXPIRED, self::CODE_TOKEN_INVALID)))
				return $this->_getToken($user, true);
			throw $e;
		}
	}
	/**
	 * Getting the result block from JSON string
	 * 
	 * @param string $json The json string
	 * 
	 * @throws SupplierConnectorException
	 * @return mixed
	 */
	private function _getJsonResult($json)
	{
		$result = json_decode($json, true);
		if(!isset($result['results']) || !isset($result['status']))
			throw new SupplierConnectorException("System Error: supplier message invalid, contact admin for further support!");
		if(trim($result['status']) !== trim(self::CODE_SUCC))
			throw new SupplierConnectorException("System Error: can not sign for supplier(" . $this->_supplier->getName() .") has NOT got sigin url, contact admin for further support!", trim($result['status']));
		return $result['results'];
	}
	/**
	 * Getting the token for session
	 * 
	 * @param bool $forceNew Whether force to renew token
	 * 
	 * @return string
	 */
	private function _getToken(UserAccount $user, $forceNew = false)
	{
		if($forceNew === false && isset($_SESSION['supplier_token']) && isset($_SESSION['supplier_token'][$this->_supplier->getId()]) && ($token = trim($_SESSION['supplier_token'][$this->_supplier->getId()])) !== '')
			return $token;
		
		$url = $this->_formatURL($this->_importUrl, 'SignIn');
		$data = array('uid' => trim($user->getUsername()), 'pwd' => trim($user->getPassword()), 'partnerid' => trim($this->_supplier->getInfo('partner_id')));
		var_dump($url);
		$results = $this->readUrl($url, SupplierConnectorAbstract::CURL_TIMEOUT, $data);
		var_dump($results);
		$results = $this->_getJsonResult($results);
		if(!isset($results['token']) || ($token = trim($results['token'])) === '')
			throw new SupplierConnectorException("System Setting Error: can not sign for supplier(" . $this->_supplier->getName() .") has NOT got sigin url, contact admin for further support!");
			
		$_SESSION['supplier_token'][$this->_supplier->getId()] = $token;
		return $token;
	}
	/**
	 * Borrowing the book
	 * 
	 * @param Product     $product The product the user is trying to borrow
	 * @param UserAccount $user    Who is borrowing it
	 * 
	 * @return SupplierConnectorAbstract
	 */
	public function borrowBook(Product $product, UserAccount $user)
	{
		//todo!!!
		return $this;
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
		$token = $this->_validToken($user);
		$url = $this->_formatURL($this->_importUrl, 'getBookList');
		$params = array('uid' => $user->getUserName(), 'token' => $token, 'partnerid' => $this->_supplier->getInfo('partner_id'));
		var_dump($url);
		$result = $this->readUrl($url, self::CURL_TIMEOUT, $params);
		var_dump($result);
		return $this->_getJsonResult($result);
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
		$token = $this->_validToken($user);
		$url = explode(',', $this->_supplier->getInfo('view_url'));
		if($url === false || count($url) === 0)
			throw new SupplierConnectorException('Invalid view url for supplier: ' . $this->_supplier->getName());
		$url = $this->_formatURL($url[0], 'launchViewer');
		
		$returnUrls = explode(',', $this->_lib->getInfo('lib_url'));
		$currentUrl = (trim($_SERVER['SERVER_NAME']) === '' ? $returnUrls[0]: trim($_SERVER['SERVER_NAME'])) . '/mybookshelf.html';
		$params = array('isbn' => $product->getAttribute('isbn'), 'no' => $product->getAttribute('cno'), 'token' => $token, 'returnUrl' => $currentUrl, 'partnerid' => $this->_supplier->getInfo('partner_id'));
		$results = $this->_getJsonResult($this->readUrl($url, SupplierConnectorAbstract::CURL_TIMEOUT, $params), true);
		if(!isset($results['url']) || ($readurl = trim($results['url'])) === '')
			throw new SupplierConnectorException("System Error: can not get the online reading url for supplier(" . $this->_supplier->getName() ."), contact admin for further support!");
		return $readurl;
	}
}