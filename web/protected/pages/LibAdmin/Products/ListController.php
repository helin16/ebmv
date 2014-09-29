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
		$js .= 'pageJs.setHTMLIDs("item-total-count", "item-list", "current-order-summary")';
		$js .= '.setCallbackId("getItems", "' . $this->getItemsBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("getOrderSummary", "' . $this->getOrderSummaryBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("orderProduct", "' . $this->orderProductBtn->getUniqueID() . '")';
		$js .= '.getOrderSummary()';
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
				$productArray = Product::getAllByCriteria('productTypeId = ?', array(ProductType::ID_BOOK), false, $pageNumber, $pageSize, array(), $stats);
			else
				$productArray[] = Product::get($productId);
			$result['pagination'] = $stats;
			foreach($productArray as $product)
			{
				$array =  $product->getJson();
				$totalOrderedQty = 0;
				foreach(LibraryOwns::getAllByCriteria('libraryId = ? and productId = ? and typeId = ?', array(Core::getLibrary()->getId(), $product->getId(), LibraryOwnsType::ID_ONLINE_VIEW_COPIES)) as $libOwn)
				{
					$totalOrderedQty += $libOwn->getTotal();
				}
				$array['orderedQty'] = $totalOrderedQty; 
				$result['items'][] = $array;
			}
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		 
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	
	public function getOrderSummary($sender, $param)
	{
		$result = $errors = array();
		try
		{
			$order = Order::getOpenOrder(Core::getLibrary());
			if(!$order instanceof Order)
				$order = Order::create(Core::getLibrary());
			$result['order'] = $order->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		 
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	public function orderProduct($sender, $param)
	{
		$result = $errors = array();
		try
		{
			if(!isset($param->CallbackParameter->orderId) || !($order = Order::get($param->CallbackParameter->orderId)) instanceof Order)
				throw new Exception('Invalid order id passed in!');
			if(!isset($param->CallbackParameter->productId) || !($product = Product::get($param->CallbackParameter->productId)) instanceof Product)
				throw new Exception('Invalid product id passed in!');
			if(!isset($param->CallbackParameter->qty) || !is_numeric($qty = trim($param->CallbackParameter->qty)))
				throw new Exception('Invalid qty passed in!');
			
			$result['item'] = OrderItem::create($order, $product, $qty)->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		 
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
}