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
		$js .= '.setHTMLIds("item-details")';
		$js .= '.displayOrder()';
		$js .= ';';
		return $js;
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
}