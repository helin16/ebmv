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
	private function _getJsonFromUrl($url, ProductType $type, $pageNo = 1)
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
			foreach($result['data'] as $row)
			{
				self::$cache['data'][] = $this->_fakeProduct($type, $row);
			}
		}
		if(count(self::$cache['data']) < intval($result['total']) )
			$this->_getJsonFromUrl($url, $type, $pageNo + 1);
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
// 		$importUrl = 'http://public.dooland.com/v1/Magazine/lists/page/{page_no}';
		$importUrl = str_replace('{page_no}', 1, trim($this->_supplier->getInfo('import_url')));

		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::got import url:' . $importUrl, __FUNCTION__);
		if(!isset(self::$cache['data']))
			$this->_getJsonFromUrl($importUrl, $type);

		if(count(self::$cache['data']) === 0)
			throw new SupplierConnectorException('Can NOT any data information from ' . $importUrl . '!');
		$array = SupplierConnectorProduct::getInitPagination ( null, count(self::$cache['data']), 1, DaoQuery::DEFAUTL_PAGE_SIZE );
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
		if(!isset(self::$cache['data']))
		{
			$importUrl = str_replace('{page_no}', 1, trim($this->_supplier->getInfo('import_url')));
			$this->_getJsonFromUrl($importUrl, $type);
		}
		return array_slice(self::$cache['data'], ($pageNo - 1) * $pageSize, $pageSize);
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getOnlineReadUrl()
	 */
	public function getOnlineReadUrl(Product $product, UserAccount $user) {
// 		$url = explode (',', $this->_supplier->getInfo ( 'view_url' ) );
// 		$url = trim('http://public.dooland.com/v1/Read/webonline/?content=' . $content);
		$url = explode (',', $this->_supplier->getInfo ( 'view_url' ) );
		$content = $this->_getContent($product);
		$url = str_replace('{content}', $content, $url);
		$url = str_replace('{appid}', self::APP_ID, $url);
		if ($url === false || count ( $url ) === 0)
			throw new SupplierConnectorException ( 'Invalid view url for supplier: ' . $this->_supplier->getName () );
		return $url;
	}
	/**
	 * Getting the encrypted content string
	 *
	 * @param Product $product
	 *
	 * @return string
	 */
	private function _getContent(Product $product)
	{
		$now = new UDate('now', 'Asia/Shanghai');
		$contentArr = array(
			'appid' => self::APP_ID,
			'id' => $product->getAttribute ( 'isbn' ),
			'issuetype' => 'mag',
			'pageid' => '1',
			'accountid' => '111',
			'timestamp' => $now->getUnixTimeStamp()
		);
		return urlencode($this->_encrypt(json_encode($contentArr), substr(self::APP_SECRET, 0, 8)));
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see SupplierConn::getProduct()
	 */
	public function getProduct(Product $product) {
		$pro = SupplierConnectorProduct::getProduct ( $this->_fakeProduct ( $product->getProductType(), null, $product ) );
		return $pro;
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
		$xml->Isbn = $product instanceof Product ? $product->getAttribute ( 'isbn' ) : $data['magazineId'];
		$xml->NO = $product instanceof Product ? $product->getAttribute ( 'cno' ) : $data['pid'];
		$xml->Author = $product instanceof Product ? $product->getAttribute ( 'author' ) : $this->_supplier->getName ();
		$xml->Press = $product instanceof Product ? $product->getAttribute ( 'publisher' ) : $this->_supplier->getName ();
		$xml->PublicationDate = $product instanceof Product ? $product->getAttribute ( 'publish_date' ) : $data['pubDate'];
		$xml->Words = '';
		$xml->FrontCover = $product instanceof Product ? $product->getAttribute ( 'image_thumb' ) : $data['thumbnail'];
		$xml->Introduction = $product instanceof Product ? $product->getAttribute ( 'description' ) : $data['des'];
		$xml->Cip = '';
		$xml->SiteID = trim ( $this->_lib->getInfo ( 'aus_code' ) );
		$xml->Language = 'zh-CN';

		$publishDate = new UDate ( $xml->PublicationDate );
		$xml->BookType = ($bookName = trim($xml->BookName)) . '/' . ($bookName . $publishDate->format ('Y')) . '/' . ($bookName . $publishDate->format('m'));
		$copiesXml = $xml->addChild( 'Copies' );
		$readOnline = $copiesXml->addChild ($this->_getLibOwnsType ( LibraryOwnsType::ID_ONLINE_VIEW_COPIES )->getCode ());
		$readOnline->Available = 1;
		$readOnline->Total = 1;
		$download = $copiesXml->addChild ( $this->_getLibOwnsType ( LibraryOwnsType::ID_DOWNLOAD_COPIES )->getCode () );
		$download->Available = $readOnlineCopy;
		$download->Total = 1;
		return $xml;
	}
	/**
	 * Getting the library owns type
	 *
	 * @param unknown $typeId
	 *
	 * @return LibraryOwnsType
	 */
	private function _getLibOwnsType($typeId) {
		if (! isset ( self::$cache ['libType'] ))
			self::$cache ['libType'] = array ();

		if (! isset ( self::$cache ['libType'] [$typeId] ))
			self::$cache ['libType'] [$typeId] = LibraryOwnsType::get ( $typeId );

		return self::$cache ['libType'] [$typeId];
	}

	private function _encrypt($input, $key) {
		$size = mcrypt_get_block_size ( 'des', 'ecb' );
		$pad = $size - (strlen ( $input ) % $size);
		$input = $input . str_repeat ( chr ( $pad ), $pad );
		$td = mcrypt_module_open ( 'des', '', 'ecb', '' );
		$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		@mcrypt_generic_init ( $td, $key, $iv );
		$data = mcrypt_generic ( $td, $input );
		mcrypt_generic_deinit ( $td );
		mcrypt_module_close ( $td );
		$data = base64_encode ( $data );
		return $data;
	}

}