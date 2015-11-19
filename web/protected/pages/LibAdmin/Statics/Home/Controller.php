<?php
/**
 * This is the Home page for statics admin
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class Controller extends LibAdminPageAbstract
{
	/**
	 * The selected Menu Item name
	 *
	 * @var string
	 */
	public $menuItemCode = 'statics';
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'pageJs.setCallbackId("getStats", "' . $this->getStatsBtn->getUniqueID() . '")';
		$js .= '.setCallbackId("exportSats", "' . $this->exportSatsBtn->getUniqueID() . '")';
		$js .= '.setHTMLIDs("top-viewed", "total-count")';
		$js .= '.load()';
		$js .= ';';
		return $js;
	}
	/**
	 * Getting the statics
	 * 
	 * @param unknown $sender
	 * @param unknown $param
	 */
	public function getStats($sender, $param)
	{
		$result = $errors = array();
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
			$stats = array();
// 			Product::getQuery()->eagerLoad('Product.libOwns', DaoQuery::DEFAULT_JOIN_TYPE, 'lib_own', 'lib_own.libraryId = ? and lib_own.productId = pro.id and lib_own.active = 1')->eagerLoad('Product.productStatics', 'left join', 'pstats')->eagerLoad('ProductStatics.type', 'left join', 'pstatstype');
			
			$products = Product::getMostPopularProducts(Core::getLibrary(), null, null, $pageNumber, $pageSize, array('pstats.value'=>'desc'), $stats);
			$result['items'] = array();
			foreach($products as $product)
			{
				$array = $product->getJson();
				$array['statics'] = array();
				foreach(ProductStaticsType::getAll() as $type)
				{
					$statics = $product->getStatic(Core::getLibrary(), $type);
					$array['statics'][$type->getId()] = ($statics instanceof ProductStatics ? $statics->getJson() : array());
				}
				$result['items'][] = $array;
			}
			$result['pagination'] =  $stats;
		}
		catch (Exception $e)
		{
			$errors[] = $e->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	/**
	 * Getting the statics
	 * 
	 * @param unknown $sender
	 * @param unknown $param
	 */
	public function exportSats($sender, $param)
	{
		$result = $errors = array();
		try 
		{
			$dateFrom = (isset($param->CallbackParameter->fromDate) && trim($param->CallbackParameter->fromDate) !== '') ? trim($param->CallbackParameter->fromDate) : '';
			$dateTo = (isset($param->CallbackParameter->toDate) && trim($param->CallbackParameter->toDate) !== '') ? trim($param->CallbackParameter->toDate) : '';
			if($dateFrom === '')
				throw new Exception('From Date is required!');
			if($dateTo === '')
				throw new Exception('To Date is required!');
			$libTimeZone = trim(Core::getLibrary()->getInfo('lib_timezone'));
			$dateFrom = new UDate($dateFrom . ' 00:00:00', $libTimeZone);
			$dateFrom->setTimeZone('UTC');
			$dateTo = new UDate($dateTo . ' 23:59:59', $libTimeZone);
			$dateTo->setTimeZone('UTC');
			
			$sql = "select stat.value `Log count`, stat_type.name `Log Type`, stat.created `Log Time`, pro.title, pi_isbn.attribute `ISBN`, pi_author.attribute `Author`, pi_publisher.attribute `Publisher`, pi_publishdate.attribute `Publish Date`
					from productstaticslog stat
					inner join product pro on (pro.id = stat.productId and pro.active = 1)
					inner join productstaticstype stat_type on (stat_type.id = stat.typeId) 
					left join productattribute pi_isbn on (pi_isbn.productId = pro.id and pi_isbn.typeId = 2 and pi_isbn.active = 1) 
					left join productattribute pi_author on (pi_author.productId = pro.id and pi_author.typeId = 1 and pi_author.active = 1)
					left join productattribute pi_publisher on (pi_publisher.productId = pro.id and pi_publisher.active = 1 and pi_publisher.typeId = 3)
					left join productattribute pi_publishdate on (pi_publishdate.productId = pro.id and pi_publishdate.active = 1 and pi_publishdate.typeId = 4)
					where stat.libraryId = ? and stat.created >= ? and stat.created <= ?";
			$results = array();
			foreach(Dao::getResultsNative($sql, array(Core::getLibrary()->getId(), trim($dateFrom), trim($dateTo)), PDO::FETCH_ASSOC) as $row)
			{
				$logTime = new UDate(trim($row['Log Time']), 'UTC');
				$logTime->setTimeZone($libTimeZone);
				$row['Log Time'] = trim($logTime);
				$results[] = $row;
			}
			$filePath = $this->_getExcel($results);
			$dir = dirname(__FILE__) . '/../../../../asset/report/';
			die($dir);
			$assetId = Asset::registerAsset('statics_export.xlsx', $filePath, $dir);
			if(!($asset = Asset::getAsset($assetId)) instanceof Asset)
				throw new Exception('System Error: can NOT generate excel file');
			$result['url'] = trim($asset);
		}
		catch (Exception $e)
		{
			$errors[] = $e->getMessage() . $e->getTraceAsString();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	private function _getExcel($data)
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();
		$rowIndex = 1;
		foreach($data as $i => $row)
		{
			if($rowIndex === 1)
			{
				foreach(array_keys($row) as $index => $title)
					$sheet->getCellByColumnAndRow($index, $rowIndex)->setValue($title);
				$rowIndex++;
			}
			
			foreach(array_values($row) as $index => $title)
				$sheet->getCellByColumnAndRow($index, $rowIndex)->setValue($title);
			$rowIndex++;
		}
	
		$sheet->setTitle('Statics_export');
		//write to a file
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$filePath = '/tmp/Statics_export_' . Core::getLibrary()->getId() . '.xlsx';
		$objWriter->save($filePath);
		return $filePath;
	}
}
?>