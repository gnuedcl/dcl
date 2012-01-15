<html>
<head><title>{TXT_TITLE}</title>
<style type="text/css">
	body { margin: 0px; background-color: #ffffff; font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
	td { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
	th { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
	input { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; width: 80px; height: 18px; }
	.titleTable { height: 20px; width: 100%; height: 100%; }
	.titleLeft { text-align: left; border-bottom: solid #3a81c1 2px; font-size: 12px; }
	.titleRight { text-align: right; width: 10%; border-bottom: solid #3a81c1 2px; white-space: nowrap; }
</style>
</head>
<body>
<table class="titleTable" border="0" cellspacing="0">
	<tr>
		<th class="titleLeft">{TXT_TITLE}</th>
		<td class="titleRight">
			<input type="button" value="{TXT_PRINT}" onclick="parent.main.print();">
			&nbsp;
			<input type="button" value="{TXT_OK}" onclick="parent.close();">
		</td>
	</tr>
</table>
</body>
</html>
