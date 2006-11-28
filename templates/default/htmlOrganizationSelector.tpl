<html>
<!-- $Id: htmlOrganizationSelector.tpl,v 1.1.1.1 2006/11/27 05:30:36 mdean Exp $ -->
	<head>
		<title>Select Organization</title>
		<frameset rows="85,*" border="0" frameborder="0">
			<frame name="topFrame" src="{$smarty.const.DCL_WWW_ROOT}main.php?menuAction=htmlOrganizationSelector.showControlFrame&multiple={$VAL_MULTIPLE}&filterID={$VAL_FILTERID|escape:"url"}" marginwidth="0" marginheight="0">
			<frame name="mainFrame" src="{$smarty.const.DCL_WWW_ROOT}main.php?menuAction=htmlOrganizationSelector.showBrowseFrame&multiple={$VAL_MULTIPLE}&filterID={$VAL_FILTERID|escape:"url"}" marginwidth="0" marginheight="0" scrolling="auto">
		</frameset>
	</head>
</html>