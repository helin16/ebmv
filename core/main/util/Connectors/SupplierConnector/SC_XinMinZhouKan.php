<?php
class SC_XinMinZhouKan extends SupplierConnectorOpenSourceAbstract implements SupplierConn 
{
	protected function _getCoverImageSrc(DOMDocument $doc)
	{
		return '';
	}
	protected function _getLanguageCode()
	{
		return 'zh-CN';
	}
	protected function _getProductKey(UDate $date)
	{
		return $date->format('Y/m/d');
	}
}