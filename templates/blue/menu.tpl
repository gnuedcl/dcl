<!-- $Id: menu.tpl,v 1.1.1.1 2006/11/27 05:30:35 mdean Exp $ -->
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/layersmenu-browser_detection.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/layersmenu-library.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/layersmenu.js"></script>
{$JS_INIT_DCL_MENU}
<div id="header">
	<div id="headerleft"><div id="apptitle">{dcl_config name='DCL_APP_NAME'|escape}</div></div>
	<div id="headerright">
		<div id="search">
			{if $PERM_WORKORDERSEARCH || $PERM_TICKETSEARCH || $PERM_PROJECTSEARCH}
			<form method="POST" action="main.php">
				<input type="hidden" name="menuAction" value="htmlSearchBox.submitSearch">
				<select name="which">
					{if $PERM_WORKORDERSEARCH}<option value="workorders">{$TXT_WORKORDERS}</option>{/if}
					{if $PERM_PROJECTSEARCH}<option value="dcl_projects">{$TXT_PROJECTS}</option>{/if}
					{if $PERM_TICKETSEARCH}<option value="tickets">{$TXT_TICKETS}</option>{/if}
				</select>&nbsp;
				<input type="text" name="search_text" size="20">&nbsp;
				<input type="submit" value="Search">
			</form>
			{/if}
		</div>
		<div id="quicknav">
			<a href="{$LNK_HOME}">{$TXT_HOME}</a>&nbsp;|&nbsp;{if $PERM_PREFS}<a href="{$LNK_PREFERENCES}">{$TXT_PREFERENCES}</a>&nbsp;|&nbsp;{/if}<a href="{$LNK_LOGOFF}">{$TXT_LOGOFF}</a>
		</div>
	</div>
</div>
{$VAL_DCL_MENU}
<div id="left">{$NAV_BOXEN}</div>
<div id="content"><div style="width:100%;">
