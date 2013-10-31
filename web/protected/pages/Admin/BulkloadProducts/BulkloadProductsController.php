<?php
class BulkloadProductsController extends AdminPageAbstract
{
	public function __construct()
	{
		parent::__construct();    
	}
	
	public function onPreInit($param)
	{
		parent::onPreInit($param);
	}
	
	public function onInit($param)
	{
		parent::onInit($param);
	}
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		if(!$this->IsPostBack || $param == "reload")
		{
			$this->showInstruction();		
		}
	}
	
	private function _getInstructionDetails()
	{
		$output = array("SUK" => "The <b>SUK of the product</b>", "Title" => "<b style = 'color:red;'>Mandatory</b> The <b>title</b> of the product");
		$patArray = BaseServiceAbastract::getInstance("ProductAttributeType")->findAll();
		foreach($patArray as $pat)
			$output[$pat->getName()] = "The <b>".$pat->getCode()."</b> of the product";
		
		return $output;
	}
	
	public function generateTemplate($sender, $param)
	{
		$content = implode(",", array_keys($this->_getInstructionDetails()));
		$fileName = "TEMPLATE_FOR_PRODUCT_BULKLOAD";
		$fileName = $fileName."_".md5($content).".csv";
		
		$fullPath = $this->getApplication()->getAssetManager()->getBasePath() . '/' . $fileName;
		$errors = $return = array();
		try
		{
			if(!file_exists($fullPath))
				file_put_contents($fullPath, $content);
			$return = array('fileName' => $this->getApplication()->getAssetManager()->getBaseUrl() . '/' . $fileName);
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		
		$param->ResponseData = StringUtilsAbstract::getJson($return, $errors);
	}
	/**
	 * Event handler for download the file
	 * 
	 * @param TCallback          $sender The trigger
	 * @param TCallbackParameter $param  The params
	 */
	public function downloadUrl($sender, $param)
	{
		$errors = $return = array();
		try
		{
		    $url = trim(isset($param->CallbackParameter->url) ? $param->CallbackParameter->url : '');
		    $script = new ProductImportScript($this->getApplication()->getAssetManager()->getBasePath());
		    $errors = array();
		    $filePath = $script->getDataFromSoup($url, Config::get('site', 'id'), true, 1, 1000, $errors)->getTmpFile();
		    if(count(array_keys($errors)) > 0)
		    	throw new Exception('Error Page Index: ' . implode(', ', array_keys($errors)));
		    if(strlen($content = file_get_contents($filePath)) === 0)
		        throw new Exception('Empty Url found!');
		    $xml = simplexml_load_file($filePath);
		    
		    $return['totalCount'] = count($xml->children());
		    $return['filePath'] = $filePath;
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage() . $ex->getTraceAsString();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($return, $errors);
	}
	/**
	 * Event handler for importing product
	 * 
	 * @param TCallback          $sender The trigger
	 * @param TCallbackParameter $param  The params
	 */
	public function importProduct($sender, $param)
	{
		$errors = $return = array();
		try
		{
		    $index = trim(isset($param->CallbackParameter->index) ? $param->CallbackParameter->index : '');
		    $filePath = trim(isset($param->CallbackParameter->filePath) ? $param->CallbackParameter->filePath : '');
		    
		    $script = new ProductImportScript($this->getApplication()->getAssetManager()->getBasePath());
		    $products = $script->parseXmltoProduct($filePath, $index);
		    if(count($products) === 0)
		        throw new Exception('Nothing imported');
		    $return['product'] = $products[0]->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage() . $ex->getTraceAsString();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($return, $errors);
	}
	/**
	 * Event handler for importing product
	 * 
	 * @param TCallback          $sender The trigger
	 * @param TCallbackParameter $param  The params
	 */
	public function deleteImportFile($sender, $param)
	{
		$errors = $return = array();
		try
		{
		    $filePath = trim(isset($param->CallbackParameter->filePath) ? $param->CallbackParameter->filePath : '');
		    $script = new ProductImportScript($this->getApplication()->getAssetManager()->getBasePath());
		    $products = $script->removeTmpFile($filePath);
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage() . $ex->getTraceAsString();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($return, $errors);
	}
	/**
	 * Show the insturctions
	 */
	public function showInstruction()
	{
		$instructions = "<div>";
		$instructions.= "<table style='border: 5px #aaaaaa solid;width:100%; font-size:13px;' cellspacing='0' cellpadding='0'>
    						<tr>
    							<td style='background-color:#aaaaaa;padding:5px;'>Sample File filling instruction: </td>
    						</tr>
    						<tr>
    							<td>
    								<table width='100%'>
    									<tr style='height:23px;background:#cccccc;'>
    										<td width='5%'>Col. No.</td>
    										<td width='10%'>Column Name</td>
    										<td width='50%'>Description</td>
    									<tr>";
		$columnArray = $this->_getInstructionDetails();
		$rc = 0;
		foreach($columnArray as $key => $value)
			$instructions .= "<tr ".($rc % 2 === 0 ? "style='background-color:white;'" : "style='background-color:#cccccc;'")."><td>".(++$rc)."</td><td>".$key."</td><td>".$value."</td></tr>";
    							
		$instructions .= "</table></td></tr></table></div>";
		$this->instruction->setText($instructions);
	}
}