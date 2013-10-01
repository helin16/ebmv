<!DOCTYPE html>
<html lang="en">
<com:THead ID="titleHeader" Title="<%$ AppTitle %>">
    <meta charset="UTF-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="description" content="">
	<meta name="keywords" content="">
</com:THead>
<body>
    <com:TForm>
	    <div id="frontend">
	        <div class="framewrapper header">
	            <div class="contentwrapper">
		            <com:Application.layout.FrontEnd.Header.Header ID="FrontEndHeader" />
	            </div>
	        </div>
	        <div class="framewrapper menu">
	            <div class="contentwrapper">
		            <com:Application.layout.FrontEnd.Menu.Menu ID="FrontEndMenu" />
	            </div>
	        </div>
	        <div class="framewrapper content">
	            <div class="contentwrapper">
	               <com:TContentPlaceHolder ID="MainContent" />
	            </div>
	        </div>
	        <div class="framewrapper footer">
	            <div class="contentwrapper">
	                <com:Application.layout.FrontEnd.Footer.Footer ID="FrontEndFooter" />
	            </div>
	        </div>
	    </div>
    </com:TForm>
</body>
</html>