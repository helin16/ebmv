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
"How can I access eBMV? " => 
'<dl>
<dd>
You can access eBMV from the link on your library’s website. You can browse and search
the catalogue and preview e-books before you login. If you want to read a whole e-book,
newspaper or magazine, login with your library card and PIN.
</dd>
<span class="questionsub">
如何使用eBMV中文电子资源?
</span>
<dd>
你可以从图书馆网页上的链接进入eBMV中文电子资源网页. 在登录之前你可以浏览和搜寻
目录, 也可以预览每本电子书的开始部分. 如果你要阅读一本电子书的全部, 或者阅读报纸
和杂志, 请先登录. 登录号是你的图书馆卡号和密码. 
</dd>
<span class="questionsub">
如何使用eBMV中文電子資源? 
</span>
<dd>
你可以從圖書館網頁上的鏈接進入eBMV中文電子資源網頁. 在登錄之前你可以流覽和搜尋
目錄, 也可以預覽每本電子書的開始部份. 如果你要閱讀一本電子書的全部, 或者閱讀報紙
和雜誌, 請先登錄. 登錄號是你的圖書館卡號和 密碼. 
</dd>
</dl>',

"How can I read Simplified e-books? " =>
'<dl>
<dd>
Click on the cover of the book you want to read. There are two buttons to select:
<b>Read Online</b> and <b>Download This Book</b>. Read Online will work on anything that runs Windows, Mac OSX, iOS or Android.
</dd>
<span class="questionsub">
如何在线阅读简体中文电子书? 
</span>
</dd>
<dd>
在目录上选择你要读的简体中文电子书, 点击封面.  在新的网页上有这本书的元数据, 还有
两个按钮供选择: “在线阅读” 和 “下载阅读”.  在线阅读可在运行微软公司视窗, 苹果
公司OS和安卓操作系统的桌上, 手提和平板电脑上实现. 
</dd>
</dl>',

"What browsers are supported? "=>
'<dl>
<dd>
Most modern browsers are supported; Microsoft IE 8 or later, Apple’s Safari, Google
Chrome, Firefox and more. 
</dd>
<span class="questionsub">
哪些浏览器, 浏览器版本可以用来在线阅读? 
</span>
<dd>
微软公司IE 第8版或以上,  苹果公司 Safari, 谷歌Chrome, 火狐Firefox. 
</dd>
</dl>',

"How can I download and read Simplified Chinese e-books? "=>
'<dl>
<dd>
Click on the cover of the book that you want to read. Then select <b>Download</b>. After the
download has finished open the file to start reading. 
</dd>
<span class="questionsub">
如何下载阅读简体中文电子书? 
</span>
</dd>
<dd>
在目录上选择你要读的简体中文电子书, 点击封面.  在新的网页上有这本书的元数据, 还有
两个按钮供选择: “在线阅读” 和 “下载阅读”. 
</dd>
<dd>
下载阅读现只可以在运行微软公司视窗操作系统(Windows XP SP2, Vista, Windows7)
的桌上, 手提和平板电脑上实现. 下载的文件是一个已包含阅读器的压缩文件 (.zip).  经解
压之后得到一个执行文件(.exe).  双击此文件即可开始阅读. 
</dd>
</dl>',

"How can I read Simplified Chinese e-newspapers and e-magazines? "=>
'<dl>
<dd>
Click on the e-newspaper or the e-magazine that you would like to read. 
</dd>
<span class="questionsub">
如何阅读简体中文电子报刊, 电子杂志? 
</span>
<dd>
简体中文电子报刊, 电子杂志服务支持在线阅读. 读者可以从图书馆网页上的链接进入
eBMV中文电子资源网页. 在登录之前你可以浏览和搜寻目录. 登录后选择中意的电子报刊
可在运行微软公司视窗, 苹果公司OS和安卓操作系统的桌上, 手提和平板电脑上阅读. 
</dd>
</dl>',

"How can I read Traditional  e-books and e-magazines? "=>
'<dl>
<dd>
To select the traditional Chinese e-book or e-magazine you want to read, click its cover.
Then click <b>Borrow</b> button. After borrowing the e-book or e-magazine you can read it online
on your computer. 
</dd>
<dd>
If want to read on your iOS device or Android device, go to the App Store or Google Play
and search for <b>臺灣雲端書庫2014</b>. 
</dd>
<span class="questionsub">
如果閱讀繁體中文電子書 和電子雜誌? 
</span>
</dd>
<dd>
在目錄上選擇你要閱讀的繁體中文電子書 和電子雜誌, 點擊封面. 在新的網頁上有此出版物
的元數據, 以及一個”借閱”按鈕. 借閱后你即可以在PC, NB, 平板電腦 上連線閱讀.  
如要用苹果或安卓移动终端離線閱讀, 需先到Apple App Store或 Google Play 下載 臺
灣雲端書庫2014 app.
</dd>
</dl>'     		
        );
        return json_encode($array);
    }
}