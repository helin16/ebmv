<?php
class SC_TaKungPao extends SupplierConnectorOpenSourceAbstract implements SupplierConn
{
	protected function _getCoverImageSrc(DOMDocument $doc)
	{
		$src = '';
		$xpath = new DOMXPath($doc);
		$books = $xpath->query("//dl[@class='imglist']/dd/div/img");
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
		return $date->format('Ymd');
	}
}