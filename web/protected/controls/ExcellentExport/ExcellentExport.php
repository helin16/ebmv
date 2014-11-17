<?php
class ExcellentExport extends TClientScript
{
	/**
     * (non-PHPdoc)
     * @see TPage::render()
     */
    public function onPreRender($param)
    {
        parent::onPreRender($param);
    	$clientScript = $this->getPage()->getClientScript();
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			$folder = $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);
			// Add jQuery library
			// Add mousewheel plugin (this is optional)
			$clientScript->registerScriptFile('excellentexport.js', $folder . '/excellentexport.min.js');
		}
    }
}