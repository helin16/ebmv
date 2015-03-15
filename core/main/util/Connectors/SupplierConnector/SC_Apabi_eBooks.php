<?php
class SC_Apabi_eBooks extends SupplierConnectorAbstract implements SupplierConn
{
// 	private $_products = array(
// 		'n.D310000dycjrb' => array('name' => '第一财经日报', 'productId' =>  'CN31-0024', 'paperUid' => 'n.D310000dycjrb', 'productType' => 'NewsPaper'),
// 		'n.D310000xmzk'   => array('name' => '新民周刊',    'productId' =>  'CN31-1802/D', 'paperUid' => 'n.D310000xmzk', 'productType' => 'NewsPaper'),
// 		'n.D440100nfdsb'  => array('name' => '南方都市报',  'productId' =>  'CN44-0175', 'paperUid' => 'n.D440100nfdsb', 'productType' => 'NewsPaper'),
// 		'n.D440100nfzm'   => array('name' => '南方周末',    'productId' =>  'CN44-0003', 'paperUid' => 'n.D440100nfzm', 'productType' => 'NewsPaper')
// 	);
	private $_orgnizationNo = 'bmv';
	private $_orgnizationKey = 'apabikey';
	private $_supplierUserName ='auebmv';
	private $_supplierPassword = '111111';
	private static $_cache = array();
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
	 * Getting a fake xml element for product
	 *
	 * @param ProductType $type
	 *        	The type of these magazines
	 * @param UDate $date
	 *        	The issue date
	 *
	 * @return SimpleXMLElement
	 */
	private function _getFakeXml(ProductType $productType, $productName, $isbn, $cno, $issueDate, $coverImg, $author, $publisher, $intro, $category, $siteId) {
		$xml = new SimpleXMLElement ( '<' . $productType->getName() . '/>' );
		$xml->BookName = $productName;
		$xml->Isbn = $isbn;
		$xml->NO = $cno;
		$xml->Author = $author;
		$xml->Press = $publisher;
		$xml->PublicationDate = trim($issueDate);
		$xml->Words = '';
		$xml->FrontCover = $coverImg;
		$xml->Introduction = $intro;
		$xml->Cip = '';
		$xml->SiteID = $siteId;
		$xml->Language = 'zh_CN';
		$xml->BookType = $category;
	
		$copiesXml = $xml->addChild( 'Copies' );
		$readOnline = $copiesXml->addChild ($this->_getLibOwnsType ( LibraryOwnsType::ID_ONLINE_VIEW_COPIES )->getCode ());
		$readOnline->Available = 1;
		$readOnline->Total = 1;
		$download = $copiesXml->addChild ( $this->_getLibOwnsType ( LibraryOwnsType::ID_DOWNLOAD_COPIES )->getCode () );
		$download->Available = 1;
		$download->Total = 1;
		$download = $copiesXml->addChild ( $this->_getLibOwnsType ( LibraryOwnsType::ID_BORROW_TIMES )->getCode () );
		$download->Available = 1;
		$download->Total = 1;
		return $xml;
	}
	private function _getListData($pageNo, $pageSize = DaoQuery::DEFAULT_JOIN_TYPE)
	{
		$now = new UDate();
		$thisYear = $now->format('Y');
		$now->modify('-1 year');
		$beforeYear = $now->format('Y');
		$url = 'http://www.apabi.com/bmv/mobile.mvc';
		$data = array(
				'api' => 'metadatasearch',
				'type' => '0',
				'key' => '',
				'order' => '1',
				'ordertype' => '0',
				'page' => $pageNo,
				'pagesize' => $pageSize,
				'digitresgroupid' => 486
		);
		$xml = BmvComScriptCURL::readUrl($url . '?' . http_build_query($data), BmvComScriptCURL::CURL_TIMEOUT);
		return new SimpleXMLElement($xml);
	}
	/**
	 * Gettht product List
	 * 
	 * @throws CoreException
	 * @return SimpleXMLElement
	 */
	public function getProductListInfo(ProductType $type = null)
	{
		$xml = $this->_getListData(1, 1);
		$totalRecords = $xml->TotalCount;
		$pageNo = 1;
		$pageSize = 100;
		$array = array('totalPages' => (intval($pageSize) === 0 ? 0 : ceil($totalRecords / $pageSize)), 'pageNo' => $pageNo, 'pageSize' => $pageSize, 'totalRecords' => $totalRecords);
		return $array;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getProductList()
	 */
	public function getProductList($pageNo = 1, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, ProductType $type = null, $onceOnly = false)
	{
		$xml = $this->_getListData($pageNo, $pageSize);
		$array = array();
		foreach($xml->Records->children() as $fakeProductXml)
		{
			$array[] = $this->_getFakeProduct($fakeProductXml, trim($this->_lib->getInfo ( 'aus_code' ) ), '_getXmlElementFromFormat2' );
		}
		return $array;
	}
	/**
	 * Generating a fake product
	 * 
	 * @param SimpleXMLElement $fakeProductXml
	 * @param string           $siteId
	 * 
	 * @return SimpleXMLElement
	 */
	private function _getFakeProduct(SimpleXMLElement $fakeProductXml, $siteId = '', $funcName = '')
	{
		$productType = ProductType::get(ProductType::ID_BOOK);
		$cno = $this->$funcName($fakeProductXml, 'Identifier');
		$productName = $this->$funcName($fakeProductXml, 'Title');
		// ignore AlternativeTitle
		$author = $this->$funcName($fakeProductXml, 'Creator');
		$publisher = $this->$funcName($fakeProductXml, 'Publisher');
		// ignore Keywords
		$intro = $this->$funcName($fakeProductXml, 'Abstract');
		$issueDate = $this->$funcName($fakeProductXml, 'PublishDate');
		// ignore Score
		// ignore BookStatus
		$isbn = $this->$funcName($fakeProductXml, 'ISBN');
		// ignore Fulltext
		// ignore Catalog
		// ignore Path
		$category = $this->$funcName($fakeProductXml, 'ZTFCategory');
		if(count($tokens = explode(',', $category)) > 1) {
			if(is_numeric(trim($tokens[0]))) {
				$category = trim($tokens[1]);
			} else {
				$category = implode(',', $tokens);
			}
		}
		$category = str_replace('|', '/', $category);
		// ignore TIYAN
		// ignore ContentUrl
		$coverImg = $this->$funcName($fakeProductXml, 'CoverUrl');
			
		$array = $this->_getFakeXml($productType, $productName, $isbn, $cno, $issueDate, $coverImg, $author, $publisher, $intro, $category, $siteId);
		return $array;
	}	
	private function _getXmlElementFromFormat1(SimpleXMLElement $xml, $filedName)
	{
		if(empty($filedName))
			throw new Exception('XML fieldname cannot be empty');
		$result = $xml->xpath("Field[@name='" . $filedName . "']");
		return trim(count($result) > 0 ? $result[0] : '');
	}
	private function _getXmlElementFromFormat2(SimpleXMLElement $xml, $filedName)
	{
		if(empty($filedName))
			throw new Exception('XML fieldname cannot be empty');
		return isset($xml->$filedName) ? trim($xml->$filedName) : '';
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
		$tokenData = array(
			'api' => 'signin',
			'uid' => trim($this->_orgnizationNo),
			'pwd' => base64_encode($this->_supplierPassword)
		);
		$tokenXml = new SimpleXMLElement(BmvComScriptCURL::readUrl($readurl . '?' . http_build_query($tokenData), BmvComScriptCURL::CURL_TIMEOUT));
		if(!isset($tokenXml->token) || trim($tokenXml->token) === '')
			throw new SupplierConnectorException('Invalid token!' . $tokenXml->asXML());
		$data = array(
			'api' => 'onlineread',
			'metaid' => $product->getAttribute('cno'),
			'objid' => $product->getAttribute('cno') . '.ft.CEBX.1',
			'token' => trim($tokenXml->token),
			'cult' => 'CN'
		);
		return $readurl . '?' . http_build_query($data);
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getProduct()
	 */
	public function getProduct(Product $product)
	{
		$productXML = $this->_getFakeXml($product->getProductType(), 
				$product->getTitle(), 
				$product->getAttribute(ProductAttributeType::get(ProductAttributeType::ID_ISBN)->getCode()), 
				$product->getAttribute(ProductAttributeType::get(ProductAttributeType::ID_CNO)->getCode()), 
				$product->getAttribute(ProductAttributeType::get(ProductAttributeType::ID_PUBLISHDATE)->getCode()), 
				$product->getAttribute(ProductAttributeType::get(ProductAttributeType::ID_IMAGE_THUMB)->getCode()), 
				$product->getAttribute(ProductAttributeType::get(ProductAttributeType::ID_AUTHOR)->getCode()), 
				$product->getAttribute(ProductAttributeType::get(ProductAttributeType::ID_PUBLISHER)->getCode()), 
				$product->getAttribute(ProductAttributeType::get(ProductAttributeType::ID_DESCRIPTION)->getCode()), 
				$product->getProductType()->getName(),
				trim($this->_lib->getInfo ( 'aus_code' ) ),
				'_getXmlElementFromFormat2'
				);
		return SupplierConnectorProduct::getProduct($productXML);
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
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getDownloadUrl()
	 */
	public function getDownloadUrl(Product $product, UserAccount $user) {
		$downloadUrl = trim($this->_supplier->getInfo('download_url'));
		if($downloadUrl === false || count($downloadUrl) === 0)
			throw new SupplierConnectorException('Invalid download url for supplier: ' . $this->_supplier->getName());
		$now = new UDate();
		$data = array(
				'metaid' => ($metaId = $product->getAttribute('cno')),
				'objid' => ($objId = ''),
				'usercode' => ($userCode = trim($this->_orgnizationNo)),
				'devicetype' => ($deviceType = '2'),
				'type' => 'borrow',
				'orgcode' => ($orgCode = trim($this->_orgnizationNo)),
				//md5(metaid+objected+orgcode+devicetype+usercode+ DateTime.Now.Date.ToString("yyyyMMdd") +秘钥)
				'string' => ($metaId . $objId . $orgCode . $deviceType . $userCode . $now->format('Ymd') . trim($this->_orgnizationKey)),
				'sign' => strtoupper(md5($metaId . $objId . $orgCode . $deviceType . $userCode . $now->format('Ymd') . trim($this->_orgnizationKey))),
				'cult' => 'CN'
		);
		return $downloadUrl . '?' . http_build_query($data);
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::downloadCatalog()
	 */
	public function downloadCatalog(ProductType $type, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE) 
	{
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Getting NOW TIME from supplier:', __FUNCTION__);
		$this->_importCatalogList($type, 1, $pageSize);
		return $this;
	}
	private function _getCatalogListData($pageNo, $pageSize = DaoQuery::DEFAULT_JOIN_TYPE)
	{
		$now = new UDate();
		$thisYear = $now->format('Y');
		$now->modify('-1 year');
		$beforeYear = $now->format('Y');
		$url = 'http://www.apabi.com/tiyan';
		$data = array(
				'pid' => 'search.api',
				'wd' => '',
				'cult' => 'CN',
				'db' => 'dlib',
				'dt' => 'EBook',
				'pg' => $pageNo,
				'dc' => intval($pageSize * 1 / 10),
				'ps' => 10,
				'filter' => 'FileStatus:[' . $beforeYear . ',' . $thisYear . ']'
		);
		$xml = BmvComScriptCURL::readUrl($url . '?' . http_build_query($data), BmvComScriptCURL::CURL_TIMEOUT);
	
		return new SimpleXMLElement($xml);
	}
	/**
	 * importing the product based on the last updated date
	 *
	 * @param unknown $lastUpdateDate
	 * @param number $index
	 * @param unknown $pageSize
	 */
	private function _importCatalogList(ProductType $type, $index = 1, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $totalPages = 0)
	{
		$bookList = $this->_getCatalogListData($index, $pageSize);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'GOT response from supplier:', __FUNCTION__);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, $bookList instanceof SimpleXMLElement ? $bookList->asXML() : print_r($bookList, true), __FUNCTION__);
		
		if($bookList instanceof SimpleXMLElement) {
			//processing the current list
			if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Start looping through ' . count($bookList->Records->children()) . ' product(s):', __FUNCTION__);
			foreach($bookList->Records->children() as $bookXml) {
				try {
					Dao::beginTransaction();
					$this->_importProduct(SupplierConnectorProduct::getProduct($this->_getFakeProduct($bookXml, '', '_getXmlElementFromFormat1') ) );
					Dao::commitTransaction();
				} catch (Exception $e) {
					Dao::rollbackTransaction();
					if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'ERROR when processing:' . $e->getMessage(), __FUNCTION__);
					if($this->_debugMode === true) SupplierConnectorAbstract::log($this, print_r($bookXml, true), __FUNCTION__);
					if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Trace: ', __FUNCTION__);
					if($this->_debugMode === true) SupplierConnectorAbstract::log($this, $e->getTraceAsString(), __FUNCTION__);
				}
			}
			if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Finished looping through' . count($bookList->children()) . ' product(s).', __FUNCTION__);
			if($totalPages === 0)
				$totalPages = intval(trim($bookList->TotalCount));
		}
		
		//check whether we need to download more
		if($index < $totalPages) {
			if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Got more products to download: current page=' . $index . ', total pages=' . $totalPages, __FUNCTION__);
			$this->_importCatalogList($type, $index + 1, $pageSize, $totalPages);
		}
	}
}
