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
					->setNeedMARCRecord(trim($itemXml->needMARC) === '1')
					->save();
			}
			$order->setComments($comments)
				->submit(Core::getUser())
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
		$mail->addAttachment($this->_getOrderExcel($order));    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML
		
		$mail->Subject = 'EBMV: New Order(No.:' . $order->getOrderNo() . ') from: ' . Core::getLibrary()->getName();
		$mail->Body    = 'There is new order submited by <b>' . $order->getUpdatedBy()->getPerson()->getFullName() . '</b> @' . $order->getUpdated() . '(UTC)';
		$mail->AltBody = 'There is new order submited by ' . $order->getUpdatedBy()->getPerson()->getFullName() . '@' . $order->getUpdated() . '(UTC)';
		
		if(!$mail->send()) {
		    $msg = 'Message could not be sent.';
		    $msg .= 'Mailer Error: ' . $mail->ErrorInfo;
		    throw new Exception('Error: ' . $msg);
		} 
	}
	
	private function _getOrderExcel(Order $order)
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$rowNo = 1;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowNo, 'Order No.:');
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowNo, $order->getOrderNo());
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowNo, 'Library:');
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowNo, Core::getLibrary()->getName());
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowNo, 'Created By:');
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowNo, $order->getUpdatedBy()->getPerson()->getFullName());
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowNo, 'Created @:');
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowNo, $order->getUpdated() . '(UTC)');
		
		$rowNo++;
		$rowNo++;
		$this->_getTableRow($objPHPExcel->getActiveSheet(), $rowNo, 'TITLE', 'ISBN', 'CNO', 'AUTHOR', 'PUBLISHER', 'PUBLISH DATE', 'LENGTH', 'CIP','DESCRIPTION', 'QTY', 'NEED MARC?');
		foreach(OrderItem::getAllByCriteria('orderId = ?', array($order->getId())) as $item)
		{
			$rowNo++;
			$product = $item->getProduct();
			$this->_getTableRow($objPHPExcel->getActiveSheet(), $rowNo,
				$product->getTitle(),
				$product->getAttribute('ISBN'),
				$product->getAttribute('Cno'),
				$product->getAttribute('Author'),
				$product->getAttribute('Publisher'),
				$product->getAttribute('PublishDate'),
				$product->getAttribute('Number Of Words'),
				$product->getAttribute('Cip'),
				$product->getAttribute('Description'),
				$item->getQty(),
				trim($item->getNeedMARCRecord()) === '1'
			);
		}
		
		$rowNo++;
		$rowNo++;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowNo, 'Comments:');
		$rowNo++;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowNo, $order->getComments());
		$objPHPExcel->getActiveSheet()->setTitle('Order Details');
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$filePath = '/tmp/' . $order->getOrderNo() . '.xlsx';
		$objWriter->save($filePath);
		return $filePath;
	}
	
	private function _getTableRow(&$sheet, $rowNo,  $title, $isbn, $cno, $author, $publisher, $publishDate, $length, $cip, $description, $qty, $needMARC)
	{
		$sheet->SetCellValue('A' . $rowNo, $title);
		$sheet->SetCellValue('B' . $rowNo, $isbn);
		$sheet->SetCellValue('C' . $rowNo, $cno);
		$sheet->SetCellValue('D' . $rowNo, $author);
		$sheet->SetCellValue('E' . $rowNo, $publisher);
		$sheet->SetCellValue('F' . $rowNo, $publishDate);
		$sheet->SetCellValue('G' . $rowNo, $length);
		$sheet->SetCellValue('H' . $rowNo, $cip);
		$sheet->SetCellValue('I' . $rowNo, $description);
		$sheet->SetCellValue('J' . $rowNo, $qty);
		$sheet->SetCellValue('H' . $rowNo, $needMARC);
	}
}