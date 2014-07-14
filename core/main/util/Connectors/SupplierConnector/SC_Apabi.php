<?php
class SC_Apabi extends SupplierConnectorAbstract implements SupplierConn
{
	private $_products = array(
		'n.D310000dycjrb' => array('name' => '第一财经日报', 'productId' =>  'CN31-0024', 'paperUid' => 'n.D310000dycjrb', 'productType' => 'NewPaper'),
		'n.D310000xmzk'   => array('name' => '新民周刊',    'productId' =>  'CN31-1802/D', 'paperUid' => 'n.D310000xmzk', 'productType' => 'Magazine'),
		'n.D440100nfdsb'  => array('name' => '南方都市报',  'productId' =>  'CN44-0175', 'paperUid' => 'n.D440100nfdsb', 'productType' => 'NewPaper'),
		'n.D440100nfzm'   => array('name' => '南方周末',    'productId' =>  'CN44-0003', 'paperUid' => 'n.D440100nfzm', 'productType' => 'Magazine')
	);
	private $_orgnizationNo = 'tiyan';
	private $_urls = array(
		'getImgs' => 'http://paper.apabi.com/servlet/getPagePicsServlet'
	);
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
	private function _getFakeXml($productType, $productName, $isbn, $cno, $issueDate, $coverImg) {
		$xml = new SimpleXMLElement ( '<' . $productType . '/>' );
		$xml->BookName = $productName;
		$xml->Isbn = $isbn;
		$xml->NO = $cno;
		$xml->Author = '';
		$xml->Press = 'Apabi';
		$xml->PublicationDate = trim($issueDate);
		$xml->Words = '';
		$xml->FrontCover = $coverImg;
		$xml->Introduction = $productName . ': ' . $issueDate;
		$xml->Cip = '';
		$xml->SiteID = trim ( $this->_lib->getInfo ( 'aus_code' ) );
		$xml->Language = 'zh_CN';
	
		$publishDate = new UDate ( $xml->PublicationDate );
		$xml->BookType = ($bookName = trim($this->_supplier->getName())) . '/' . ($bookName . $publishDate->format ('Y')) . '/' . ($bookName . $publishDate->format('m'));
		$copiesXml = $xml->addChild( 'Copies' );
		$readOnline = $copiesXml->addChild ($this->_getLibOwnsType ( LibraryOwnsType::ID_ONLINE_VIEW_COPIES )->getCode ());
		$readOnline->Available = 1;
		$readOnline->Total = 1;
		$download = $copiesXml->addChild ( $this->_getLibOwnsType ( LibraryOwnsType::ID_DOWNLOAD_COPIES )->getCode () );
		$download->Available = 0;
		$download->Total = 1;
		return $xml;
	}
	/**
	 * Gettht product List
	 * 
	 * @throws CoreException
	 * @return SimpleXMLElement
	 */
	public function getProductListInfo(ProductType $type = null)
	{
		$pageNo = 1;
		$pageSize = count($this->_products);
		$totalRecords = count($this->_products);
		$array = $array = array('totalPages' => (intval($pageSize) === 0 ? 0 : ceil($totalRecords / $pageSize)), 'pageNo' => $pageNo, 'pageSize' => $pageSize, 'totalRecords' => $totalRecords);;
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
		$data = array(
			'paperUids' => implode(',', array_keys($this->_products))
			,'picType' => ''
			,'orgNo' => trim($this->_orgnizationNo)
		);
		$xml = BmvComScriptCURL::readUrl($this->_urls['getImgs'], BmvComScriptCURL::CURL_TIMEOUT, $data);
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Trying to get images, got ' . $xml, __FUNCTION__);
		
		$xml = new SimpleXMLElement($xml);
		$array = array();
		foreach($xml->children() as $fakeProductXml)
		{
			$array[] = $this->_getFakeXml($this->_products[$fakeProductXml->paperUid]['productType'], $fakeProductXml->paperName, $fakeProductXml->paperUid, $fakeProductXml->pageUid, $fakeProductXml->issueDate, trim($fakeProductXml));
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
	public function getProduct($isbn, $no)
	{
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Getting Product from supplier:', __FUNCTION__);
		
		$params = array(
				"siteId"  => trim($this->_lib->getInfo('aus_code')),
				'_method' => 'singleCourse',
				'id'      => trim($no)
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
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getDownloadUrl()
	 */
	public function getDownloadUrl(Product $product, UserAccount $user) {
	}
}