<html>
<!-- $Id: htmlProjectSelector.tpl,v 1.1.1.1 2006/11/27 05:30:37 mdean Exp $ -->
	<head>
		<frameset rows="85,*" border="0" frameborder="0">
			<frame name="topFrame" src="{$smarty.const.DCL_WWW_ROOT}main.php?menuAction=htmlProjectSelector.showControlFrame&multiple={$VAL_MULTIPLE}" marginwidth="0" marginheight="0">
			<frame name="mainFrame" src="{$smarty.const.DCL_WWW_ROOT}main.php?menuAction=htmlProjectSelector.showBrowseFrame&multiple={$VAL_MULTIPLE}" marginwidth="0" marginheight="0" scrolling="auto">
		</frameset>
	</head>
</html>