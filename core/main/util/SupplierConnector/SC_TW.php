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
	 * construtor
	 * 
	 * @param Supplier $supplier The supplier
	 */
	public function __construct(Supplier $supplier)
	{
		parent::__construct($supplier);
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
		$this->_importUrl = ($urls === false ? null : $urls[0]);
		return $this->_importUrl;
	}
	/**
	 * Gettht product List
	 * 
	 * @throws CoreException
	 * @return SimpleXMLElement
	 */
	public function getProductListInfo()
	{
		$xml = $this->_getXmlFromUrl($this->_importUrl, 1, 1);
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
	public function getProductList($pageNo = 1, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE)
	{
		$array = array();
		$xml = $this->_getXmlFromUrl($this->_importUrl, $pageNo, $pageSize);
		foreach($xml->children() as $childXml)
		{
			$array[] = $childXml;
		}
		return $array;
	}
	private function _getXmlFromUrl($url, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $format = 'xml')
	{
		$params = array('format' => $format, 'size' => $pageSize, 'index' => $pageNo);
		$result = $this->readUrl($url . '?' . http_build_query($params), 120000);
		return new SimpleXMLElement($result);
	}
	/**
	 * Getting the book shelf
	 * 
	 * @param UserAccount $user
	 * @param Library     $lib
	 * 
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function getBookShelfList(UserAccount $user, Library $lib)
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
	 * @param Library     $lib
	 * 
	 * @throws CoreException
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function addToBookShelfList(UserAccount $user, Product $product, Library $lib)
	{
	}
	/**
	 * Removing a product from the book shelf
	 * 
	 * @param UserAccount $user
	 * @param Product     $product
	 * @param Library     $lib
	 * 
	 * @throws CoreException
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function removeBookShelfList(UserAccount $user, Product $product, Library $lib)
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
}