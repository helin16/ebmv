<?php
/**
 * This is the product listing page for library admin
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class ListController extends LibAdminPageAbstract
{
	/**
	 * The selected Menu Item name
	 *
	 * @var string
	 */
	public $menuItemCode = 'products';
	/**
	 * (non-PHPdoc)
	 * @see FrontEndPageAbstract::_getEndJs()
	 */
	protected function _getEndJs()
	{
		$pageNumber = 1;
		$pageSize = 10;
		$productId = 0;
		 
		$js = parent::_getEndJs();
		$js .= 'pageJs.setHTMLIDs("item-list")';
		$js .= '.setCallbackId("getItems", "' . $this->getItemsBtn->getUniqueID() . '")';
		$js .= '.getResult(true);';
		return $js;
	}
	public function getItems($sender, $param)
	{
		$result = $errors = $productArray = array();
		try
		{
			$pageNumber = 1;
			$pageSize = DaoQuery::DEFAUTL_PAGE_SIZE;
			if(isset($param->CallbackParameter->pagination))
			{
				$pagination = $param->CallbackParameter->pagination;
				$pageNumber = (isset($pagination->pageNo) && trim($pagination->pageNo) !== '' && is_numeric($pagination->pageNo)) ? trim($pagination->pageNo) : $pageNumber;
				$pageSize = (isset($pagination->pageSize) && trim($pagination->pageSize) !== '' && is_numeric($pagination->pageSize)) ? trim($pagination->pageSize) : $pageSize;
			}
			$productId = (isset($param->CallbackParameter->itemId) && trim($param->CallbackParameter->itemId) !== '' && is_numeric($param->CallbackParameter->itemId)) ? trim($param->CallbackParameter->itemId) : '0';
	
			$stats = array();
			if($productId === '' || $productId === '0')
				$productArray = Product::getAll(false, $pageNumber, $pageSize, array(), $stats);
			else
				$productArray[] = Product::get($productId);
			$result['pagination'] = $stats;
			foreach($productArray as $product)
				$result['items'][] = $product->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage() . $ex->getTraceAsString();
		}
		 
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
}