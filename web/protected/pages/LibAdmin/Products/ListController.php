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
		 
		$cates  = array('' => array());
		foreach(Language::getAll() as $lang)
			$cates[$lang->getId()] = array();
		$type = ProductType::get(ProductType::ID_BOOK);
		foreach(Category::getCategories($type) as $cate)
		{
			$cateArray = array('id' => $cate->getId(), 'name' => $cate->getName());
			if(count($langIds = $cate->getLangIds($type)) === 0)
			{
				$cates[''][] = $cateArray;
					continue;
			}
			foreach($langIds as $langId)
				$cates[$langId][] = $cateArray;
		}
		foreach($cates as $langId => $cateArray){
			$cates[$langId]  = array_unique($cateArray);
		}
		$js = parent::_getEndJs();
		$js .= 'pageJs.setHTMLIDs("item-total-count", "item-list", "current-order-summary", "order-btn", "my-cart")';
		$js .= '.setCallbackId("getItems", "' . $this->getItemsBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("getOrderSummary", "' . $this->getOrderSummaryBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("orderProduct", "' . $this->orderProductBtn->getUniqueID() . '")';
		$js .= '.setLanguages("lang-sel", ' . json_encode(array_map(create_function('$a', 'return $a->getJson();'), Language::getAll())) . ', "cate-sel")';
		$js .= '.setCategories("cate-sel", ' . json_encode($cates) . ')';
		$js .= '.setSalesMargin(' . $this->_getSaleMargin() . ')';
		$js .= '.bindChosen()';
		$js .= '.getOrderSummary()';
		$js .= '.getResult(true);';
		return $js;
	}
	/**
	 * Getting sales margin
	 * @return Ambigous <number, string>
	 */
	private function _getSaleMargin()
	{
		$margin = trim(Core::getLibrary()->getInfo('sales_margin'));
		if($margin === '')
			$margin = trim(SystemSettings::getSettings(SystemSettings::TYPE_DEFAULT_SALES_MARGIN));
		return $margin === '' ? 0 : $margin;
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
			$searchTxt = trim(isset($searchCriteria['searchTxt']) ? $searchCriteria['searchTxt'] : '');
			$categoryIds = (isset($searchCriteria['categoryIds']) && is_array($searchCriteria['categoryIds']) ? $searchCriteria['categoryIds'] : array());
			$language = (isset($searchCriteria['languageId']) && ($language = Language::get($searchCriteria['languageId'])) ? $language : null);
			
			$stats = array();
			$productArray = Product::findProductsInCategory(null, $searchTxt, $categoryIds, '', $language, ProductType::get(ProductType::ID_BOOK), true, $pageNumber, $pageSize, array('pro.id' => 'desc'), $stats);
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
				$array['gpm'] = Core::getLibrary()->getInfo('gross_profit_margin');
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
		$gpm=0;
		try
		{
			Dao::beginTransaction();
			if(!isset($param->CallbackParameter->orderId) || !($order = Order::get($param->CallbackParameter->orderId)) instanceof Order)
				throw new Exception('Invalid order id passed in!');
			if(!isset($param->CallbackParameter->productId) || !($product = Product::get($param->CallbackParameter->productId)) instanceof Product)
				throw new Exception('Invalid product id passed in!');
			if(!isset($param->CallbackParameter->qty) || !is_numeric($qty = trim($param->CallbackParameter->qty)))
				throw new Exception('Invalid qty passed in!');
			if(!isset($param->CallbackParameter->unitPrice) || !is_numeric($unitPrice = trim($param->CallbackParameter->unitPrice)))
				throw new Exception('Invalid price passed in!');
			
			OrderItem::create($order, $product, $qty, false, $unitPrice);
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