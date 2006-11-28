<!-- $Id: logout.tpl,v 1.1.1.1 2006/11/27 05:30:39 mdean Exp $ -->
<html>
	<head>
		<meta http-equiv="Set-Cookie" content="DCLINFO=">
		<script language="JavaScript">
			window.onload = function()
			{literal}{{/literal}
				if (parent.frames["menu"] && parent.frames["workspace"])
					parent.location.href="{$URL}";
				else
					location.href="{$URL}";
			{literal}}{/literal}
		</script>
	</head>
	<body>
	</body>
</html>