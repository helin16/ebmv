<com:TCallback ID="fetchProductBtn" OnCallback="fetchProducts" />
<com:Application.controls.JCarousel.JCarouselJs />
<div ID="<%= $this->getClientID()%>"
	class="panel panel-default nodefault">
	<div class="panel-heading">
	  <div class="row">
	      <div class="col-xs-10 title">
	          <%=$this->getTitle() %>
	      </div>
	      <div class="col-xs-2 rightbtns">
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
	<div class="panel-body container-fluid">
	   <div class="row">
			<div class="col-md-2 col-sm-4 col-xs-6">
				<div href="#" class="thumbnail nodefault"> 
				    <a href="#">
				        <img data-src="holder.js/100%x180" alt="..." src="https://ebmv.com.au/asset/get?id=c6ca6dc99fab39eb5bb4c61f425cb1b4">
				    </a>
				    <div class="caption">
						test test test test test test test test test test test test 
					</div>
				</div>
			</div>
			<div class="col-md-2 col-sm-4 col-xs-6">
				<div href="#" class="thumbnail"> 
				    <a href="#">
				        <img data-src="holder.js/100%x180" alt="..." src="https://ebmv.com.au/asset/get?id=c6ca6dc99fab39eb5bb4c61f425cb1b4">
				    </a>
				    <div class="caption">
						<p>test test test test test test test test test test test test </p>
					</div>
				</div>
			</div>
			<div class="col-md-2 col-sm-4 col-xs-6">
				<div href="#" class="thumbnail"> 
				    <a href="#">
				        <img data-src="holder.js/100%x180" alt="..." src="https://ebmv.com.au/asset/get?id=c6ca6dc99fab39eb5bb4c61f425cb1b4">
				    </a>
				    <div class="caption">
						<p>test test test test test test test test test test test test </p>
					</div>
				</div>
			</div>
			<div class="col-md-2 col-sm-4 col-xs-6">
				<div href="#" class="thumbnail"> 
				    <a href="#">
				        <img data-src="holder.js/100%x180" alt="..." src="https://ebmv.com.au/asset/get?id=c6ca6dc99fab39eb5bb4c61f425cb1b4">
				    </a>
				    <div class="caption">
						<p>test test test test test test test test test test test test </p>
					</div>
				</div>
			</div>
			<div class="col-md-2 col-sm-4 col-xs-6">
				<div href="#" class="thumbnail"> 
				    <a href="#">
				        <img data-src="holder.js/100%x180" alt="..." src="https://ebmv.com.au/asset/get?id=c6ca6dc99fab39eb5bb4c61f425cb1b4">
				    </a>
				    <div class="caption">
						<p>testtesttesttesttesttesttesttest test test test test </p>
					</div>
				</div>
			</div>
			<div class="col-md-2 col-sm-4 col-xs-6">
				<div href="#" class="thumbnail"> 
				    <a href="#">
				        <img data-src="holder.js/100%x180" alt="..." src="https://ebmv.com.au/asset/get?id=c6ca6dc99fab39eb5bb4c61f425cb1b4">
				    </a>
				    <div class="caption">
						<p>test test test test test test test test test test test test </p>
					</div>
				</div>
			</div>
		</div>
		<div class="list"></div>
	</div>
</div>