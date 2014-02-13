<div class="systemtitle">
	<span class="inlineblock headleft">
		<a class="arrow" href="/" >
			<span class="logo"></span>
			<span class="libname"><%= Core::getLibrary()->getName() %></span>
		</a>
	</span>
	<span class="inlineblock headright">
		<a href="/login.html">登录/登錄</a>
	</span>
	<span class="inlineblock headright">
		<a href="/help.html">Help</a>
	</span>

</div>

<ul class="menuH decor1">
 <li><a class="arrow" href="/" >首页/首頁</a></li> 

 <li><a class="arrow" href="/user.html">我的书架/我的書架</a></li> 
 <li><a>&nbsp;|&nbsp;</a></li>

 <li><a>简体中文</a>
<ul>
<li><a href="/products/1/1">书</a> </li>

<li><a href="/products/1/2">杂志</a> </li>
<li><a href="/products/1/3">报纸</a></li> 
</ul> </li> 

 <li><a>繁體中文</a>
<ul>
<li><a href="/products/2/1">書</a> </li>

<li><a href="/products/2/2">雜誌</li>
<li><a href="/products/2/3">報紙</a></li> 
</ul> </li> 
<span>
<input id="searchtxt" type="textbox" class="search" placeholder="搜寻/搜尋"/>
</span>
<span>
<input id="searchbtn" type="button" class="searchimg"  OnClick="window.location='/products/search/' + $F('searchtxt');"/>
</span>
</ul>

