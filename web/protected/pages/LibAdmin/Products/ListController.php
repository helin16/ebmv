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
		 
		$cates  = array();
		foreach(Category::getAll() as $cate)
		{
			if($cate->getNoOfProducts(ProductType::get(ProductType::ID_BOOK)) > 0)
				$cates[] = $cate->getJson();
		}
		$js = parent::_getEndJs();
		$js .= 'pageJs.setHTMLIDs("item-total-count", "item-list", "current-order-summary", "order-btn", "my-cart")';
		$js .= '.setCallbackId("getItems", "' . $this->getItemsBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("getOrderSummary", "' . $this->getOrderSummaryBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("orderProduct", "' . $this->orderProductBtn->getUniqueID() . '")';
		$js .= '.setLanguages("lang-sel", ' . json_encode(array_map(create_function('$a', 'return $a->getJson();'), Language::getAll())) . ')';
		$js .= '.setCategories("cate-sel", ' . json_encode($cates) . ')';
		$js .= '.bindChosen()';
		$js .= '.getOrderSummary()';
		$js .= '.getResult(true);';
		return $js;
	}
	/**
	 * Getting items
	 * 
	 * @param unknown $sender
	 * @param unknown $param
	 * 
	 * 
	 */
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
	
			$searchCriteria = json_decode(json_encode($param->CallbackParameter->searchCriteria), true);
			var_dump($searchCriteria);
			$searchTxt = trim(isset($searchCriteria['searchTxt']) ? $searchCriteria['searchTxt'] : '');
			$categoryIds = (isset($searchCriteria['categoryIds']) && is_array($searchCriteria['categoryIds']) ? $searchCriteria['categoryIds'] : array());
			$language = (isset($searchCriteria['languageId']) && ($language = Language::get($searchCriteria['languageId'])) ? $language : null);
			
			$stats = array();
			Dao::$debug = true;
			$productArray = Product::findProductsInCategory(null, $searchTxt, $categoryIds, '', $language, ProductType::get(ProductType::ID_BOOK), true, $pageNumber, $pageSize, array('pro.id' => 'desc'), $stats);
			Dao::$debug = false;
			
			$result['pagination'] = $stats;
			$result['items'] = array();
			foreach($productArray as $product)
			{
				$array =  $product->getJson();
				$totalOrderedQty = 0;
				$orderedLibs = array();
				foreach(LibraryOwns::getAllByCriteria('productId = ? and typeId = ?', array($product->getId(), LibraryOwnsType::ID_ONLINE_VIEW_COPIES)) as $libOwn)
				{
					if($libOwn->getLibrary()->getId() === Core::getLibrary()->getId())
						$totalOrderedQty += $libOwn->getTotal();
					else
						$orderedLibs[] = $libOwn->getLibrary()->getJson();
				}
				$array['orderedQty'] = $totalOrderedQty;
				$array['orderedLibs'] = $orderedLibs;
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
			Dao::beginTransaction();
			$order = Order::getOpenOrder(Core::getLibrary());
			if(!$order instanceof Order)
				$order = Order::create(Core::getLibrary());
			$result['order'] = $order->getJson();
			Dao::commitTransaction();
		}
		catch(Exception $ex)
		{
			Dao::rollbackTransaction();
			$errors[] = $ex->getMessage(). $ex->getTraceAsString();
		}
		 
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	public function orderProduct($sender, $param)
	{
		$result = $errors = array();
		try
		{
			Dao::beginTransaction();
			if(!isset($param->CallbackParameter->orderId) || !($order = Order::get($param->CallbackParameter->orderId)) instanceof Order)
				throw new Exception('Invalid order id passed in!');
			if(!isset($param->CallbackParameter->productId) || !($product = Product::get($param->CallbackParameter->productId)) instanceof Product)
				throw new Exception('Invalid product id passed in!');
			if(!isset($param->CallbackParameter->qty) || !is_numeric($qty = trim($param->CallbackParameter->qty)))
				throw new Exception('Invalid qty passed in!');
			
			$price = explode(',', $product->getAttribute('price', ','));
			$price = (count($price) === 0 ? '0.0000' : trim($price[0]));
			OrderItem::create($order, $product, $qty, false, $price, (($price * 1) * ($qty * 1)));
			$result['order'] = Order::get($order->getId())->getJson();
			Dao::commitTransaction();
		}
		catch(Exception $ex)
		{
			Dao::rollbackTransaction();
			$errors[] = $ex->getMessage(). $ex->getTraceAsString();
		}
		 
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
}