<nav class="navbar navbar-default navbar-static-top" role="navigation">
	<div class="container">
		<div class="row">
			<div class="col-sm-8">
				<div class="media">
				  <a class="pull-left" href="/">
				    <img class="media-object" src="/themes/<%= $this->getPage()->getTheme()->getName() %>/images/logo.png" alt="<%= Core::getLibrary()->getName() %>">
				  </a>
				  <div class="media-body">
				    <h4 ><%= Core::getLibrary()->getName() %></h4>
				  </div>
				</div>
			</div>
			<div class="col-sm-4">
				<ul class="nav navbar-nav">
					<li><a href="/">帮助/幫助 <span class=" glyphicon glyphicon-question-sign"></span></a></li>
					<li><a href="/user.html">登录/登錄</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="container topmenu">
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
				<li class="active"><a href="/">首页/首頁<small>Home</small> <span class="glyphicon glyphicon-home"></span></a></li>
				<li><a href="/user.html">我的书架/我的書架 <span class="glyphicon glyphicon-signal"></span></a></li>
				<li><a> | </a></li>
				<li><a>Transactions</a></li>
				<li><a>Properties</a></li>
			</ul>
		</div>
	</div>
</nav>


