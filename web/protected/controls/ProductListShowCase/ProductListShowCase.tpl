<com:TCallback ID="fetchProductBtn" OnCallback="fetchProducts" />
<com:Application.controls.JCarousel.JCarouselJs />
<div ID="<%= $this->getClientID()%>" class="ProductListShowCaseWrapper sectionWrapper">
	<div class="title">
		<%= $this->getTitle() %>
		<span class="inlineblock langchoser">
			<ul class="langlist">
				<li class="langitem cursorpntr active" langid=''>All</li>
				<li class="langitem cursorpntr" langid='1'>简体</li>
				<li class="langitem cursorpntr" langid='2'>繁體</li>
			</ul>
		</span>
	</div>
	<div class="list"></div>
</div>