<?php
class SC_CIO extends SupplierConnectorAbstract implements SupplierConn
{
	private $urls = array(
		'Happy Chinese' => 'http://www.chinesecio.com/cms/zh-hans/courseware/happy-chinese',
		'Chinese Crash Course' => 'http://www.chinesecio.com/cms/en/course/chinese-crash-course',
		'Chinese 900' => 'http://www.chinesecio.com/cms/en/course/chinese-900'
	);
	/**
	 * Getting the library owns type
	 *
	 * @param unknown $typeId
	 *
	 * @return LibraryOwnsType
	 */
	private function _getLibOwnsType($typeId) {
		if (! isset ( self::$_cache ['libType'] ))
			self::$_cache ['libType'] = array ();
	
		if (! isset ( self::$_cache ['libType'] [$typeId] ))
			self::$_cache ['libType'] [$typeId] = LibraryOwnsType::get ( $typeId );
	
		return self::$_cache ['libType'] [$typeId];
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getProductList()
	 */
	public function getProductList($pageNo = 1, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, ProductType $type = null, $onceOnly = false) {
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, 'Getting product list:', __FUNCTION__ );
		$products = array ();
		$start = (($pageNo - 1) * $pageSize);
		for($i = $start; $i < count($this->urls); $i ++) {
			// if we are only try to grab one lot
			if (($i >= ($start + $pageSize)) && $onceOnly === true)
				break;
			$products[] = $this->_fakeProduct (ProductType::get(ProductType::ID_COURSE), $i);
		}
		return $products;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getProductListInfo()
	 */
	public function getProductListInfo(ProductType $type = null) {
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, 'Getting product list info:', __FUNCTION__ );
		$array = SupplierConnectorProduct::getInitPagination ( null, count($this->urls), 1, DaoQuery::DEFAUTL_PAGE_SIZE );
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, '::got array from results:' . print_r ( $array, true ), __FUNCTION__ );
		return $array;
	}
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
	private function _fakeProduct(ProductType $type) {
		$readOnlineCopy = 0;
		// check whether the magazine still there from supplier
		if (($coverImg = $this->_getCoverImage ( $productKey )) !== '')
			$readOnlineCopy = 1;
	
		$xml = new SimpleXMLElement ( '<' . $type->getName () . '/>' );
		$xml->BookName = $product instanceof Product ? $product->getTitle () : $this->_supplier->getName () . ': ' . $date->format ( 'd/F/Y' );
		$xml->Isbn = $product instanceof Product ? $product->getAttribute ( 'isbn' ) : '9789629964245';
		$xml->NO = $productKey;
		$xml->Author = $product instanceof Product ? $product->getAttribute ( 'author' ) : $this->_supplier->getName ();
		$xml->Press = $product instanceof Product ? $product->getAttribute ( 'publisher' ) : $this->_supplier->getName ();
		$xml->PublicationDate = $product instanceof Product ? $product->getAttribute ( 'publish_date' ) : $date->format ( 'Y-F-d' );
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
	/**
	 * Fetching html from URL
	 * 
	 * @param string $url The url
	 * 
	 * @return mixed
	 */
	private function _getHTMLFromCache($url)
	{
		$key = md5($url);
		//try to use apc
		if(extension_loaded('apc') && ini_get('apc.enabled'))
		{
			if(apc_exists($key))
				return apc_fetch($key);
			$html = BmvComScriptCURL::readUrl ( $url );
			apc_add($key, $html);
			return $html;
		}
		return BmvComScriptCURL::readUrl ( $url );
	}
	/**
	 * Getting the HTML from the url
	 *
	 * @param string $url
	 *        	The url
	 *
	 * @throws SupplierConnectorException
	 * @return NULL DOMDocument
	 */
	private function _getHTML($productKey) {
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, 'Getting HTML for productKey: ' . $productKey, __FUNCTION__ );
		$url = explode ( ',', $this->_supplier->getInfo ( 'view_url' ) );
		if ($url === false || count ( $url ) === 0)
			throw new SupplierConnectorException ( 'Invalid view url for supplier: ' . $this->_supplier->getName () );
		$url = $this->_formatURL ( $url[0], $productKey );
		$html = self::_getHTMLFromCache( $url );
		// checking whether we've got some html
		if (trim ( $html ) === '') {
			if ($this->_debugMode === true)
				SupplierConnectorAbstract::log ( $this, 'Got empty html from url:' . $url, __FUNCTION__ );
			return null;
		}
		// load this into DOMDocument
		$doc = new DOMDocument ();
		if (($loaded = @$doc->loadHTML ( $html )) !== true) {
			if ($this->_debugMode === true)
				SupplierConnectorAbstract::log ( $this, 'Failed to load html into DOMDocument!', __FUNCTION__ );
			return null;
		}
		return $doc;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getBookShelfList()
	 */
	public function getBookShelfList(UserAccount $user) {
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::syncUserBookShelf()
	 */
	public function syncUserBookShelf(UserAccount $user, array $shelfItems) {
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::addToBookShelfList()
	 */
	public function addToBookShelfList(UserAccount $user, Product $product) {
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::removeBookShelfList()
	 */
	public function removeBookShelfList(UserAccount $user, Product $product) {
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getDownloadUrl()
	 */
	public function getDownloadUrl(Product $product, UserAccount $user) {
		$url = explode ( ',', $this->_supplier->getInfo ( 'download_url' ) );
		if ($url === false || count ( $url ) === 0)
			throw new SupplierConnectorException ( 'Invalid download url for supplier: ' . $this->_supplier->getName () );
		$url = $this->_formatURL ($url[0], $product->getAttribute ('cno') );
		return $url;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getOnlineReadUrl()
	 */
	public function getOnlineReadUrl(Product $product, UserAccount $user) {
		$url = explode ( ',', $this->_supplier->getInfo ( 'view_url' ) );
		if ($url === false || count ( $url ) === 0)
			throw new SupplierConnectorException ( 'Invalid view url for supplier: ' . $this->_supplier->getName () );
		$url = $this->_formatURL ($url[0], $product->getAttribute ( 'cno' ) );
		return $url;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::borrowProduct()
	 */
	public function borrowProduct(Product &$product, UserAccount $user) {
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::returnProduct()
	 */
	public function returnProduct(Product &$product, UserAccount $user) {
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getProduct()
	 */
	public function getProduct(Product $product) {
		$pro = SupplierConnectorProduct::getProduct ( $this->_fakeProduct ( $product->getProductType()) );
		return $pro;
	}
}