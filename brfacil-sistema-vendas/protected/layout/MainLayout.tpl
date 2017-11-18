<!DOCTYPE html>
<html >
	<com:THead Title="SFacil" >
	<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	     <!-- JQUERY -->
	 <link href="themes/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" media="screen">
     <script src="themes/bootstrap/js/bootstrap.min.js"></script>
     <script src="themes/bootstrap/js/jquery-latest.js"></script> 
     
	 
	 <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script src="themes/Chart/Chart.js"></script>
	 
 </com:THead>	       
	<body>	  
    <!-- Cabecalho -->
<com:TForm>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
            <span class="sr-only">Toggle navigation</span>
          <a class="navbar-brand" href="<%= $this->Service->constructUrl('Home')%>">
          <com:TActiveLabel id="logo" />
          </a>
        </div>
        <div class="navbar-collapse collapse ">
          <ul class="nav navbar-nav navbar-right  ">
            <li><a href="<%= $this->Service->constructUrl('Dashboard')%>">Dashboard</a></li>
            <li>
	            <com:THyperLink Text="Entrar" NavigateUrl="<%= $this->Service->constructUrl('Login') %>"  Visible="<%= $this->User->IsGuest %>" id="login"/> 
	            <com:TLinkButton  Text=" Sair " OnClick="logoutButtonClicked"  Visible="<%= !$this->User->IsGuest %>" CausesValidation="false" id="logoff" />
            </li>
          </ul>
        </div>
      </div>
    </div>    
    <div class="container-fluid">
      <div class="row ">
        <div class="col-sm-9 col-md-2 sidebar navbar-defautl">          
          <ul class="nav nav-pills nav-stacked ">
          </br>
          </br>
          </br>
          <com:TRepeater ID="Repeater" EnableViewState="false">
          <prop:ItemTemplate>
            <li class="<%= str_replace($this->Data->pagina,'active',$this->Page->PagePath)  %>"><a href="<%= $this->Service->constructUrl($this->Data->pagina)%>"><%#$this->Data->descricao%></a></li>
          </prop:ItemTemplate>
          </com:TRepeater>
          </ul>
        </div>
        </br>
        </br>
        <div class="col-md-offset-2 main">
        </br>
        <ol class="breadcrumb">
         <li><a href="<%= $this->Service->constructUrl('Home')%>">Home</a></li>
        <li class="active"><%= $this->Page->PagePath  %></li>
        </ol>
	      <div role="form">
	   		  <div class="jumbotron">
						<com:TContentPlaceHolder ID="Main" />
				  </div>		
      	 </div>
      		</com:TForm>
</div>
			</body>
</html>

