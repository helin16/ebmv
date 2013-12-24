<?php
/**
 * The ProductImportView Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class ProductImportView extends TTemplateControl
{
	/**
	 * (non-PHPdoc)
	 * @see TPage::render()
	 */
	public function onLoad($param)
	{
		parent::onLoad($param);
		$cScripts = FrontEndPageAbstract::getLastestJS(get_class($this));
		$clientScript = $this->getPage()->getClientScript();
		if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
			$clientScript->registerScriptFile(get_class($this) . '_js', $this->publishAsset($lastestJs));
		if (isset($cScripts['css']) && ($lastestCss = trim($cScripts['css'])) !== '')
			$clientScript->registerStyleSheetFile(get_class($this) . '_css', $this->publishAsset($lastestCss));
		$clientScript->registerEndScript(get_class($this) . '_js_' . $this->getId(), $this->_getJs());
	}
	private function _getJs()
	{
		$js = 'var pImportView = new ProductImportViewJs(pageJs, "' . $this->getSupplierLibInfo->getUniqueID() . '", "' . $this->isImportInProgressBtn->getUniqueID() . '", "' . $this->importBtn->getUniqueID() . '");';
		return $js;
	}
	
	/**
	 * Getting the supplier and/or library information
	 */
	public function getSupLibInfo($sender, $param)
	{
		$result = $errors = array();
		try
		{
			$result['suppliers'] = $result['libraries'] = array();
			if (($getSuppliers = (isset($param->CallbackParameter->suppliers) && $param->CallbackParameter->suppliers === true)) === true)
			{
				foreach(BaseServiceAbastract::getInstance('Supplier')->findAll() as $sup)
					$result['suppliers'][] = $sup->getJson();
			}
			
			if (($getLibraries = (isset($param->CallbackParameter->libraries) && $param->CallbackParameter->libraries === true)) === true)
			{
				foreach(BaseServiceAbastract::getInstance('Library')->findAll() as $lib)
					$result['libraries'][] = $lib->getJson();
			}
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	/**
	 * is importing progress
	 * 
	 * @param unknown $sender
	 * @param unknown $param
	 */
	public function isImportInProgress($sender, $param)
	{
		$result = $errors = array();
		try
		{
			//todo:: checking whether we are having the script running already
			$result['isImporting'] = true;
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
}