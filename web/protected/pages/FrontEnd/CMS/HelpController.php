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
			 array (
        		'en' => array (
        			'question' => "How can I access eBMV?"
        			,'answer'   => 'You can access eBMV from the link on your library’s website. You can browse and search the catalogue and preview e-books before you login. If you want to read a whole e-book, newspaper or magazine, login with your library card and PIN.'
				)
			 	,'zh_cn' => array (
        			'question' => "如何使用eBMV中文电子资源?"
        			,'answer'  => '你可以从图书馆网页上的链接进入eBMV中文电子资源网页. 在登录之前你可以浏览和搜寻目录, 也可以预览每本电子书的开始部分. 如果你要阅读一本电子书的全部, 或者阅读报纸和杂志, 请先登录. 登录号是你的图书馆卡号和密码.'
				)
			 	,'zh_tw' => array (
        			'question' => "如何使用eBMV中文電子資源? "
        			,'answer'  => '你可以從圖書館網頁上的鏈接進入eBMV中文電子資源網頁. 在登錄之前你可以流覽和搜尋目錄, 也可以預覽每本電子書的開始部份. 如果你要閱讀一本電子書的全部, 或者閱讀報紙和雜誌, 請先登錄. 登錄號是你的圖書館卡號和密碼.'
				)
			 )
			 ,array (
        		'en' => array (
        			'question' => "How can I read Simplified e-books?"
        			,'answer'   => 'Click on the cover of the book you want to read. There are two buttons to select: <b>Read Online</b> and <b>Download This Book</b>. Read Online will work on anything that runs Windows, Mac OSX, iOS or Android.'
				)
			 	,'zh_cn' => array (
        			'question' => "如何在线阅读简体中文电子书?"
        			,'answer'  => '在目录上选择你要读的简体中文电子书, 点击封面.  在新的网页上有这本书的元数据, 还有两个按钮供选择: “在线阅读” 和 “下载阅读”.  在线阅读可在运行微软公司视窗, 苹果公司OS和安卓操作系统的桌上, 手提和平板电脑上实现. '
				)
			 	,'zh_tw' => array (
        			'question' => "如何在線閱讀簡體中文電子書?"
        			,'answer'  => '在目錄上選擇你要讀的簡體中文電子書, 點擊封面.  在新的網頁上有這本書的元數據, 還有兩個按鈕供選擇: “在線閱讀” 和“下載閱讀”.  在線閱讀可在運行微軟公司視窗, 蘋果公司OS和安卓操作系統的桌上, 手提和平板電腦上實現.'
				)
			 )
			 ,array (
        		'en' => array (
        			'question' => "What browsers are supported?"
        			,'answer'   => 'Most modern browsers are supported; Microsoft IE 8 or later, Apple’s Safari, Google Chrome, Firefox and more. '
				)
			 	,'zh_cn' => array (
        			'question' => "哪些浏览器, 浏览器版本可以用来在线阅读?"
        			,'answer'  => '微软公司IE 第8版或以上,  苹果公司 Safari, 谷歌Chrome, 火狐Firefox. '
				)
			 	,'zh_tw' => array (
        			'question' => "哪些瀏覽器, 瀏覽器版本可以用來在線閱讀?"
        			,'answer'  => '微軟公司IE 第8版或以上,  蘋果公司Safari, 谷歌Chrome, 火狐Firefox.'
				)
			 )
			 ,array (
        		'en' => array (
        			'question' => "How can I download and read Simplified Chinese e-books?"
        			,'answer'   => 'Click on the cover of the book that you want to read. Then select <b>Download</b>. After the download has finished open the file to start reading. '
				)
			 	,'zh_cn' => array (
        			'question' => "如何下载阅读简体中文电子书?"
        			,'answer'  => '在目录上选择你要读的简体中文电子书, 点击封面.在新的网页上有这本书的元数据, 还有两个按钮供选择: “在线阅读” 和 “下载阅读”. 下载阅读现只可以在运行微软公司视窗操作系统(Windows XP SP2, Vista, Windows7)的桌上, 手提和平板电脑上实现. 下载的文件是一个已包含阅读器的压缩文件 (.zip).  经解压之后得到一个执行文件(.exe).  双击此文件即可开始阅读. '
				)
			 	,'zh_tw' => array (
        			'question' => "如何下載閱讀簡體中文電子書?"
        			,'answer'  => '在目錄上選擇你要讀的簡體中文電子書, 點擊封面.在新的網頁上有這本書的元數據, 還有兩個按鈕供選擇: “在線閱讀” 和“下載閱讀”. 下載閱讀現只可以在運行微軟公司視窗操作系統(Windows XP SP2, Vista, Windows7)的桌上, 手提和平板電腦上實現. 下載的文件是一個已包含閱讀器的壓縮文件(.zip).  經解壓之後得到一個執行文件(.exe).  雙擊此文件即可開始閱讀.'
				)
			 )
			 ,array (
        		'en' => array (
        			'question' => "How can I read Simplified Chinese e-newspapers and e-magazines?"
        			,'answer'   => 'Click on the e-newspaper or the e-magazine that you would like to read. '
				)
			 	,'zh_cn' => array (
        			'question' => "如何阅读简体中文电子报刊, 电子杂志?"
        			,'answer'  => '简体中文电子报刊, 电子杂志服务支持在线阅读. 读者可以从图书馆网页上的链接进入eBMV中文电子资源网页. 在登录之前你可以浏览和搜寻目录. 登录后选择中意的电子报刊可在运行微软公司视窗, 苹果公司OS和安卓操作系统的桌上, 手提和平板电脑上阅读. '
				)
			 	,'zh_tw' => array (
        			'question' => "如何閱讀簡體中文電子報刊, 電子雜誌?"
        			,'answer'  => '簡體中文電子報刊, 電子雜誌服務支持在線閱讀. 讀者可以從圖書館網頁上的鏈接進入eBMV中文電子資源網頁. 在登錄之前你可以瀏覽和搜尋目錄. 登錄後選擇中意的電子報刊可在運行微軟公司視窗, 蘋果公司OS和安卓操作系統的桌上, 手提和平板電腦上閱讀.'
				)
			 )
			 ,array (
        		'en' => array (
        			'question' => "How can I read Traditional  e-books and e-magazines?"
        			,'answer'   => 'To select the traditional Chinese e-book or e-magazine you want to read, click its cover. Then click <b>Borrow</b> button. After borrowing the e-book or e-magazine you can read it online on your computer. If you want to read on your iOS device or Android device, go to the App Store or Google Play and search for <b>臺灣雲端書庫2014</b>. '
				)
			 	,'zh_cn' => array (
        			'question' => "如果阅读繁体中文电子书和电子杂志?"
        			,'answer'  => '在目录上选择你要阅读的繁体中文电子书和电子杂志, 点击封面. 在新的网页上有此出版物的元数据, 以及一个”借阅”按钮. 借阅后你即可以在PC, NB, 平板电脑上连线阅读.如要用苹果或安卓移动终端离线阅读, 需先到Apple App Store或Google Play 下载台湾云端书库2014 app.'
				)
			 	,'zh_tw' => array (
        			'question' => "如果閱讀繁體中文電子書和電子雜誌?"
        			,'answer'  => '在目錄上選擇你要閱讀的繁體中文電子書和電子雜誌, 點擊封面. 在新的網頁上有此出版物的元數據, 以及一個”借閱”按鈕. 借閱后你即可以在PC, NB, 平板電腦上連線閱讀.如要用苹果或安卓移动终端離線閱讀, 需先到Apple App Store或 Google Play 下載臺灣雲端書庫2014 app.'
				)
			 )
        );
        return json_encode($array);
    }
}