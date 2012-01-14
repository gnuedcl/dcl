<!-- $Id$ -->
<link rel="stylesheet" type="text/css" href="{$DIR_JS}/jqueryui/overcast/jquery-ui-1.8.2.custom.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$DIR_JS}/superfish/css/superfish.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$DIR_JS}/gritter/css/jquery.gritter.css" media="screen">
<link rel="stylesheet" type="text/css" href="{$DIR_CSS}/css3buttons.css" media="screen">
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/jquery-1.4.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/jquery.bgiframe.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/hoverIntent.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/jquery-ui-1.8.2.custom.min.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/superfish.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/supersubs.js"></script>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}/gritter/jquery.gritter.min.js"></script>
<div id="header">
	<div id="headerleft"><div id="apptitle">{dcl_config name='DCL_APP_NAME'|escape}</div></div>
	<div id="headerright">
		<div id="search">
			{if $PERM_WORKORDERSEARCH || $PERM_TICKETSEARCH || $PERM_PROJECTSEARCH}
			<form method="POST" action="main.php">
				<input type="hidden" name="menuAction" value="htmlSearchBox.submitSearch">
				<select name="which">
					{if $PERM_WORKORDERSEARCH}<option value="openworkorders">Open {$TXT_WORKORDERS}</option><option value="workorders">All {$TXT_WORKORDERS}</option>{/if}
					{if $PERM_PROJECTSEARCH}<option value="opendcl_projects">Open {$TXT_PROJECTS}</option><option value="dcl_projects">All {$TXT_PROJECTS}</option>{/if}
					{if $PERM_TICKETSEARCH}<option value="opentickets">Open {$TXT_TICKETS}</option><option value="tickets">All {$TXT_TICKETS}</option>{/if}
					{if $PERM_WORKORDERSEARCH || $PERM_TICKETSEARCH}<option value="tags">Tags</option>{/if}
					{if $PERM_HOTLISTVIEW}<option value="hotlists">Hotlists</option>{/if}
				</select>&nbsp;
				<input type="text" name="search_text" size="20">&nbsp;
				<input type="submit" value="Search">
			</form>
			{/if}
		</div>
		<div id="quicknav">
			<a href="{$LNK_HOME}">{$TXT_HOME}</a>&nbsp;|&nbsp;{if $PERM_PREFS}<a href="{$LNK_PREFERENCES}">{$TXT_PREFERENCES}</a>&nbsp;|&nbsp;{/if}{if $PERM_WORKSPACE}{dcl_select_workspace default=$VAL_WORKSPACE}&nbsp;|&nbsp;{/if}<a href="{$LNK_LOGOFF}">{$TXT_LOGOFF}</a>
		</div>
	</div>
</div>{strip}
<div class="sf-menu-container"><ul class="sf-menu">
{foreach from=$VAL_DCL_MENU key=menu item=menuItems}
	<li><a href="javascript:;">{$menu|escape}</a><ul>
	{foreach from=$menuItems key=label item=menuItem name=mainMenuItems}
		{if $menuItem[1]}
			{if is_array($menuItem[0])}
			<ul>
				{foreach from=$menuItem[0] key=subLabel item=subMenuItem}
				<li><a href="{if substr($subMenuItem[0], 0, 7) != 'http://' && substr($subMenuItem[0], 0, strlen(DCL_WWW_ROOT)) != DCL_WWW_ROOT}{$URL_MAIN_PHP}?menuAction={/if}{$subMenuItem[0]|escape}"{if substr($subMenuItem[0], 0, 7) == 'http://'} target="_blank"{/if}>{$subLabel}</a></li>
				{/foreach}
			</ul>
			{else}{if !$smarty.foreach.mainMenuItems.first}</li>{/if}
			<li><a href="{if substr($menuItem[0], 0, 7) != 'http://' && substr($menuItem[0], 0, strlen(DCL_WWW_ROOT)) != DCL_WWW_ROOT}{$URL_MAIN_PHP}?menuAction={/if}{$menuItem[0]|escape}"{if substr($menuItem[0], 0, 7) == 'http://'} target="_blank"{/if}>{$label}</a>
			{/if}
		{/if}
	{/foreach}
	</ul></li>
{/foreach}
</ul></div>{/strip}
<div style="clear: both;"></div>
<script type="text/javascript">
//<![CDATA[{literal}
$(document).ready(function() {
	$(".sf-menu").supersubs().superfish();
	var $notification = $("div.dcl-notification");
	if ($notification.length > 0) {
		$.gritter.add({title: $notification.attr("title"), text: $notification.text()});
	}
{/literal}{if $PERM_WORKSPACE}{literal}$("#workspace_id").change(function() { location.href = "{/literal}{$URL_MAIN_PHP}{literal}?menuAction=htmlWorkspaceForm.changeWorkspace&workspace_id=" + $(this).val(); });{/literal}{/if}{literal}
});
{/literal}//]]>
</script>
<div id="left">{$NAV_BOXEN}</div>
<div id="content"><div style="width:100%;">
