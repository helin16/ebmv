<?php
/**
 * The connector script for 读览天下
 * @author lhe
 *
 */
class SC_DLTX extends SupplierConnectorAbstract implements SupplierConn
{
	const APP_ID = 'f239776c84236b30c83e86993666cc99';
	const APP_SECRET = '20b832cd14b69a02b32c36fbd66f531a';
	public static $cache = array();
	/**
	 * Getting the json from url
	 *
	 * @param string      $url
	 * @param string      $method
	 * @param int         $pageNo
	 * @param int         $pageSize
	 * @param ProductType $type
	 *
	 * @return SimpleXMLElement
	 */
	private function _getJsonFromUrl($url, $pageNo = 1)
	{
		$url = str_replace('{page_no}', $pageNo, $url);
		if($this->_debugMode === true)
			SupplierConnectorAbstract::log($this, '::reading from url: ' . $url , __FUNCTION__);
		$result = SupplierConnectorAbstract::readUrl($url, BmvComScriptCURL::CURL_TIMEOUT);
		if($this->_debugMode === true)
			SupplierConnectorAbstract::log($this, '::got results:' . $result , __FUNCTION__);
		$result = json_decode($result, true);
		if(intval($result['status']) !== 1)
			throw new Exception('Error Occurred, when trying to fetch data from "' . $url . '": ' . $result['error']);
		if(count($result['data']) > 0)
		{
			if(!isset(self::$cache['data']))
				self::$cache['data'] = array();
			self::$cache['data'] = array_merge(self::$cache['data'], $result['data']);
		}
		if(count(self::$cache['data']) < intval($result['total']) )
			$this->_getJsonFromUrl($url, $pageNo + 1);
		return $this;
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
// 		$importUrl =trim($this->_supplier->getInfo('import_url'));
		$importUrl = 'http://public.dooland.com/v1/Magazine/lists/page/{page_no}';

		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::got import url:' . $importUrl, __FUNCTION__);
		$this->_getJsonFromUrl($importUrl);

		if(count(self::$cache['data']) === 0)
			throw new SupplierConnectorException('Can NOT any data information from ' . $importUrl . '!');
		$array = self::$cache['data'];
// 		$array = SupplierConnectorProduct::getInitPagination($xml);
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
		$params = array('type'=>'new', 'pagesize' => $pageSize, 'page' => $pageNo, 'siteid' => trim($this->_lib->getInfo('aus_code')));
		$importUrl =trim($this->_supplier->getInfo('import_url'));
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::got import url:' . $importUrl, __FUNCTION__);

		$xml = $this->_getXmlFromUrl($importUrl, 'new', $pageNo, $pageSize, $type);
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
	 * @see SupplierConn::getOnlineReadUrl()
	 */
	public function getOnlineReadUrl(Product $product, UserAccount $user)
	{
		$readurl = $this->_supplier->getInfo('view_url');
		if($readurl === false || count($readurl) === 0)
			throw new SupplierConnectorException('Invalid view url for supplier: ' . $this->_supplier->getName());
		$readurl = str_replace('{cno}', $product->getAttribute('cno'), $readurl);
		return $readurl;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getProduct()
	 */
	public function getProduct(Product $product)
	{
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Getting Product from supplier:', __FUNCTION__);

		$params = array("siteid" => trim($this->_lib->getInfo('aus_code')),
				'type' => 'one',
				'magid' =>  trim($product->getAttribute('cno')),
				'isbn' => trim($product->getAttribute('isbn'))
		);
		$url = $this->_supplier->getInfo('import_url') . '?' . http_build_query($params);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Sending params to :' . $url, __FUNCTION__);

		$results = SupplierConnectorAbstract::readUrl($url, BmvComScriptCURL::CURL_TIMEOUT);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Got results:' . print_r($results, true), __FUNCTION__);

		return SupplierConnectorProduct::getProduct(new SimpleXMLElement($results));
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getBookShelfList()
	 */
	public function getBookShelfList(UserAccount $user) {}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::syncUserBookShelf()
	 */
	public function syncUserBookShelf(UserAccount $user, array $shelfItems) {}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::addToBookShelfList()
	 */
	public function addToBookShelfList(UserAccount $user, Product $product) {}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::removeBookShelfList()
	 */
	public function removeBookShelfList(UserAccount $user, Product $product) {}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::borrowProduct()
	 */
	public function borrowProduct(Product &$product, UserAccount $user) {}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::returnProduct()
	 */
	public function returnProduct(Product &$product, UserAccount $user) {
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getDownloadUrl()
	 */
	public function getDownloadUrl(Product $product, UserAccount $user) {}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::downloadCatalog()
	 */
	public function downloadCatalog(ProductType $type, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE){}

	/**
	 * Getting a fake xml element for product
	 *
	 * @param ProductType $type
	 *        	The type of these magazines
	 * @param UDate $date
	 *        	The issue date
	 *
	 * @return SimpleXMLElement
	 */
	private function _fakeProduct(ProductType $type, $data, Product $product = null) {
		$readOnlineCopy = 0;
// 		// check whether the magazine still there from supplier
// 		$productKey = $product instanceof Product ? $product->getAttribute ( 'cno' ) : $this->_getProductKey($date);
// 		if (($coverImg = $this->_getCoverImage ( $productKey )) !== '')
// 			$readOnlineCopy = 1;

		$xml = new SimpleXMLElement ( '<' . $type->getName () . '/>' );
		$xml->BookName = $product instanceof Product ? $product->getTitle () : $data['title'];
		$xml->Isbn = $product instanceof Product ? $product->getAttribute ( 'isbn' ) : '';
		$xml->NO = $product instanceof Product ? $product->getAttribute ( 'cno' ) : $data['magazineId'];
		$xml->Author = $product instanceof Product ? $product->getAttribute ( 'author' ) : $this->_supplier->getName ();
		$xml->Press = $product instanceof Product ? $product->getAttribute ( 'publisher' ) : $this->_supplier->getName ();
		$xml->PublicationDate = $product instanceof Product ? $product->getAttribute ( 'publish_date' ) : $data['pubDate'];
		$xml->Words = '';
		$xml->FrontCover = $coverImg;
		$xml->Introduction = $product instanceof Product ? $product->getAttribute ( 'description' ) : $this->_supplier->getName () . ': ' . $date->format ( 'd F Y' );
		$xml->Cip = '';
		$xml->SiteID = trim ( $this->_lib->getInfo ( 'aus_code' ) );
		$xml->Language = trim($this->_getLanguageCode());

		$publishDate = new UDate ( $xml->PublicationDate );
		$xml->BookType = ($bookName = trim($this->_supplier->getName())) . '/' . ($bookName . $publishDate->format ('Y')) . '/' . ($bookName . $publishDate->format('m'));
		$copiesXml = $xml->addChild( 'Copies' );
		$readOnline = $copiesXml->addChild ($this->_getLibOwnsType ( LibraryOwnsType::ID_ONLINE_VIEW_COPIES )->getCode ());
		$readOnline->Available = $readOnlineCopy;
		$readOnline->Total = 1;
		$download = $copiesXml->addChild ( $this->_getLibOwnsType ( LibraryOwnsType::ID_DOWNLOAD_COPIES )->getCode () );
		$download->Available = $readOnlineCopy;
		$download->Total = 1;
		return $xml;
	}
}