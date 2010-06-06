<html><head><title>{TXT_TITLE}</title>
<style type="text/css">
	body { margin: 0px; background-color: #ffffff; font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
	td { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
	th { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
	select { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
	input { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
	.go { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; width: 80px; height: 18px; }
	form { margin-bottom: 0px; }
	img { border: 0px; }
	.formTitle { border-bottom: solid #3a81c1 2px; text-align: left; color: {COLOR_DARK}; font-size: 14px; }
	tr.formContainer { background-color: {COLOR_LIGHT}; }
	td.formLinks { border-bottom: solid #3a81c1 2px; text-align: right; }
</style>
<script language="JavaScript">
function setOptions(f) {
	var oValues = window.opener.oSelectorValue;
	var aText = window.opener.aSelectorText;
	var c = f.elements["_sel_"];

	oValues.value = "";
	aText.length = 0;

	if (c.options.length > 0) {
		var s = new String;
		for (var i = 0; i < c.options.length; i++) {
			if (i > 0)
				oValues.value += ",";

			oValues.value += c.options[i].value;
			aText[aText.length] = c.options[i].text
		}
	}

	// Silly IE thinks the call back is an object instead of a function
	if (typeof(window.opener.fSelectorCallBack) == "function" || (document.all && typeof(window.opener.fSelectorCallBack) == "object"))
		window.opener.fSelectorCallBack();

	window.close();
}

function moveSelected(from, to) {
	if (from.options.length > 0) {
		for (var i = 0; i < from.options.length; i++) {
			while (i < from.options.length && from.options[i].selected == true) {
				var o = new Option();
				o.value = from.options[i].value;
				o.text = from.options[i].text;
				to.options[to.options.length] = o;
				from.options[i] = null;
			}
		}
	}
}

function moveAll(from, to) {
	if (from.options.length > 0) {
		while (from.options.length > 0) {
			var o = new Option();
			o.value = from.options[0].value;
			o.text = from.options[0].text;
			to.options[to.options.length] = o;
			from.options[0] = null;
		}
	}
}

function validateRequest()
{
	if (window.opener == null || window.opener.oSelectorValue == null || window.opener.aSelectorText == null)
	{
		alert('Caller Error: opening window does not have selector destination elements set!');
		window.close();
	}
}
</script>
</head>
<body onload="validateRequest();">
<form name="selector">
<table border="0" height="100%" width="100%" cellspacing="0">
	<tr>
		<th colspan="2" class="formTitle">{TXT_TITLE}</th>
		<td class="formLinks">
			<input class="go" type="button" value="{TXT_SAVE}" onclick="setOptions(this.form);">
			<input class="go" type="button" value="{TXT_CANCEL}" onclick="window.close();">
		</td>
	</tr>
	<tr class="formContainer">
		<td><b>{TXT_AVAILABLE}:</b><br>
			<!-- Available Options -->
			<select name="_avail_" multiple size="16" style="width: 200px;">
<!-- BEGIN avail -->
				<option value="{VAL_VALUE}">{VAL_TEXT}</option>
<!-- END avail -->
			</select>
		</td>
		<td style="width: 100px; text-align: center; vertical-align: middle;">
			<!-- Action Buttons -->
			<input class="go" type="button" value="All >>" onclick="moveAll(this.form._avail_, this.form._sel_);"><br>
			<input class="go" type="button" value=">>" onclick="moveSelected(this.form._avail_, this.form._sel_);"><br>
			<input class="go" type="button" value="<<" onclick="moveSelected(this.form._sel_, this.form._avail_);"><br>
			<input class="go" type="button" value="All <<" onclick="moveAll(this.form._sel_, this.form._avail_);"><br>
		</td>
		<td style="width: 200px;"><b>{TXT_SELECTED}:</b><br>
			<!-- Selected Options -->
			<select name="_sel_" multiple size="16" style="width: 200px;">
<!-- BEGIN sel -->
				<option value="{VAL_VALUE}">{VAL_TEXT}</option>
<!-- END sel -->
			</select>
		</td>
	</tr>
</table>
</form>
</body>
</html>
