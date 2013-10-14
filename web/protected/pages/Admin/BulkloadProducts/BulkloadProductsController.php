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
		$patArray = BaseService::getInstance("ProductAttributeType")->findAll();
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
				
//			chmod($fullPath, 0777);
			$return = array('fileName' => $this->getApplication()->getAssetManager()->getBaseUrl() . '/' . $fileName);
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		
		$param->ResponseData = StringUtilsAbstract::getJson($return, $errors);
	}
	
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