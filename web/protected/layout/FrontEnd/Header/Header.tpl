<com:Application.controls.FancyBox.FancyBox />
<nav class="navbar navbar-default navbar-static-top" role="navigation">
	<div class="container">
		<div class="row">
			<div class="col-sm-8">
				<div class="media">
				  <a class="pull-left logo" href="/">
				    <img class="media-object" src="/themes/<%= $this->getPage()->getTheme()->getName() %>/images/logo.png" alt="<%= Core::getLibrary()->getName() %>">
				  </a>
				  <div class="media-body title">
				    <h4 ><%= Core::getLibrary()->getName() %></h4>
				  </div>
				</div>
			</div>
			<div class="col-sm-4 hidden-xs topmenu">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="/help.html" class="iconbtn"><div class="btnname">帮助/幫助<small>Help</small></div><span class="glyphicon glyphicon-question-sign"></span></a></li>
					<li>
						<a href="/user.html" class="iconbtn">
							<%= Core::getUser() instanceof UserAccount ?
								'<div class="btnname">Welcome<small>' . Core::getUser()->getPerson() . '</small></div>'
								:
								'<div class="btnname">登录/登錄<small>Login</small></div>'
							%>
							<span class="glyphicon glyphicon-user">
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="container mainmenu">
		<div class="navbar-header">
			<button class="navbar-toggle" data-target="#topmenulist" data-toggle="collapse" type="button">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="topmenulist">
			<ul class="nav navbar-nav">
				<li class='hidden-sm'>
				    <a href="/" class="iconbtn "><div class="btnname">首页/首頁<small>Home</small></div><span class="glyphicon glyphicon-home"></span></a>
				</li>
				<li><a href="/user.html" class="iconbtn"><div class="btnname">我的书架/我的書架<small>My Bookshelf</small></div><span class="glyphicon glyphicon-signal"></span></a></li>
				<li class="hidden-xs"><a> | </a></li>
				<%= $this->getMenuList(Language::get(Language::ID_SIMPLIFIED_CHINESE), Core::getLibrary()) %>
				<%= $this->getMenuList(Language::get(Language::ID_TRADITIONAL_CHINESE), Core::getLibrary()) %>
				<%= $this->getMenuList('', Core::getLibrary(), array('CN'=> '学英语', 'EN'=> 'ESL'), array(Product::get(62), Product::get(61))) %>
				<%= $this->getMenuList('', Core::getLibrary(), array('CN'=> '学汉语', 'EN'=> 'Learn Chinese'),  Supplier::get(Supplier::ID_CIO)->getProducts(array(), array(ProductType::ID_COURSE))) %>
				<li class="visible-xs"><a href="/help.html" class="iconbtn"><div class="btnname">帮助/幫助<small>Help</small></div><span class=" glyphicon glyphicon-question-sign"></span></a></li>
				<li class="visible-xs"><a href="/user.html" class="iconbtn"><div class="btnname">登录/登錄<small>Login</small></div></a></li>
			</ul>
		</div>
	</div>
</nav>


