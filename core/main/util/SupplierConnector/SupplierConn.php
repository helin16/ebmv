<?php
interface SupplierConn
{
	/**
	 * Gettht product List
	 *
	 * @throws CoreException
	 * @return SimpleXMLElement
	 */
	public function getProductListInfo(){}
	/**
	 * Getting xml product list
	 *
	 * @param number $pageNo   The page no
	 * @param number $pageSize the page size
	 *
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function getProductList($pageNo = 1, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE){}
	/**
	 * importing the products from the supplier
	 *
	 * @param string $productList The list of product from supplier
	 * @param int    $index       Which product of the file to import
	 *
	 * @throws CoreException
	 * @return array
	 */
	public function importProducts($productList, $index = null) {}
	/**
	 * Getting the book shelf
	 *
	 * @param UserAccount $user The current user
	 * @param Library     $lib  Which library the user has been assigned to
	 *
	 * @return Mixed The ProductShelfItem array
	 */
	public function getBookShelfList(UserAccount $user, Library $lib) {}
	/**
	 * Synchronize user's bookshelf from supplier to local
	 *
	 * @param UserAccount $user       The library current user
	 * @param array       $shelfItems The
	 *
	 * @return SupplierConnector
	 */
	public function syncUserBookShelf(UserAccount $user, array $shelfItems) {}
	/**
	 * Adding a product to the user's bookshelf
	 *
	 * @param UserAccount $user    The library current user
	 * @param Product     $product The product to be added
	 * @param Library     $lib     Which library we are in now
	 *
	 * @throws CoreException
	 * @return Ambigous <NULL, SimpleXMLElement>
	 */
	public function addToBookShelfList(UserAccount $user, Product $product, Library $lib) {}
	/**
	 * Removing a product from the book shelf
	 *
	 * @param UserAccount $user    The library current user
	 * @param Product     $product The product to be removed
	 * @param Library     $lib     Which library we are in now
	 *
	 * @throws CoreException
	 * @return mixed
	 */
	public function removeBookShelfList(UserAccount $user, Product $product, Library $lib) {}
	/**
	 * Getting the download url for a book
	 *
	 * @param Product     $product The product we are trying to get the url for
	 * @param UserAccount $user    Who wants to download it
	 *
	 * @throws Exception
	 * @return string
	 */
	public function getDownloadUrl(Product $product, UserAccount $user) {}
}