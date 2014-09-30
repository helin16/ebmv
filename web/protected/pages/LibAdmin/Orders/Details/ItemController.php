<?php
/**
 * This is the product listing page for library admin
*
* @package    Web
* @subpackage Controller
* @author     lhe<helin16@gmail.com>
*/
class ItemController extends LibAdminPageAbstract
{
	/**
	 * The selected Menu Item name
	 *
	 * @var string
	 */
	public $menuItemCode = 'order.details';
	/**
	 * (non-PHPdoc)
	 * @see FrontEndPageAbstract::_getEndJs()
	 */
	protected function _getEndJs()
	{
		$order = Order::get($this->request['id']);
		if(!$order instanceof Order)
			die('invalid order');
		$js = parent::_getEndJs();
		$js .= 'pageJs.setOrder(' . json_encode($order->getJson()) . ')';
		$js .= '.setCallbackId("delItem", "' . $this->delItemBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("saveOrder", "' . $this->saveOrderBtn->getUniqueID() . '")';
		$js .= '.setHTMLIds("item-details")';
		$js .= '.displayOrder()';
		$js .= ';';
		return $js;
	}
	public function onInit($params)
	{
		parent::onInit($params);
		$this->getPage()->setTheme($this->_getThemeByName('default'));
	}
	public function delItem($sender, $param)
	{
		$result = $errors = $productArray = array();
		try
		{
			if(!isset($param->CallbackParameter->id) || !($item = OrderItem::get($param->CallbackParameter->id)) instanceof OrderItem)
				throw new Exception('Invalid orderitem id!');
			$item->setActive(false)
				->save();
			$result['item'] = $item->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
			
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	public function saveOrder($sender, $param)
	{
		$result = $errors = $productArray = array();
		try
		{
			if(!isset($param->CallbackParameter->id) || !($order = Order::get($param->CallbackParameter->id)) instanceof Order)
				throw new Exception('Invalid orderitem id!');
			if(!isset($param->CallbackParameter->items) || count($items = $param->CallbackParameter->items) === 0)
				throw new Exception('At least one item needed!');
			$comments = "";
			if(!isset($param->CallbackParameter->comments) || ($comments = trim($param->CallbackParameter->comments)) !== '')
				$comments = $comments;
			
			foreach($items as $itemXml)
			{
				OrderItem::get($itemXml->id)
					->setQty($itemXml->qty)
					->save();
			}
			$order->setComments($comments)
				->setStatus(Order::STATUS_CLOSED)
				->save();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
			
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
}