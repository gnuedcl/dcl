<!-- $Id$ -->
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/layersmenu-browser_detection.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/layersmenu-library.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/layersmenu.js"></script>
{$JS_INIT_DCL_MENU}
{if $PERM_WORKSPACE}<script language="JavaScript" type="text/javascript">
function changeWorkspace()
{literal}{{/literal}
	var o = document.getElementById("workspace_id");
	if (o)
		location.href = "{$URL_MAIN_PHP}?menuAction=htmlWorkspaceForm.changeWorkspace&workspace_id=" + o.options[o.options.selectedIndex].value;
{literal}}{/literal}
</script>{/if}
<div id="header">
	<div id="headerleft"><div id="apptitle">{dcl_config name='DCL_APP_NAME'|escape}</div></div>
	<div id="headerright">
		<div id="search">
			{if $PERM_WORKORDERSEARCH || $PERM_TICKETSEARCH || $PERM_PROJECTSEARCH}
			<form method="POST" action="main.php">
				<input type="hidden" name="menuAction" value="htmlSearchBox.submitSearch">
				<select name="which">
					{if $PERM_WORKORDERSEARCH}<option value="openworkorders">Open {$TXT_WORKORDERS}</option>
					<option value="workorders">All {$TXT_WORKORDERS}</option>{/if}
					{if $PERM_PROJECTSEARCH}<option value="opendcl_projects">Open {$TXT_PROJECTS}</option>
					<option value="dcl_projects">All {$TXT_PROJECTS}</option>{/if}
					{if $PERM_TICKETSEARCH}<option value="opentickets">Open {$TXT_TICKETS}</option>
					<option value="tickets">All {$TXT_TICKETS}</option>{/if}
					{if $PERM_WORKORDERSEARCH || $PERM_TICKETSEARCH}<option value="tags">Tags</option>{/if}
				</select>&nbsp;
				<input type="text" name="search_text" size="20">&nbsp;
				<input type="submit" value="Search">
			</form>
			{/if}
		</div>
		<div id="quicknav">
			<a href="{$LNK_HOME}">{$TXT_HOME}</a>&nbsp;|&nbsp;{if $PERM_PREFS}<a href="{$LNK_PREFERENCES}">{$TXT_PREFERENCES}</a>&nbsp;|&nbsp;{/if}{if $PERM_WORKSPACE}{dcl_select_workspace onchange="changeWorkspace();" default=$VAL_WORKSPACE}&nbsp;|&nbsp;{/if}<a href="{$LNK_LOGOFF}">{$TXT_LOGOFF}</a>
		</div>
	</div>
</div>
{$VAL_DCL_MENU}
<div id="left">{$NAV_BOXEN}</div>
<div id="content"><div style="width:100%;">
