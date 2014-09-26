<div role="navigation" class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="/libadmin/" class="navbar-brand" title="Dashbaord for library admin of <%= Core::getLibrary()->getName() %>">Dashboard</a>
    </div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <%=$this->getMenu() %>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>