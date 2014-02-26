<?php
class SC_WenHuiPo extends SupplierConnectorOpenSourceAbstract implements SupplierConn
{
	protected function _getCoverImageSrc(DOMDocument $doc)
	{
		$src = '';
		$xpath = new DOMXPath($doc);
		$books = $xpath->query("//ul[@id='pdf_c_c1']/li/a/img");
		if($books->item(0) instanceof DOMElement)
			$src = $books->item(0)->getAttribute('src');
		return $src;
	}
	protected function _getLanguageCode()
	{
		return 'zh-tw';
	}
	protected function _getProductKey(UDate $date)
	{
		return $date->format('Y/m/d');
	}
}