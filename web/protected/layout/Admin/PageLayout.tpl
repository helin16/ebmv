<!DOCTYPE html>
<html lang="en">
<com:THead ID="titleHeader" Title="<%$ AppTitle %>">
    <meta charset="UTF-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	
	<!-- Optional theme -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	
	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</com:THead>
<body>
<com:TForm>
    <div class="admin">
        <div class="menu">
	        <div class="contentwrapper">
                <com:Application.layout.Admin.Menu.Menu />
	        </div>
        </div>
        <div class="pagecontent">
	        <div class="contentwrapper">
                <com:TContentPlaceHolder ID="MainContent" />
	        </div>
        </div>
    </div>
</com:TForm>
</body>
</html>