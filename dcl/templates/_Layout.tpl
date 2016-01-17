<!doctype html>
<html class="no-js" lang="">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>{block name=title}{/block} | {dcl_config name='DCL_HTML_TITLE'|escape}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}jqueryui/jquery-ui.min.css" media="screen">
	<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}bootstrap/css/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}gritter/css/jquery.gritter.css" media="screen">
	<link rel="stylesheet" type="text/css" href="{$DIR_CSS}default.css" />
	{block name=css}{/block}
</head>
<body>
<nav id="dcl-main-nav-bar" class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
	{dcl_menu}
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{$Menu->LinkHome}">{dcl_config name='DCL_APP_NAME'|escape}</a>
		</div>
		<div class="collapse navbar-collapse" id="main-navbar-collapse">
			{$MenuList}
			<div class="pull-right">
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown"><a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">{dcl_gravatar userId=$Menu->DclId size=24 class="img-rounded"} {$Menu->DclName|escape} <b class="caret"></b></a>
						<ul class="dropdown-menu">
							{if $Menu->CanModifyPreferences}<li><a href="{$Menu->LinkPreferences}"><i class="icon-cog"></i> {$Menu->TextPreferences|escape}</a></li>{/if}
							<li class="divider"></li>
							<li><a href="{$Menu->LinkLogoff}"><i class="icon-off"></i> {$Menu->TextLogoff|escape}</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</nav>
<div style="clear: both;"></div>
<div class="row-offcanvas row-offcanvas-left">
	<div id="sidebar" class="sidebar-offcanvas">
		<div class="col-md-12">
			{if $Menu->CanViewWorkspaces}<h5>Workspace</h5><div class="form-group">{dcl_select_workspace default=$Menu->Workspace class="form-control input-sm"}</div>{/if}
			{if $Menu->CanViewWorkOrders || $Menu->CanViewTickets || $Menu->CanViewProjects}
				<h5>Search</h5>
				<form id="sidebar-search-form" role="search" method="POST" action="main.php">
					<input type="hidden" name="menuAction" value="htmlSearchBox.submitSearch">
					<div class="form-group">
						<select class="form-control input-sm" name="which">
							{if $Menu->CanViewWorkOrders}<option value="openworkorders">Open {$Menu->TextWorkOrders}</option><option value="workorders">All {$Menu->TextWorkOrders}</option>{/if}
							{if $Menu->CanViewProjects}<option value="opendcl_projects">Open {$Menu->TextProjects}</option><option value="dcl_projects">All {$Menu->TextProjects}</option>{/if}
							{if $Menu->CanViewTickets}<option value="opentickets">Open {$Menu->TextTickets}</option><option value="tickets">All {$Menu->TextTickets}</option>{/if}
							{if $Menu->CanViewWorkOrders || $Menu->CanViewTickets}<option value="tags">Tags</option>{/if}
							{if $Menu->CanViewHotlists}<option value="hotlists">Hotlists</option>{/if}
						</select>
					</div>
					<div class="input-group input-group-sm">
						<input class="form-control" type="text" name="search_text" id="sidebar-search-text">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" id="sidebar-search-form-btn"><span class="glyphicon glyphicon-search"></span></button>
					</span>
					</div>
					{if $Menu->CanViewWorkOrders}
						<h5>Work Order Views</h5>
						{dcl_select_views name="wo-views" public=false}
					{/if}
					{if $Menu->CanViewTickets}
						<h5>Ticket Views</h5>
						{dcl_select_views name="tck-views" public=false table=tickets}
					{/if}
				</form>
			{/if}
		</div>
	</div>
	<div id="content"><div class="col-md-12">
		<p class="visible-xs"><button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button></p>
		{dcl_flash}{dcl_notifications}
{block name=content}{/block}
	</div></div></div>
<script type="text/javascript" src="{$DIR_VENDOR}jquery/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}jquery/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}jqueryui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}modernizr/modernizr-2.8.2.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}gritter/jquery.gritter.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	var $notification = $("div.dcl-notification");
	if ($notification.length > 0) {
		$.gritter.add({
			title: $notification.attr("title"),
			text: $notification.text()
		});
	}
	$('[data-toggle=offcanvas]').click(function() {
		$('.row-offcanvas').toggleClass('active');
	});
	$("#sidebar-search-form-btn").click(function() {
		if ($.trim($("#sidebar-search-text").val()) == "") {
			alert("Please enter search criteria.");
			return;
		}
		$("#sidebar-search-form").submit();
	});
	$("#wo-views,#tck-views").change(function() {
		location.href = "{$URL_MAIN_PHP}?menuAction=boViews.exec&viewid=" + parseInt($(this).val(), 10);
	});
	{if $Menu->CanViewWorkspaces}
	$("#workspace_id").change(function() {
		location.href = "{$URL_MAIN_PHP}?menuAction=htmlWorkspaceForm.changeWorkspace&workspace_id=" + parseInt($(this).val(), 10);
	});
	{/if}
});
</script>
{block name=script}{/block}
</body>
</html>