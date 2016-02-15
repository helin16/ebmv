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
        			,'answer'   => '<p>You can access eBMV Chinese Language e-Resource via the link on your library’s website. '
        				          .'You can browse and search catalogue, or preview e-books before you login. '
        				          .'If you’d like to read a whole e-book, or read newspaper and magazine, please login with your library card and PIN.</p>'
				)
			 	,'zh_cn' => array (
        			'question' => "如何使用eBMV中文电子资源?"
        			,'answer'  => '<p>你可以从图书馆网页上的链接进入eBMV中文电子资源网页. 在登录之前你可以浏览和检索目录, 也可以预览每本电子书的开始部分. '
			 			         .'如果你要阅读一本电子书的全部, 或者阅读报纸和杂志, 请先登录. 登录号是你的图书馆卡号和密码.</p>'
				)
			 	,'zh_tw' => array (
        			'question' => "如何使用eBMV中文電子資源? "
        			,'answer'  => '<p>你可以從圖書館網頁上的鏈接進入eBMV中文電子資源網頁. 在登錄之前你可以流覽和檢索目錄, 也可以預覽每本電子書的開始部份.'
			 			         .'如果你要閱讀一本電子書的全部, 或者閱讀報紙和雜誌, 請先登錄. 登錄號是你的圖書館卡號和 密碼.</p>'
				)
			 ),
			 array (
        		'en' => array (
        			'question' => "How can I read Simplified Chinese e-books?"
        			,'answer'   => 'Select the Simplified Chinese e-book of interest in the catalogue, click its cover. The book’s metadata will appear on the new page. There are also two buttons to select: “Read Online” and “Download This Book”. <br />e-book from Founder Apabi can be read on Android tablets or Apple iPad after download.<br />e-book from Xinhua Downloaded e-book requires Microsoft Windows platform to open (Windows XP SP2, Vista, and Widows 7). Downloaded file is a compressed file bundled with reader.  After unzip you will get an executable file (.exe). Double click the file to start reading*. <br /><br />*Note: if Chinese is not selected as the default language in MS Windows, the downloaded file’s name will appear as a string of strange characters. It will not affect you open the file.'
				)
			 	,'zh_cn' => array (
        			'question' => "如何在线阅读简体中文电子书?"
        			,'answer'  => '在目录上选择你要读的简体中文电子书, 点击封面. 在新的网页上有这本书的元数据, 还有两个按钮供选择: “在线阅读” 和 “下载阅读”. 方正阿帕陛的电子书下载后可以在安卓或苹果平板电脑上阅读.<br />新华e店的电子书下载阅读现只可以在运行微软公司视窗操作系统(Windows XP SP2, Vista, Windwons7, Windows8)的桌上, 手提和平板电脑上实现. 下载的文件是一个已包含阅读器的压缩文件 (.zip).  经解压之后得到一个执行文件(.exe).  双击此文件即可开始阅读*.<br /><br />*注: 如果没有在微软视窗中选择中文作为默认语言, 下载文件的文件名会是一串奇怪的字符. 但这并不影响你打开此文件.'
				)
			 	,'zh_tw' => array (
        			'question' => "如何在線閱讀簡體中文電子書?"
        			,'answer'  => '在目錄上選擇你要讀的簡體中文電子書, 點擊封面. 在新的網頁上有這本書的元數據, 還有兩個按鈕供選擇: “在線閱讀” 和“下載閱讀”. 在線閱讀可在運行微軟公司視窗, 蘋果公司OS或安卓操作系統的桌上, 手提或和平板電腦上實現.'
				)
			 ),
			 array (
        		'en' => array (
        			'question' => "What browsers are supported?"
        			,'answer'   => '<table class="table table-bordered"><tr><td>Microsoft</td><td>IE 8 or later</td></tr><tr><td>Apple</td><td>Safari</td></tr><tr><td>Google</td><td>Chrome</td></tr><tr><td>Mozilla</td><td>Firefox</td></tr></table>'
				)
			 	,'zh_cn' => array (
        			'question' => "哪些浏览器, 浏览器版本可以用来在线阅读?"
        			,'answer'  => '<table class="table table-bordered"><tr><td>微软公司</td><td>IE 8 or later</td></tr><tr><td>苹果公司</td><td>Safari</td></tr><tr><td>谷歌</td><td>Chrome</td></tr><tr><td>火狐</td><td>Firefox</td></tr></table>'
				)
			 	,'zh_tw' => array (
        			'question' => "哪些瀏覽器, 瀏覽器版本可以用來在線閱讀?"
        			,'answer'  => '<table class="table table-bordered"><tr><td>微軟公司</td><td>IE 8 or later</td></tr><tr><td>蘋果公司</td><td>Safari</td></tr><tr><td>谷歌</td><td>Chrome</td></tr><tr><td>火狐</td><td>Firefox</td></tr></table>'
				)
			 )
			 ,array (
        		'en' => array (
        			'question' => "How can I download and read Simplified Chinese e-books?"
        			,'answer'   => '<p>Apps for Android tablet and iPad can be found at the following sites:</p><p>The URL for Android app is <a href="http://gw.apabi.com/download/product/ApabiReaderforAndroid/ApabiReaderforAndroid.apk">http://gw.apabi.com/download/product/ApabiReaderforAndroid/ApabiReaderforAndroid.apk</a></p><p>The URL for iOS app is <a href="http://gw.apabi.com/download/product/ApabiReaderforiOS/ApabiReader2.1.4/ApabiReader.2.1.4.ipa">http://gw.apabi.com/download/product/ApabiReaderforiOS/ApabiReader2.1.4/ApabiReader.2.1.4.ipa</a></p><p>You need to install Android and / or iOS apps first, and then you will be able to read e-books on your tablets.You can borrow up to 20 Simplified Chinese e-books. Loan period is two weeks. eBMV will automatically return Simplified Chinese e-books when it’s due.</p><p>Log in to library from the device. Select the Simplified Chinese e-book of interest in the catalogue, click its cover. The book’s metadata will appear on the new page. There are also two buttons to select: “Read Online” and “Download This Book”. Click “Download This Book”. It can be read on Android tablets or Apple iPad after download.</p><p>Click “中华数字书苑”, open mobile Apabi reader<br /><img src="/themes/images/apabi_reader_1.png" /></p><p>Go to the landing page; enter your site code and card number, password<br /><img src="/themes/images/apabi_reader_2.png" /></p><p>Once logged in there is a list of books on bookshelf, click cover page to open<br /><img src="/themes/images/apabi_reader_3.png" /></p>'
				)
			 	,'zh_cn' => array (
        			'question' => "如何下载阅读简体中文电子书?"
        			,'answer'   => '<p>安卓和苹果公司平板电脑的阅读器可从下列网站上下载:</p><p>安卓阅读器的链接是: <a href="http://gw.apabi.com/download/product/ApabiReaderforAndroid/ApabiReaderforAndroid.apk">http://gw.apabi.com/download/product/ApabiReaderforAndroid/ApabiReaderforAndroid.apk</a></p><p>苹果iOS阅读器的链接是: <a href="http://gw.apabi.com/download/product/ApabiReaderforiOS/ApabiReader2.1.4/ApabiReader.2.1.4.ipa">http://gw.apabi.com/download/product/ApabiReaderforiOS/ApabiReader2.1.4/ApabiReader.2.1.4.ipa</a></p><p>你须先安装安卓或苹果iOS阅读器然后才能在平板电脑上看电子书. 你可以最多借20本简体中文电子书, 借阅期限是两个星期. eBMV会自动归还到期的简体中文电子书</p><p>登录到图书馆，在目录上选择你要读的简体中文电子书, 点击封面. 在新的网页上有这本书的元数据, 还有两个按钮供选择: “在线阅读” 和 “下载阅读”. 点击“下载阅读”。下载后可以在安卓或苹果平板电脑上阅读.</p><p>点击“中华数字书苑”，打开手机阿帕比reader</p><p>点击“中华数字书苑”，打开手机阿帕比reader<br /><img src="/themes/images/apabi_reader_1.png" /></p><p>跳转到登录界面，输入您的机构代码和用户名，密码<br /><img src="/themes/images/apabi_reader_2.png" /></p><p>登录后有图书列表，点击封面后可打开<br /><img src="/themes/images/apabi_reader_3.png" /></p>'
				)
			 	,'zh_tw' => array (
        			'question' => "如何下載閱讀簡體中文電子書?"
        			,'answer'   => '<p>安卓和蘋果公司平板電腦的閱讀器可從下列網站上下載:</p><p>安卓閱讀器的鏈接是: <a href="http://gw.apabi.com/download/product/ApabiReaderforAndroid/ApabiReaderforAndroid.apk">http://gw.apabi.com/download/product/ApabiReaderforAndroid/ApabiReaderforAndroid.apk</a></p><p>蘋果iOS閱讀器的鏈接是: <a href="http://gw.apabi.com/download/product/ApabiReaderforiOS/ApabiReader2.1.4/ApabiReader.2.1.4.ipa">http://gw.apabi.com/download/product/ApabiReaderforiOS/ApabiReader2.1.4/ApabiReader.2.1.4.ipa</a></p><p>你須先安裝安卓或蘋果iOS閱讀器然後才能在平板電腦上看電子書. 你可以最多藉20本簡體中文電子書, 借閱期限是兩個星期. eBMV會自動歸還到期的簡體中文電子書</p><p>登錄到圖書館，在目錄上選擇你要讀的簡體中文電子書, 點擊封面.在新的網頁上有這本書的元數據, 還有兩個按鈕供選擇: “在線閱讀” 和“下載閱讀”. 點擊“下載閲讀”。 下載後可以在安卓或蘋果平板電腦上閱讀.</p><p>点击“中华数字书苑”，打开手机阿帕比reader</p><p>點擊“中華數字書苑”，打開手機阿帕比閲讀器<br /><img src="/themes/images/apabi_reader_1.png" /></p><p>跳轉到登陸界面，輸入您的機構代碼和用戶名，密碼<br /><img src="/themes/images/apabi_reader_2.png" /></p><p>登錄后有圖書列表，點擊封面后可打開<br /><img src="/themes/images/apabi_reader_3.png" /></p>'
				)
			 )
			 ,array (
        		'en' => array (
        			'question' => "How can I read Simplified Chinese e-newspapers and e-magazines?"
        			,'answer'   => 'Simplified Chinese e-newspaper, e-magazine and Traditional Chinese e-newspaper service supports online reading. Readers can enter eBMV Chinese e-resource page via the link on library website. You can browse and search before log in. After log in, select interested e-newspaper or e-magazine and read on desktops, laptops and tablets with Microsoft Windows, Apple OS and Android operating system.'
				)
			 	,'zh_cn' => array (
        			'question' => "如何阅读简体中文电子报刊, 电子杂志?"
        			,'answer'  => '简体中文电子报刊, 电子杂志和繁体中文报纸服务支持在线阅读. 读者可以从图书馆网页上的链接进入eBMV中文电子资源网页. 在登录之前你可以浏览和检索目录. 登录后选择中意的电子报刊, 在运行微软公司视窗, 苹果公司OS或安卓操作系统的桌上, 手提和平板电脑上阅读.'
				)
			 	,'zh_tw' => array (
        			'question' => "如何閱讀簡體中文電子報刊, 電子雜誌?"
        			,'answer'  => '簡體中文電子報刊, 電子雜誌和繁體中文雜誌服務支持在綫閱讀. 讀者可以從圖書館網頁上的鏈接進入eBMV中文電子資源網頁. 在登錄之前你可以流覽和檢索目錄. 登錄后選擇中意的電子報刊, 在運行微軟公司視窗, 蘋果公司OS 或安卓操作系統的桌上, 手提和平板電腦上閱讀.'
				)
			 )
			 ,array (
        		'en' => array (
        			'question' => "How can I read Traditional Chinese e-books and e-magazines?"
        			,'answer'   => '<p>Select the traditional Chinese e-book or e-magazine you want to read, click its cover. This publication’s metadata will appear on a new web page, together with a “Borrow” button. After borrow the e-book or e-magazine you can read it online using a desktop, notebook or tablet.</p>'
        						  .'<p>If you’d like to use an Apple or Android mobile device, please go to Apple App Store or Google Play and download<strong>臺灣雲端書庫2014</strong> app.</p>'
        				          .'<p>You can borrow up to 10 Traditional Chinese e-books and e-magazines, loan period is two weeks. eBMV will automatically return Traditional Chinese e-books and e-magazines when it’s due.</p>'
				)
			 	,'zh_cn' => array (
        			'question' => "如果阅读繁体中文电子书和电子杂志?"
        			,'answer'  => '<p>在目录上选择你要阅读的繁体中文电子书和电子杂志, 点击封面. 在新的网页上有此出版物的元数据, 以及一个”借阅”按钮. 借阅后你即可以在PC, NB, 平板电脑上连线阅读.</p>'
			 					 .'<p>如要用苹果或安卓移动终端, 需先到Apple App Store或 Google Play 下載<strong>臺灣雲端書庫2014</strong> app.</p>'
			 					 .'<p>你可以最多借十本中文繁体电子书和电子杂志., 借阅期限是两个星期. eBMV 会自动归还到期的中文繁体电子书和电子杂志.</p>'
				)
			 	,'zh_tw' => array (
        			'question' => "如果閱讀繁體中文電子書和電子雜誌?"
        			,'answer'  => '<p>在目錄上選擇你要閱讀的繁體中文電子書 和電子雜誌, 點擊封面. 在新的網頁上有此出版物的元數據, 以及一個”借閱”按鈕. 借閱后你即可以在PC, NB, 平板電腦 上連線閱讀.</p>'
			 					 .'<p>如要用苹果或安卓移动终端, 需先到Apple App Store或 Google Play 下載<strong>臺灣雲端書庫2014</strong> app.</p>'
			 					 .'<p>你可以最多借十本中文繁體電子書和電子雜誌., 借閱期限是兩個星期. eBMV 會自動歸還到期的中文繁體電子書和電子雜誌.</p>'
				)
			 )
			 ,array (
        		'en' => array (
        			'question' => "How to access ELS and Chinese language courses?"
        			,'answer'  => '<p>From eBMV you can access wide range English language courses from beginners’ to IELTS provided by Xin Dong Fang; '
			 				     .'and various levels of Chinese language courses from Confucius Institute. Currently there are three beginners’ level '
			 			         .'Chinese language courses available as free samples. Please select one of them under “学中文“ by single mouse click. '
			 			         .'The course will start. The list under ESL will display the Xin Dong Fang English courses your library subscribed to.</p>'
				)
			 	,'zh_cn' => array (
        			'question' => "怎样学英语和学中文课程?"
        			,'answer'  => '<p>从eBMV你可以使用由新东方提供的从初级到雅思各种级别英语课程; 以及不同程度的孔子学院中文教程. 现在有三个初学者中文课程样本可供使用.'
			 			         .' 请单击鼠标在”学中文”下面选择一个课程, 就开始上课了. 在“ESL”下面的课程表会列出你的图书馆已采购的新东方英语课程.</p>'
				)
			 	,'zh_tw' => array (
        			'question' => "怎樣學英語和學中文課程?"
        			,'answer'  => '<p>從eBMV你可以使用由新東方提供的從初級到雅思各種級別英語課程; 以及不同程度的孔子學院中文教程. 現在有三個初學者中文課程樣本可供使用.'
			 			         .' 請單擊鼠標在”學中文”下麵選擇一個課程, 就開始上課了. 在“ESL”下麵的課程表會列出你的圖書館採購的新東方的英語課程.</p>'
				)
			 )
        		,array (
        				'en' => array (
        						'question' => "How to read traditional Chinese e-books and e-magazines via iPad and Android apps?"
        						,'answer'   => '<p>iPad and Android apps are available for traditional Chinese e-books and e-magazines. To download the apps please go to these sites:'
        						.'<br/>iPad app'
        						.'<br/><a href:"https://itunes.apple.com/tw/app/tai-wan-yun-duan-shu-ku/id675068310?l=zh&mt=8">https://itunes.apple.com/tw/app/tai-wan-yun-duan-shu-ku/id675068310?l=zh&mt=8</a>'
        						.'<br/>Android app'
        						.'<br/><a href:"https://play.google.com/store/apps/details?id=tw.ebookservice.voler">https://play.google.com/store/apps/details?id=tw.ebookservice.voler</a>'
        						.'<br/>Once you have downloaded and installed the apps, the steps to access contents using either of the app are the same.'
        						.'<br/>1) Click to launch the app'
        						.'<br/>2) Click the menu icon at top left corner highlighted in red'
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_01.jpg">'
        						.'<br/>3) A side bar appears displaying a list of libraries as in image shown. Click the button down the bottom highlighted in red'
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_02.jpg">'
        						."<br/>4) Enter your library's code as Picture 3. Please use:"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;<b>VHEI</b> for Yarra Plenty Regional Library"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;<b>VHOB</b> for Moreland City Library Services"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;<b>VCML</b> for City of Melbourne Library Services"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;<b>VHOB</b> for Hobsons Bay Library Services"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;<b>WML</b> for Whitehorse Manningham Regional Library Corporation "
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_03.jpg">'
        						.'<br/>5) Select your library and log in, you can now access e-books and e-magazines in traditional Chinese.'
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_04.jpg">'
        						.'</p>'
        				)
        				,'zh_cn' => array (
        						'question' => "怎樣用iPad和安卓應用閱讀繁體中文電子書和電子雜誌?"
        						,'answer'   => '<p>iPad和安卓应用可用来阅读繁体中文电子书和电子杂志. 请去以下网站下载这两个应用:'
        						.'<br/>iPad 应用'
        						.'<br/><a href:"https://itunes.apple.com/tw/app/tai-wan-yun-duan-shu-ku/id675068310?l=zh&mt=8">https://itunes.apple.com/tw/app/tai-wan-yun-duan-shu-ku/id675068310?l=zh&mt=8</a>'
        						.'<br/>安卓应用'
        						.'<br/><a href:"https://play.google.com/store/apps/details?id=tw.ebookservice.voler">https://play.google.com/store/apps/details?id=tw.ebookservice.voler</a>'
        						.'<br/>一旦你下载, 安装了应用, 从iPad或安卓平板电脑上阅读电子资源的步骤是相同的.'
        						.'<br/>1) 点击打开应用'
        						.'<br/>2) 点击左上角的目录键 (在图中有红色记号处) '
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_01.jpg">'
        						.'<br/>3) 屏幕左边会出现一个图书馆名单, 如图二所示. 点击屏幕下方的按钮(在图中已经用红色方框指出) '
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_02.jpg">'
        						."<br/>4) 键入你的图书馆代号, 如图所示"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;Yarra Plenty Regional Library 的读者请键入<b>VHEI</b>"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;Moreland City Library Services 的读者请键入<b>VHOB</b>"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;Melbourne City Library 的读者请键入 <b>VCML</b>"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;Hobsons Bay Library Services 的读者请键入<b>VHOB</b>"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;Whitehorse Manningham Regional Library 的读者请键入<b>WML</b>"
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_03.jpg">'
        						.'<br/>5) 选择你的图书馆, 登录. 你就可以在iPad 或 安卓平板电脑上阅读繁体中文电子书和电子杂志了'
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_04.jpg">'
        						.'</p>'
        				)
        				,'zh_tw' => array (
        						'question' => "怎样用iPad和安卓应用阅读繁体中文电子书和电子杂志"
        						,'answer'   => '<p>iPad和安卓應用可以用來閱讀繁體中文電子書和電子雜誌. 請去以下網站下載這兩個應用:'
        						.'<br/>iPad app'
        						.'<br/><a href:"https://itunes.apple.com/tw/app/tai-wan-yun-duan-shu-ku/id675068310?l=zh&mt=8">https://itunes.apple.com/tw/app/tai-wan-yun-duan-shu-ku/id675068310?l=zh&mt=8</a>'
        						.'<br/>Android app'
        						.'<br/><a href:"https://play.google.com/store/apps/details?id=tw.ebookservice.voler">https://play.google.com/store/apps/details?id=tw.ebookservice.voler</a>'
        						.'<br/>一旦你下載, 安裝了應用, 從iPad或安卓平板電腦上閱讀電子資源的步驟是相同的.'
        						.'<br/>1) 點擊打開應用'
        						.'<br/>2) 點擊左上角的目錄鍵(在圖中有紅色記號處) '
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_01.jpg">'
        						.'<br/>3) 屏幕左邊會出現一個圖書館名單, 如圖二所示. 點擊屏幕下方的按鈕 (在圖中已經用紅色方框指出) '
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_02.jpg">'
        						."<br/>4) 鍵入你的圖書館代號, 如圖所示"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;Yarra Plenty Regional Library 的讀者請鍵入<b>VHEI</b>"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;Moreland City Library Services 的讀者請鍵入<b>VHOB</b>"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;Melbourne City Library 的讀者請鍵入 <b>VCML</b>"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;Hobsons Bay Library Services 的讀者請鍵入<b>VHOB</b>"
        						."<br/>&nbsp;&nbsp;&nbsp;&nbsp;Whitehorse Manningham Regional Library 的讀者請鍵入<b>WML</b>"
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_03.jpg">'
        						.'<br/>5) 選擇你的圖書館, 登錄. 你就可以在iPad或安卓平板電腦上閱讀繁體中文電子書和電子雜誌了.'
        						.'<br/><img class="img-responsive" src="/themes/fairfield/images/yuanhang_help_zh_tw_04.jpg">'
        						.'</p>'
        				)
        		)
        );
        return json_encode($array);
    }
}
