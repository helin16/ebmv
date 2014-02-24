<?php
class SC_TaKungPao extends SupplierConnectorAbstract implements SupplierConn
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
	private function _formatURL($url, $productKey)
	{
		return trim(str_replace('{productKey}', $productKey, $url));
	}
	/**
	 * Getting the issue date range
	 * 
	 * @return multitype:UDate unknown
	 */
	private function _getValidDateRange()
	{
		if(!isset(self::$_cache['isseRange']))
		{
			$now = new UDate();
			$start = new UDate();
			$start->modify('-6 month');
			self::$_cache['isseRange'] = array('start'=> $start, 'end' => $now);
		}
		return self::$_cache['isseRange'];
	}
	/**
	 * Getting the library owns type
	 * 
	 * @param unknown $typeId
	 * 
	 * @return LibraryOwnsType
	 */
	private function _getLibOwnsType($typeId)
	{
		if(!isset(self::$_cache['libType']))
			self::$_cache['libType'] = array();
		
		if(!isset(self::$_cache['libType'][$typeId]))
			self::$_cache['libType'][$typeId] = BaseServiceAbastract::getInstance('LibraryOwnsType')->get($typeId);
		
		return self::$_cache['libType'][$typeId];
	}
	/**
	 * Getting the pagination array
	 * 
	 * @return array
	 */
	private function _getPaginationArray()
	{
		$dateRange = $this->_getValidDateRange();
		$diff = $dateRange['end']->diff($dateRange['start']);
		$array = SupplierConnectorProduct::getInitPagination(
				null, 
				$diff->days,
				1,
				DaoQuery::DEFAUTL_PAGE_SIZE
		);
		return $array;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getProductListInfo()
	 */
	public function getProductListInfo(ProductType $type = null)
	{
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Getting product list info:', __FUNCTION__);
		$array = $this->_getPaginationArray();
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, '::got array from results:' . print_r($array, true) , __FUNCTION__);
		return $array;
	}
	private function _getCoverImage()
	{
		return '';
	}
	/**
	 * Getting a fake xml element for product
	 * 
	 * @param ProductType $type The type of these magazines
	 * @param UDate       $date The issue date
	 * 
	 * @return SimpleXMLElement
	 */
	private function _fakeProduct(ProductType $type, UDate $date = null, Product $product = null)
	{
		$xml = new SimpleXMLElement('<' . $type->getName() . '/>');
		$xml->BookName = $product instanceof Product ? $product->getTitle() : 'TaKungPao Issue: ' . $date->format('d/F/Y');
		$xml->Isbn = $product instanceof Product ? $product->getAttribute('isbn') : '9786133577794';
		$xml->NO = $product instanceof Product ? $product->getAttribute('cno') : $date->format('Ymd');
		$xml->Author = $product instanceof Product ? $product->getAttribute('author') : '大公報';
		$xml->Press = $product instanceof Product ? $product->getAttribute('publisher') : '大公報';
		$xml->PublicationDate = $product instanceof Product ? $product->getAttribute('publish_date') : $date->format('Y-F-d');
		$xml->Words = '';
		$xml->FrontCover = $product instanceof Product ? $product->getAttribute('image_thumb') : $this->_getCoverImage();
		$xml->Introduction = $product instanceof Product ? $product->getAttribute('description') : '大公報: ' . $date->format('Y年m月d日');
		$xml->Cip = '';
		$xml->SiteID = trim($this->_lib->getInfo('aus_code'));
		$xml->Language = 'zh-tw';
		
		$publishDate = new UDate($xml->PublicationDate);
		$xml->BookType = '大公報/' . $publishDate->format('Y') . '/' .$publishDate->format('m');
		$copiesXml = $xml->addChild('Copies');
		$readOnline = $copiesXml->addChild($this->_getLibOwnsType(LibraryOwnsType::ID_ONLINE_VIEW_COPIES)->getCode());
		$readOnline->Available = 1;
		$readOnline->Total = 1;
		return $xml;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getProductList()
	 */
	public function getProductList($pageNo = 1, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, ProductType $type = null, $onceOnly = false)
	{
		if($this->_debugMode === true) SupplierConnectorAbstract::log($this, 'Getting product list:', __FUNCTION__);
		$dateRange = $this->_getValidDateRange();
		$products = array();
		$pagination = $this->_getPaginationArray();
		$start = (($pageNo - 1) * $pageSize);
		for($i = $start; $i <= $pagination['totalRecords']; $i++)
		{
			//if we are only try to grab one lot
			if(($i >= ($start + $pageSize)) && $onceOnly === true)
				break;
			$isseDate = new UDate($dateRange['start']->format('Y-m-d H:i:s'));
			$isseDate->modify('+' . $i . ' day');
			$products[] = $this->_fakeProduct($type, $isseDate);
		}
		return $products;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getBookShelfList()
	 */
	public function getBookShelfList(UserAccount $user){}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::syncUserBookShelf()
	 */
	public function syncUserBookShelf(UserAccount $user, array $shelfItems){}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::addToBookShelfList()
	 */
	public function addToBookShelfList(UserAccount $user, Product $product){}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::removeBookShelfList()
	 */
	public function removeBookShelfList(UserAccount $user, Product $product){}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getDownloadUrl()
	 */
	public function getDownloadUrl(Product $product, UserAccount $user) {}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getOnlineReadUrl()
	 */
	public function getOnlineReadUrl(Product $product, UserAccount $user)
	{
		$url = explode(',', $this->_supplier->getInfo('view_url'));
		if($url === false || count($url) === 0)
			throw new SupplierConnectorException('Invalid view url for supplier: ' . $this->_supplier->getName());
		$url = $this->_formatURL($url[0], $product->getAttribute('cno'));
		return $url;
	}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConnectorAbstract::updateProduct()
	 */
	public function updateProduct(Product &$product) {}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::borrowProduct()
	 */
	public function borrowProduct(Product &$product, UserAccount $user) {}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::returnProduct()
	 */
	public function returnProduct(Product &$product, UserAccount $user) {}
	/**
	 * (non-PHPdoc)
	 * @see SupplierConn::getProduct()
	 */
	public function getProduct($isbn, $no)
	{
		if(!($product = BaseServiceAbastract::getInstance('Product')->findProductWithISBNnCno($isbn, $no, $this->_supplier)) instanceof Product)
			return null;
		return SupplierConnectorProduct::getProduct($this->_fakeProduct($product->getProductType(), null, $product));
	}
}