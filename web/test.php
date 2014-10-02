<?php 
require_once 'bootstrap.php';
 
class TEST
{
	public function run(Order $order)
	{
		$file = $this->_getOrderExcel($order);
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}
	}
	
	private function _getOrderExcel(Order $order)
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$rowNo = 1;
		$titleRowNo = $rowNo;
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->SetCellValue('A' . $rowNo, 'Order No.:');
		$sheet->getStyle('A' . $rowNo)->getFont()->setBold(true);
		$sheet->SetCellValue('B' . $rowNo, $order->getOrderNo());
		$sheet->SetCellValue('D' . $rowNo, 'Library:');
		$sheet->getStyle('D' . $rowNo)->getFont()->setBold(true);
		$sheet->SetCellValue('E' . $rowNo, '');
		$sheet->SetCellValue('G' . $rowNo, 'Submit By:');
		$sheet->getStyle('G' . $rowNo)->getFont()->setBold(true);
		$sheet->SetCellValue('H' . $rowNo, $order->getSubmitBy() instanceof UserAccount ? $order->getSubmitBy()->getPerson()->getFullName() : '');
		$sheet->SetCellValue('J' . $rowNo, 'Submit @:');
		$sheet->getStyle('J' . $rowNo)->getFont()->setBold(true);
		$sheet->SetCellValue('K' . $rowNo, $order->getUpdated() . '(UTC)');
		//display the order items
		$rowNo++;
		$rowNo++;
		$tableRowStart = $rowNo;
		$this->_getTableRow($sheet, $rowNo, 'TITLE', 'ISBN', 'CNO', 'AUTHOR', 'PUBLISHER', 'PUBLISH DATE', 'LENGTH', 'CIP','DESCRIPTION', 'QTY', 'TOTAL PRICE', 'NEED MARC?');
		foreach(OrderItem::getAllByCriteria('orderId = ?', array($order->getId())) as $item)
		{
			$rowNo++;
			$product = $item->getProduct();
			$this->_getTableRow($sheet, $rowNo,
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
					$item->getTotalPrice(),
					trim($item->getNeedMARCRecord()) === '1' ? 'Y' : 'N'
			);
		}
		$tableRowEnd = $rowNo;
		//summary
		$rowNo++;
		$sheet->SetCellValue('A' . $rowNo, 'Comments:');
		$sheet->getStyle('A' . $rowNo)->getFont()->setBold(true);
		$sheet->SetCellValue('J' . $rowNo, 'Subtotal:');
		$sheet->getStyle('J' . $rowNo)->getFont()->setBold(true);
		$sheet->SetCellValue('K' . $rowNo, '=SUM(K' . ($tableRowStart + 1) . ':K' . $tableRowEnd . ')');
		
		$rowNo++;
		$sheet->SetCellValue('A' . $rowNo, $order->getComments());
		$sheet->mergeCells('A' . $rowNo . ':H' . ($rowNo + 1));
		$sheet->getStyle('A' . $rowNo . ':I' . ($rowNo + 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$sheet->SetCellValue('J' . $rowNo, 'GST:');
		$sheet->getStyle('J' . $rowNo)->getFont()->setBold(true);
		$sheet->SetCellValue('K' . $rowNo, '=K' . ($rowNo - 1) . '* 0.1');
		$rowNo++;
		$sheet->SetCellValue('J' . $rowNo, 'TOTAL');
		$sheet->getStyle('J' . $rowNo)->getFont()->setBold(true);
		$sheet->SetCellValue('K' . $rowNo, '=sum(K' . ($rowNo - 2) . ':K' . ($rowNo - 1) . ')');
		
		$sheet->setTitle('Order Details');
		//set style
		$this->_setStyle($sheet, $titleRowNo, $tableRowStart, $tableRowEnd);
		//write to a file
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$filePath = '/tmp/' . $order->getOrderNo() . '.xlsx';
		$objWriter->save($filePath);
		return $filePath;
	}
	
	private function _setStyle(&$sheet, $titleRowNo, $tableStartRowNo, $tableEndRowNo)
	{
		$sheet->getStyle($titleRowNo . ':' . $titleRowNo)->getFont()->setSize(20);
		$sheet->getStyle('A' . $tableStartRowNo . ':L' . $tableStartRowNo)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$sheet->getStyle('A' . $tableStartRowNo . ':L' . $tableStartRowNo)->getFill()->getStartColor()->setARGB('FF808080');
		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		$sheet->getColumnDimension('I')->setWidth(20);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		$sheet->getColumnDimension('K')->setAutoSize(true);
		$sheet->getColumnDimension('L')->setAutoSize(true);
		
		$styleThinBlackBorderOutline = array(
				'borders' => array(
						'outline' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('argb' => 'FF000000'),
						),
				),
		);
		$sheet->getStyle('A' . $tableStartRowNo . ':L' . $tableEndRowNo)->applyFromArray($styleThinBlackBorderOutline);
		
		$sheet->getStyle('K' . $tableStartRowNo . ':K' . ($tableEndRowNo + 3))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
	}
	
	private function _getTableRow(&$sheet, $rowNo,  $title, $isbn, $cno, $author, $publisher, $publishDate, $length, $cip, $description, $qty, $totalPrice, $needMARC)
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
		$sheet->SetCellValue('K' . $rowNo, $totalPrice);
		$sheet->SetCellValue('L' . $rowNo, $needMARC);
	}
}
try {
	$runner = new TEST();
	$runner->run(Order::get(5));
} catch (Exception $e) {
	var_dump($e->getMessage());
}

?>