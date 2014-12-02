<?php
/**
 * The Fancy Select Box
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class Chosen extends TClientScript
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		$clientScript = $this->getPage()->getClientScript();
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			// Add chosen main JS and CSS files
			$folder = $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR);
			
			$clientScript->registerStyleSheetFile('chosen.css', $folder . '/chosen.css', 'screen');
			$clientScript->registerScriptFile('chosen.jquery', $folder . '/chosen.jquery.min.js');
		}
	}
}