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
	const RUNNING_SCRIPT = 'ImportProduct';
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
		$js = 'var pImportView = new ProductImportViewJs(pageJs, "' . $this->getSupplierLibInfo->getUniqueID() . '", "' . $this->isImportInProgressBtn->getUniqueID() . '", "' . $this->importBtn->getUniqueID() . '", "' . $this->getLogBtn->getUniqueID() . '");';
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
	private function _isImporting()
	{
		$output = shell_exec('ps aux | grep ' . self::RUNNING_SCRIPT . '_Run.php | grep -v grep');
		return (trim($output) !== '' && strtolower(trim($output)) !== 'null');
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
			$result['isImporting'] = $this->_isImporting();
			$now = new UDate();
			if($result['isImporting'] === true)
			{
				$logs = BaseServiceAbastract::getInstance('Log')->findByCriteria('type = ?', array('ProductImportScript'), true, 1, 1, array("log.id" => 'desc'));
				if(count($logs) === 0)
					throw new Exception('System Error Occurred: no logs found when checking!');
				$result['transId'] = trim($logs[0]->getTransId());
			}
			$result['nowUTC'] = trim($now);
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	/**
	 * start importing
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 */
	public function import($sender, $param)
	{
		$result = $errors = array();
		try
		{
			
			if (!isset($param->CallbackParameter->libraryIds) || count($libraryIds = $param->CallbackParameter->libraryIds) === 0)
				throw new Exception('System Error: no libraryIds provided!');
			if (!isset($param->CallbackParameter->supplierIds) || count($supplierIds = $param->CallbackParameter->supplierIds) === 0)
				throw new Exception('System Error: no supplierIds provided!');
			if (!isset($param->CallbackParameter->maxQty) || (($maxQty = trim($param->CallbackParameter->maxQty)) === '') || ($maxQty !== 'all' && (!is_numeric($maxQty) || intval($maxQty) <= 0)) )
				throw new Exception('System Error: invalid maxQty provided: ' . $maxQty . '!');
			
			$libCodes = array();
			foreach(BaseServiceAbastract::getInstance('Library')->findByCriteria('id in (' . implode(', ', $libraryIds) . ')', array()) as $lib)
			{
				$libCodes = array_merge($libCodes, explode(',', $lib->getInfo('aus_code')));
			}
			$libCodes = array_unique($libCodes);
			
			$now = new UDate();
			$scriptName = self::RUNNING_SCRIPT;
			$class = new ReflectionClass(new $scriptName());
			$script = 'nohup php ' . dirname($class->getFileName()) . DIRECTORY_SEPARATOR . $scriptName . '_Run.php';
			$script .= ' ' . implode(',', $libCodes);
			$script .= ' ' . implode(',', $supplierIds);
			$script .= ' ' . $maxQty;
			$script .= ' > /dev/null 2>/dev/null &';
			var_dump($script);
			$output = shell_exec($script);
			if($output === false)
				throw new Exception('System Error Occurred when trying to run the script.');
			
			sleep(1);
			$logs = BaseServiceAbastract::getInstance('Log')->findByCriteria('type = ?', array('ProductImportScript'), true, 1, 1, array("log.id" => 'desc'));
			if(count($logs) === 0)
				throw new Exception('System Error Occurred: no logs found!');
			$result['transId'] = trim($logs[0]->getTransId());
			$result['nowUTC'] = trim($now);
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
	/**
	 * getting the logs for the importing progress
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 */
	public function getLogs($sender, $param)
	{
		$result = $errors = array();
		try
		{
			if ((!isset($param->CallbackParameter->nowUTC)) || ($nowUTC = trim($param->CallbackParameter->nowUTC)) === '')
				throw new Exception('System Error: now time not passed in!');
			if (!isset($param->CallbackParameter->transId) || ($transId = trim($param->CallbackParameter->transId)) === '')
				throw new Exception('System Error: no transId passed!');
			
			$result['logs'] = array();
			$logs = BaseServiceAbastract::getInstance('Log')->findByCriteria('transId = ? and created >= ?', array($transId, $nowUTC));
			foreach ($logs as $log)
			{
				$result['logs'][] = $log->getJson();
			}
			$result['hasMore'] = $this->_isImporting();
			$result['nowUTC'] = trim(new UDate());
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($result, $errors);
	}
}