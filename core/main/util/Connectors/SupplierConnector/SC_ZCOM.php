<?php
class SC_ZCOM extends SupplierConnectorAbstract implements SupplierConn
{
	/**
	 * Getting the formatted url
	 * 
	 * @param string $url
	 * @param string $methodName
	 * 
	 * @return string
	 */
	private function _formatURL($url, $methodName)
	{
		return trim(str_replace('{method}', $methodName, str_replace('{SiteID}', $this->_lib->getInfo('aus_code'), $url)));
	}
	/**
	 * Gettht product List
	 * 
	 * @throws CoreException
	 * @return SimpleXMLElement
	 */
	public function getProductListInfo(ProductType $type = null)
	{
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Getting product list info:', __FUNCTION__);
		$importUrl = $this->_formatURL($this->_supplier->getInfo('import_url'), 'SyncBooks');
		
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::got import url:' . $importUrl, __FUNCTION__);
		$xml = $this->_getXmlFromUrl($importUrl, 1, 1, $type);
		
		if(!$xml instanceof SimpleXMLElement)
			throw new SupplierConnectorException('Can NOT get the pagination information from ' . $importUrl . '!');
		$array = SupplierConnectorProduct::getInitPagination($xml);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::got array from results:' . print_r($array, true) , __FUNCTION__);
		return $array;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getProductList()
	 */
	public function getProductList($pageNo = 1, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, ProductType $type = null, $onceOnly = false)
	{
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Getting product list:', __FUNCTION__);
		if(trim($pageSize) === '')
		{
			$pageInfo = $this->getProductListInfo($type);
			$pageSize = $pageInfo['totalRecords'];
			if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::pageInfo:' . print_r($pageInfo, true), __FUNCTION__);
		}
		
		$array = array();
		$importUrl = $this->_formatURL($this->_supplier->getInfo('import_url'), 'SyncBooks');
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::got import url:' . $importUrl, __FUNCTION__);
		
		$xml = $this->_getXmlFromUrl($importUrl, $pageNo, $pageSize, $type);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::got results:' . (!$xml instanceof SimpleXMLElement ? trim($xml) : $xml->asXML()) , __FUNCTION__);
		foreach($xml->children() as $childXml)
		{
			$array[] = $childXml;
		}
		
		if($onceOnly === false)
		{
			//next page
			$attributes = $xml->attributes();
			if(isset($attributes['totalPages']) && $pageNo < $attributes['totalPages'])
				$array = array_merge($array, $this->getProductList($pageNo + 1, $pageSize, $type));
		}
		return $array;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::borrowProduct()
	 */
	public function borrowProduct(Product &$product, UserAccount $user)
	{
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Borrowing Product: uid:' . $user->getId() . ', pid: ' . $product->getId() , __FUNCTION__);
		if(!($supplier = $product->getSupplier()) instanceof Supplier)
			throw new SupplierConnectorException('System Error: The wanted book/magazine/newspaper does NOT have a supplier linked!');
		if($supplier->getId() !== $this->_supplier->getId())
			throw new SupplierConnectorException('System Error: The wanted book/magazine/newspaper does NOT belong to this supplier!');
		
		$token = $this->_validToken($user);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::Got token: ' . $token , __FUNCTION__);
		
		$url = $this->_formatURL($this->_supplier->getInfo('import_url'), 'bookShelf');
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::Got url:' . $url , __FUNCTION__);
		
		$params = array('partnerid' => $this->_supplier->getInfo('partner_id'), 'uid' => $user->getUserName(), 'token' => $token, 'isbn' => $product->getAttribute('isbn'), 'no' => $product->getAttribute('cno'));
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::submiting to url with params' . print_r($params, true) , __FUNCTION__);
		
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::reading from url (' . $url . ') with (' . print_r($params, true) . ', type = ) with timeout limit: ' . BmvComScriptCURL::CURL_TIMEOUT , __FUNCTION__);
		$result = SupplierConnectorAbstract::readUrl($url, BmvComScriptCURL::CURL_TIMEOUT, $params);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::Got results:' . print_r($result, true) , __FUNCTION__);
		
		$results = $this->_getJsonResult($result, 'results');
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::Decoded json:' . print_r($results, true) , __FUNCTION__);
		//TODO:: need to update the expiry date of the shelfitem
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::returnProduct()
	 */
	public function returnProduct(Product &$product, UserAccount $user)
	{
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Returning Product: uid:' . $user->getId() . ', pid: ' . $product->getId() , __FUNCTION__);
		if(!($supplier = $product->getSupplier()) instanceof Supplier)
			throw new SupplierConnectorException('System Error: The wanted book/magazine/newspaper does NOT have a supplier linked!');
		if($supplier->getId() !== $this->_supplier->getId())
			throw new SupplierConnectorException('System Error: The wanted book/magazine/newspaper does NOT belong to this supplier!');
		
		$token = $this->_validToken($user);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::Got token: ' . $token , __FUNCTION__);
		
		$url = $this->_formatURL($this->_supplier->getInfo('import_url'), 'bookShelf');
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::Got url:' . $url , __FUNCTION__);
		
		$params = array('partnerid' => $this->_supplier->getInfo('partner_id'), 'uid' => $user->getUserName(), 'token' => $token, 'isbn' => $product->getAttribute('isbn'), 'no' => $product->getAttribute('cno'));
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::reading from url (' . $url . ') with (' . print_r($params, true) . ', type = "DELETE") with timeout limit: ' . BmvComScriptCURL::CURL_TIMEOUT , __FUNCTION__);
		
		$result = SupplierConnectorAbstract::readUrl($url, BmvComScriptCURL::CURL_TIMEOUT, $params, 'DELETE');
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::Got result: ' . print_r($result, true) , __FUNCTION__);
		
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
		$url = $this->_formatURL($this->_supplier->getInfo('import_url'), 'bookShelf');
		$params = array('partnerid' => $this->_supplier->getInfo('partner_id'), 'uid' => $user->getUserName(), 'token' => $token);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::reading from url (' . $url . ') with (' . print_r($params, true) . ', type = "GET") with timeout limit: ' . BmvComScriptCURL::CURL_TIMEOUT , __FUNCTION__);
		
		$result = SupplierConnectorAbstract::readUrl($url . '?' . http_build_query($params), BmvComScriptCURL::CURL_TIMEOUT);
		return $this->_getJsonResult($result, 'bookList');
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
		$currentUrl = 'http://' . (trim($_SERVER['SERVER_NAME']) === '' ? $returnUrls[0]: trim($_SERVER['SERVER_NAME'])) . '/mybookshelf.html';
		$params = array('isbn' => $product->getAttribute('isbn'), 'no' => $product->getAttribute('cno'), 'token' => $token, 'returnUrl' => $currentUrl, 'partnerid' => $this->_supplier->getInfo('partner_id'));
		
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::reading from url (' . $url . ') with (' . print_r($params, true) . ', type = "POST") with timeout limit: ' . BmvComScriptCURL::CURL_TIMEOUT , __FUNCTION__);
		$results = $this->_getJsonResult(SupplierConnectorAbstract::readUrl($url, BmvComScriptCURL::CURL_TIMEOUT, $params), 'results');
		if(!isset($results['url']) || ($readurl = trim($results['url'])) === '')
			throw new SupplierConnectorException("System Error: can not get the online reading url for supplier(" . $this->_supplier->getName() ."), contact admin for further support!");
		return $readurl;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getProduct()
	 */
	public function getProduct($isbn, $no)
	{
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Getting Product from supplier:', __FUNCTION__);
		
		$params = array("SiteID" => trim($this->_lib->getInfo('aus_code')),
				'Isbn' => trim($isbn),
				'NO' => trim($no),
				'format' => 'xml'
		);
		$url = $this->_formatURL($this->_supplier->getInfo('import_url'), "getBookInfo") . '?' . http_build_query($params);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Sending params to :' . $url, __FUNCTION__);
		
		$results = SupplierConnectorAbstract::readUrl($url, BmvComScriptCURL::CURL_TIMEOUT);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Got results:' . print_r($results, true), __FUNCTION__);
		
		return SupplierConnectorProduct::getProduct(new SimpleXMLElement($results));
	}
}