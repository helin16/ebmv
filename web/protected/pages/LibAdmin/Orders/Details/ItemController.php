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
	/**
	 * (non-PHPdoc)
	 * @see LibAdminPageAbstract::onInit()
	 */
	public function onInit($params)
	{
		parent::onInit($params);
		$this->getPage()->setTheme($this->_getThemeByName('default'));
	}
	/**
	 * deleting an orderitem
	 * 
	 * @param unknown $sender
	 * @param unknown $param
	 * 
	 * @throws Exception
	 */
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
	/**
	 * Saving the order
	 * 
	 * @param unknown $sender
	 * @param unknown $param
	 * 
	 * @throws Exception
	 */
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
			$this->_notifyAdmin($order);
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
			
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	/**
	 * Generating a new order to admin
	 * 
	 * @param Order $order
	 * 
	 * @throws Exception
	 */
	private function _notifyAdmin(Order $order)
	{
		$mail = new PHPMailer();

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.websiteforyou.com.au';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'test@websiteforyou.com.au';                 // SMTP username
		$mail->Password = 'TEST@websiteforyou.com.au';                           // SMTP password
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                    // TCP port to connect to
		
		$mail->From = 'noreplay@ebmv.com.au';
		$mail->FromName = 'New Order Generator';
		$mail->addAddress('dchen_oz@hotmail.com', 'Douglas');     // Add a recipient
		$mail->addCC('helin16@gmail.com');
		
		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->isHTML(true);                                  // Set email format to HTML
		
		$mail->Subject = 'New Order from: ' . Core::getLibrary()->getName();
		$mail->Body    = 'There is new order submited by <b>' . $order->getUpdatedBy()->getPerson()->getFullName() . '</b> @' . $order->getUpdated() . '(UTC)';
		$mail->AltBody = 'There is new order submited by ' . $order->getUpdatedBy()->getPerson()->getFullName() . '@' . $order->getUpdated() . '(UTC)';
		
		if(!$mail->send()) {
		    $msg = 'Message could not be sent.';
		    $msg .= 'Mailer Error: ' . $mail->ErrorInfo;
		    throw new Exception('Error: ' . $msg);
		} 
	}
}