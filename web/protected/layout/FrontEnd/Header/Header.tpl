<div class="systemtitle">
	<span class="inlineblock headleft">
		<a class="arrow" href="/" >
			<span class="logo"></span>
			<span class="libname"><%= Core::getLibrary()->getName() %></span>
		</a>
	</span>
	<span class="inlineblock headright">
		<a href="/user.html"><%= Core::getUser() instanceof UserAccount ? 'Welcome, ' . Core::getUser()->getPerson() : '登录/登錄' %></a>
	</span>
	<span class="inlineblock headright">
		<a href="/help.html">
		<span>帮助/幫助</span>
		<span class="help"></help>
		</a>
	</span>

</div>

<ul class="menuH decor1">
 <li><a class="arrow" href="/" >
 <span>首页/首頁</span>
 <span class="home"></span>
 </a></li> 

 <li><a class="arrow" href="/user.html">
 <span>我的书架/我的書架</span>
 <span class="shelf"></span>
 </a></li> 
 <li><a>&nbsp;|&nbsp;</a></li>

 <li><a>简体中文</a>
<ul>
<li><a href="/products/1/1">书</a> </li>

<li><a href="/products/1/3">杂志</a> </li>
<li><a href="/products/1/2">报纸</a></li> 
</ul> </li> 

 <li><a>繁體中文</a>
<ul>
<li><a href="/products/2/1">書</a> </li>

<li><a href="/products/2/3">雜誌</li>
<li><a href="/products/2/2">報紙</a></li> 
</ul> </li> 
</ul>

