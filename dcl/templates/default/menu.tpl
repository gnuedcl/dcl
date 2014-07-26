<link rel="stylesheet" type="text/css" href="{$DIR_JS}/jqueryui/dcl/jquery-ui-1.8.21.custom.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$DIR_JS}/gritter/css/jquery.gritter.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$DIR_CSS}/css3buttons.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}bootstrap/css/bootstrap-theme.min.css">
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/jquery-1.7.1.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/jquery-ui-1.8.17.custom.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_VENDOR}modernizr/modernizr-2.8.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/gritter/jquery.gritter.min.js"></script>
<script src="{$DIR_VENDOR}bootstrap/js/bootstrap.min.js"></script>
<nav id="dcl-main-nav-bar" class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{$LNK_HOME}">{dcl_config name='DCL_APP_NAME'|escape}</a>
		</div>
		<div class="collapse navbar-collapse" id="main-navbar-collapse">
			{dcl_menu menu=$VAL_DCL_MENU}
			<div class="pull-right">
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown"><a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">{dcl_gravatar userId=$DCLID size=24 class="img-rounded"} {$DCLNAME|escape} <b class="caret"></b></a>
						<ul class="dropdown-menu">
							{if $PERM_PREFS}<li><a href="{$LNK_PREFERENCES}"><i class="icon-cog"></i> {$TXT_PREFERENCES|escape}</a></li>{/if}
							<li class="divider"></li>
							<li><a href="{$LNK_LOGOFF}"><i class="icon-off"></i> {$TXT_LOGOFF|escape}</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</nav>
<div style="clear: both;"></div>
<script type="text/javascript">
//<![CDATA[
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
{if $PERM_WORKSPACE}$("#workspace_id").change(function() { location.href = "{$URL_MAIN_PHP}?menuAction=htmlWorkspaceForm.changeWorkspace&workspace_id=" + $(this).val(); });{/if}
});
//]]>
</script>
<div class="row-offcanvas row-offcanvas-left">
<div id="sidebar" class="sidebar-offcanvas">
	<div class="col-md-12">
		{if $PERM_WORKSPACE}<h5>Workspace</h5><div class="form-group">{dcl_select_workspace default=$VAL_WORKSPACE class="form-control input-sm"}</div>{/if}
		{if $PERM_WORKORDERSEARCH || $PERM_TICKETSEARCH || $PERM_PROJECTSEARCH}
			<h5>Search</h5>
			<form id="sidebar-search-form" role="search" method="POST" action="main.php">
				<input type="hidden" name="menuAction" value="htmlSearchBox.submitSearch">
				<div class="form-group">
					<select class="form-control input-sm" name="which">
						{if $PERM_WORKORDERSEARCH}<option value="openworkorders">Open {$TXT_WORKORDERS}</option><option value="workorders">All {$TXT_WORKORDERS}</option>{/if}
						{if $PERM_PROJECTSEARCH}<option value="opendcl_projects">Open {$TXT_PROJECTS}</option><option value="dcl_projects">All {$TXT_PROJECTS}</option>{/if}
						{if $PERM_TICKETSEARCH}<option value="opentickets">Open {$TXT_TICKETS}</option><option value="tickets">All {$TXT_TICKETS}</option>{/if}
						{if $PERM_WORKORDERSEARCH || $PERM_TICKETSEARCH}<option value="tags">Tags</option>{/if}
						{if $PERM_HOTLISTVIEW}<option value="hotlists">Hotlists</option>{/if}
					</select>
				</div>
				<div class="input-group input-group-sm">
					<input class="form-control" type="text" name="search_text" id="sidebar-search-text">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button" id="sidebar-search-form-btn"><span class="glyphicon glyphicon-search"></span></button>
					</span>
				</div>
			</form>
		{/if}
	</div>
</div>
<div id="content"><div class="col-md-12">
<p class="visible-xs">
	<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
</p>
