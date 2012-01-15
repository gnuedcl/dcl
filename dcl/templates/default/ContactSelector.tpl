<html>
	<head>
		<frameset rows="85,*" border="0" frameborder="0">
			<frame name="topFrame" src="{$smarty.const.DCL_WWW_ROOT}main.php?menuAction=htmlContactSelector.showControlFrame&multiple={$VAL_MULTIPLE}&filterID={$VAL_FILTERID|escape:"url"}&filterActive={$VAL_FILTERACTIVE}" marginwidth="0" marginheight="0">
			<frame name="mainFrame" src="{$smarty.const.DCL_WWW_ROOT}main.php?menuAction=htmlContactSelector.showBrowseFrame&multiple={$VAL_MULTIPLE}&filterID={$VAL_FILTERID|escape:"url"}&filterActive={$VAL_FILTERACTIVE}" marginwidth="0" marginheight="0" scrolling="auto">
		</frameset>
	</head>
</html>