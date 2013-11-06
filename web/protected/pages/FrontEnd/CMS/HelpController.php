<?php
/**
 * This is the help page
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class HelpController extends FrontEndPageAbstract
{
    /**
     * (non-PHPdoc)
     * @see FrontEndPageAbstract::_getEndJs()
     */
    protected function _getEndJs()
    {
        $js = parent::_getEndJs();
        $js .= 'pageJs.questions = ' . $this->_getQuestions() . ';';
        $js .= 'pageJs.init("topQs", "questionlist");';
        return $js;
    }
    private function _getQuestions()
    {
        $array = array(
            "怎样下载阅读中文简体电子书?" => '<dl><dt>1. 新华e店PC下载阅读客户端产品简介</dt>
        	<dd>新华e店PC下载阅读客户端是新华e店出品，是一款基于PC的单本电子书阅读客户端。它具有功能完善，界面友好，操作简单，无需安装等特点。</dd>
			<dt>2. 应用环境及语言支持</dt>
    		<dd>操作系统：WinXP SP2/Vista/Win7</dd>
    		<dd>语言：简体中文</dd> 
			<dt>3. 使用流程</dt>
    		<dd>用户登录后，在“我的书架”中点击“下载阅读”下载对应图书到本地。</dd>
        	<dd><img src="/themes/images/image190.png"></dd>
			<dt>4. 产品功能介绍</dt>
    		<dd>无需安装直接打开客户端，打开后提示用户授权，授权后自动记住用户当前状态下次启动客户端自动直接打开图书.</dd>
			<dt>5. 阅读界面</dt>
        	<dd><img src="/themes/images/image193.png"></dd>
    		<dd>A.	上一页：跳转到电子书上一页</dd>
    		<dd>B.	下一页：跳转到电子书下一页</dd>
    		<dd>C.	阅读进度显示：显示当前页码及总页数</dd>
    		<dd>D.	页面缩小：缩小当前页面，目前支持10%~500%</dd>
    		<dd>E.	页面放大：放大当前页面，目前支持10%~500%</dd>
    		<dd>F.	原版页面尺寸：选择后页面还原到原版尺寸</dd>
    		<dd>G.	适合宽度：适合窗口宽度并启用滚动</dd>
    		<dd>H.	适合整页：适合一个整页至窗口</dd>
    		<dd>I.	页面放大百分比：以百分比的形式显示当前页面放大比例</dd>
    		<dd>J.	评论数：显示当前该本书的评论数</dd>
    		<dd>K.	分享：实时分享阅读感想</dd>
    		<dd>L.	解绑 ：解除帐号在当前设备上看该本书的授权，一个帐号最多只能授权5台设备（包括PC/iPad/Android客户端）。</dd>
    		<dd>K.	分享：实时分享阅读感想</dd>
    		<dd>M.	关于：软件版本及版权信息</dd>
    		<dd>N.	帮助：软件帮助说明</dd>
        	</dl>'
        );
        return json_encode($array);
    }
}