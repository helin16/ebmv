<?php
class SC_WenHuiPo extends SupplierConnectorAbstract implements SupplierConn 
{
	public static $_cache;
	/**
	 * Getting the formatted url
	 *
	 * @param string $url        	
	 * @param string $methodName        	
	 *
	 * @return string
	 */
	private function _formatURL($url, $productKey) {
		return trim ( str_replace ( '{productKey}', $productKey, $url ) );
	}
	/**
	 * Getting the issue date range
	 *
	 * @return multitype:UDate unknown
	 */
	private function _getValidDateRange() {
		if (! isset ( self::$_cache ['isseRange'] )) {
			$now = new UDate ();
			$start = new UDate ();
			$start->modify ( '-1 month' );
			self::$_cache ['isseRange'] = array (
					'start' => $start,
					'end' => $now 
			);
		}
		return self::$_cache ['isseRange'];
	}
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
			self::$_cache ['libType'] [$typeId] = BaseServiceAbastract::getInstance ( 'LibraryOwnsType' )->get ( $typeId );
		
		return self::$_cache ['libType'] [$typeId];
	}
	/**
	 * Getting the pagination array
	 *
	 * @return array
	 */
	private function _getPaginationArray() {
		$dateRange = $this->_getValidDateRange ();
		$diff = $dateRange ['end']->diff ( $dateRange ['start'] );
		$array = SupplierConnectorProduct::getInitPagination ( null, $diff->days, 1, DaoQuery::DEFAUTL_PAGE_SIZE );
		return $array;
	}
	/**
	 * (non-PHPdoc)
	 * 
	 * @see SupplierConn::getProductListInfo()
	 */
	public function getProductListInfo(ProductType $type = null) {
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, 'Getting product list info:', __FUNCTION__ );
		$array = $this->_getPaginationArray ();
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, '::got array from results:' . print_r ( $array, true ), __FUNCTION__ );
		return $array;
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
		$url = $this->_formatURL ( $url [0], $productKey );
		$html = BmvComScriptCURL::readUrl ( $url );
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, 'Got from(' . $url . '): <textarea>' . $html . '</textarea>', __FUNCTION__ );
			
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
	 * Getting the cover image
	 *
	 * @param string $productKey        	
	 *
	 * @throws SupplierConnectorException
	 * @return string
	 */
	private function _getCoverImage($productKey) {
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, 'Getting coverpage image:', __FUNCTION__ );
		$src = '';
		try {
			if (! ($doc = $this->_getHTML ( $productKey )) instanceof DOMDocument)
				throw new SupplierConnectorException ( 'Can NOT load the HTML for productKey: ' . $productKey );
			$xpath = new DOMXPath ( $doc );
			$books = $xpath->query ( "//dl[@class='imglist']/dd/div/img" );
			if ($books->item ( 0 ) instanceof DOMElement)
				$src = $books->item ( 0 )->getAttribute ( 'src' );
		} catch ( Exception $ex ) {
			if ($this->_debugMode === true) {
				SupplierConnectorAbstract::log ( $this, ' == Got Error: ' . $ex->getMessage (), __FUNCTION__ );
				SupplierConnectorAbstract::log ( $this, '   == trace: ' . $ex->getTraceAsString (), __FUNCTION__ );
			}
			$src = '';
		}
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, ' == found image url: ' . $src, __FUNCTION__ );
		return $src;
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
	private function _fakeProduct(ProductType $type, UDate $date = null, Product $product = null) {
		$readOnlineCopy = 0;
		// check whether the magazine still there from supplier
		$productKey = $product instanceof Product ? $product->getAttribute ( 'cno' ) : $date->format ( 'Ymd' );
		if (($coverImg = $this->_getCoverImage ( $productKey )) !== '')
			$readOnlineCopy = 1;
		
		$xml = new SimpleXMLElement ( '<' . $type->getName () . '/>' );
		$xml->BookName = $product instanceof Product ? $product->getTitle () : $this->_supplier->getName () . ': ' . $date->format ( 'd/F/Y' );
		$xml->Isbn = $product instanceof Product ? $product->getAttribute ( 'isbn' ) : '9786133577794';
		$xml->NO = $product instanceof Product ? $product->getAttribute ( 'cno' ) : $date->format ( 'Ymd' );
		$xml->Author = $product instanceof Product ? $product->getAttribute ( 'author' ) : $this->_supplier->getName ();
		$xml->Press = $product instanceof Product ? $product->getAttribute ( 'publisher' ) : $this->_supplier->getName ();
		$xml->PublicationDate = $product instanceof Product ? $product->getAttribute ( 'publish_date' ) : $date->format ( 'Y-F-d' );
		$xml->Words = '';
		$xml->FrontCover = $coverImg;
		$xml->Introduction = $product instanceof Product ? $product->getAttribute ( 'description' ) : $this->_supplier->getName () . ': ' . $date->format ( 'd F Y' );
		$xml->Cip = '';
		$xml->SiteID = trim ( $this->_lib->getInfo ( 'aus_code' ) );
		$xml->Language = 'zh-tw';
		
		$publishDate = new UDate ( $xml->PublicationDate );
		$xml->BookType = $this->_supplier->getName () . '/' . $publishDate->format ( 'Y' ) . '/' . $publishDate->format ( 'm' );
		$copiesXml = $xml->addChild ( 'Copies' );
		$readOnline = $copiesXml->addChild ( $this->_getLibOwnsType ( LibraryOwnsType::ID_ONLINE_VIEW_COPIES )->getCode () );
		$readOnline->Available = $readOnlineCopy;
		$readOnline->Total = 1;
		$download = $copiesXml->addChild ( $this->_getLibOwnsType ( LibraryOwnsType::ID_DOWNLOAD_COPIES )->getCode () );
		$download->Available = $readOnlineCopy;
		$download->Total = 1;
		return $xml;
	}
	/**
	 * (non-PHPdoc)
	 * 
	 * @see SupplierConn::getProductList()
	 */
	public function getProductList($pageNo = 1, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, ProductType $type = null, $onceOnly = false) {
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, 'Getting product list:', __FUNCTION__ );
		$dateRange = $this->_getValidDateRange ();
		$products = array ();
		$pagination = $this->_getPaginationArray ();
		$start = (($pageNo - 1) * $pageSize);
		for($i = $start; $i <= $pagination ['totalRecords']; $i ++) {
			// if we are only try to grab one lot
			if (($i >= ($start + $pageSize)) && $onceOnly === true)
				break;
			$isseDate = new UDate ( $dateRange ['start']->format ( 'Y-m-d H:i:s' ) );
			$isseDate->modify ( '+' . $i . ' day' );
			$products [] = $this->_fakeProduct ( $type, $isseDate );
		}
		return $products;
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
		$url = $this->_formatURL ( $url [0], $product->getAttribute ( 'cno' ) );
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
		$url = $this->_formatURL ( $url [0], $product->getAttribute ( 'cno' ) );
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
	public function getProduct($isbn, $no) {
		if ($this->_debugMode === true)
			SupplierConnectorAbstract::log ( $this, 'Getting product(ISBN=' . $isbn . ', NO=' . $no . '):', __FUNCTION__ );
		if (! ($product = BaseServiceAbastract::getInstance ( 'Product' )->findProductWithISBNnCno ( $isbn, $no, $this->_supplier )) instanceof Product) {
			if ($this->_debugMode === true)
				SupplierConnectorAbstract::log ( $this, 'Can NOT find any product (ISBN=' . $isbn . ', NO=' . $no . ')', __FUNCTION__ );
			return null;
		}
		$pro = SupplierConnectorProduct::getProduct ( $this->_fakeProduct ( $product->getProductType (), null, $product ) );
		return $pro;
	}
}