<com:TCallback ID="fetchProductBtn" OnCallback="fetchProducts" />
<com:Application.controls.JCarousel.JCarouselJs />
<div ID="<%= $this->getClientID()%>" class="panel panel-default nodefault">
	<div class="panel-heading">
		<div class="navbar-header">
			<button class="dropdown-toggle" data-target="#<%= $this->getClientID()%>_bar"	data-toggle="dropdown" type="button">
				<span class="caret"></span>
			</button>
			<span class="navbar-brand title" href="#"><%= $this->getTitle() %></span>
		</div>
		<div class="collapse navbar-collapse" id="<%= $this->getClientID()%>_bar" role="menu">
			<ul class="nav nav-tabs navbar-right langlist" aria-labelledby="dropdownMenu1">
				<li class="active" langid=''><a href="javascript: void(0);">All</a></li>
				<li langid='1'><a href="javascript: void(0);">简体</a></li>
				<li langid='2'><a href="javascript: void(0);">繁體</a></li>
			</ul>
		</div>
	</div>
	<div class="panel-body">
		<div class="list"></div>
	</div>
</div>