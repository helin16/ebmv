<?php

class AdvanceSearchControl extends TTemplateControl
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TPage::render()
	 */
	public function onInit($param)
	{
	    parent::onInit($param);
	    $this->getPage()->getClientScript()->registerPradoScript('ajax');
        $cScripts = FrontEndPageAbstract::getLastestJS(get_class($this));
	    if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
	        $this->getPage()->getClientScript()->registerScriptFile('asJs', $this->publishAsset($lastestJs));
	    if (isset($cScripts['css']) && ($lastestCss = trim($cScripts['css'])) !== '')
	        $this->getPage()->getClientScript()->registerStyleSheetFile('asCss', $this->publishAsset($lastestCss));
	}
	
	public function onLoad($param)
	{
		$this->getPage()->getClientScript()->registerEndScript('asJs', $this->_getEndJs());
	}
	
	private function _getEndJs()
	{
		$patArray = EntityDao::getInstance('ProductAttributeType')->findByCriteria('searchable = ?', array(1));
		$returnArray = array();
		foreach($patArray as $pat)
			$returnArray[$pat->getCode()] = $pat->getName();
		$catArray = EntityDao::getInstance('Category')->findAll();
		$returnArray2 = array();
		foreach($catArray as $cat)
			$returnArray2[$cat->getId()] = $cat->getName();
		//$js .= 'pageJs.pagination.pageSize = ' . $this->pageSize . ';';
		$js = 'var asJs = new AdvanceSearchJs(); ';
		$js .= 'asJs.attributeTypeArray = '.json_encode($returnArray).';';
		$js .= 'asJs.categoryArray = '.json_encode($returnArray2).';';
		return $js;
	}
}

?>