<?php
class GoogleAnalytics extends TClientScript
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
			$clientScript->registerEndScript('google.analytics', $this->_getJs());
		}
	}
	/**
	 * Getting the google 's code
	 * @return string
	 */
	private function _getJs()
	{
		$js = '';
		if(Core::getLibrary() instanceof Library && ($analyticsTrackingCode = trim(Core::getLibrary()->getInfo('analytics_track_code'))) !== '') {
			$js .= '(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){ (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) })(window,document, "script","//www.google-analytics.com/analytics.js","ga");';
			$js .= 'ga("create", "' . $analyticsTrackingCode . '", "auto");';
			$js .= 'ga("send", "pageview");';
		}
		return $js;
	}
}