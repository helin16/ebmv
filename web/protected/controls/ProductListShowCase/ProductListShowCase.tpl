<com:TCallback ID="fetchProductBtn" OnCallback="fetchProducts" />
<com:Application.controls.JCarousel.JCarouselJs />
<div ID="<%= $this->getClientID()%>"
	class="panel panel-default nodefault">
	<div class="panel-heading">
	  <div class="row">
	      <div class="col-xs-10">
	          <%=$this->getTitle() %>
	      </div>
	      <div class="col-xs-2 navbar-right">
	          <div class="hidden-sm hidden-xs">
                <ul class="nav nav-tabs langlist">
                    <li class="langitem active" langid=''><a href="javascript: void(0);">All</a></li>
                    <li class="langitem" langid='1'><a href="javascript: void(0);">简体</a></li>
                    <li class="langitem" langid='2'><a href="javascript: void(0);">繁體</a></li>
                </ul>
            </div>
            <div class="dropdown visible-sm visible-xs">
                <button class="btn dropdown-toggle" type="button" id="<%= $this->getClientID()%>_langDropdown" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="<%= $this->getClientID()%>_langDropdown">
                    <li role="presentation" class="langitem" langid=''><a role="menuitem" href="javascript: void(0);">All</a></li>
                    <li role="presentation" class="langitem" langid='1'><a role="menuitem" href="javascript: void(0);">简体</a></li>
                    <li role="presentation" class="langitem" langid='2'><a role="menuitem" href="javascript: void(0);">繁體</a></li>
                </ul>
            </div>
	      </div>
	  </div>
	</div>
	<div class="panel-body">
		<div class="list"></div>
	</div>
</div>