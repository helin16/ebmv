<?php
class SC_XinMinWanBao extends SupplierConnectorOpenSourceAbstract implements SupplierConn 
{
	/**
	 * Getting the cover image
	 *
	 * @param string $productKey
	 *
	 * @throws SupplierConnectorException
	 * @return string
	 */
	protected function _getCoverImage($productKey)
	{
		$dateString = str_replace('/', '-', $productKey);
		return 'http://xmwb.xinmin.cn/resfile/' . $dateString . '/A01/Page_b.jpg';
	}
	protected function _getLanguageCode()
	{
		return 'zh-CN';
	}
	protected function _getProductKey(UDate $date)
	{
		return $date->format('Y-m/d');
	}
}