<!-- $Id$ -->
<script language="JavaScript">
function toggleCheckGroup(btnSender)
{
	var bChk = btnSender.checked;
	var bOK = false;
	var e=btnSender.form.elements;
	for (var i=0;i<e.length;i++){
		if (!bOK && e[i] == btnSender)
			bOK = true;
		else if (bOK && (e[i].type != "checkbox" || e[i].value == "_groupcheck_"))
			return;
		else if (bOK && e[i].type == "checkbox" && e[i].value != "_groupcheck_")
			e[i].checked = bChk;
	}
}

function submitActionIfValid(sAction){
	var bHasChecks = false;
	var f = document.forms['frmWorkorders'];
	for (var i = 0; i < f.elements.length && !bHasChecks; i++){
		bHasChecks = (f.elements[i].type == "checkbox" && f.elements[i].checked)
	}
	if (bHasChecks){
		f.elements['menuAction'].value = sAction;
		f.submit();
	}
}

function showAccounts(iWOID, iSeq)
{
	var sURL = 'main.php?menuAction=htmlWindowList.FrameRender&what=dcl_wo_account.wo_id&wo_id=' + iWOID + '&seq=' + iSeq;
	var newWin = window.open(sURL, '_dcl_selector_', 'width=500,height=255');
}
</script>
<center>
<form method="POST" action="main.php" name="frmWorkorders">
<input type="hidden" name="menuAction" value="">
<input type="hidden" name="return_to" value="boProjects.viewproject">
<input type="hidden" name="project" value="{VAL_PROJECTID}">
<table border="0">
<tr><th colspan="16">{TXT_TITLE}</th></tr>
<tr>
	<th class="header">
	<!-- BEGIN nogrouping -->
	<input type="checkbox" onclick="toggleCheckGroup(this);" value="">
	<!-- END nogrouping -->
	</th>
	<th class="header">{TXT_WO}</th>
	<th class="header">{TXT_SEQ}</th>
	<th class="header">{TXT_TYPE}</th>
	<th class="header">{TXT_ASN}</th>
	<th class="header">{TXT_PRODUCT}</th>
	<th class="header">{TXT_MODULE}</th>
	<th class="header">{TXT_ACCOUNT}</th>
	<th class="header">{TXT_STATUS}</th>
	<th class="header">{TXT_DEADLINE}</th>
	<th class="header">{TXT_HRS}</th>
	<th class="header">{TXT_ETC}</th>
	<th class="header">{TXT_PRJ}</th>
	<th class="header">{TXT_PLUSMINUS}</th>
	<th class="header">{TXT_PCTCOMPLETE}</th>
	<th class="header">{TXT_SUMMARY}</th>
	<th class="header">{TXT_OPTIONS}</th>
</tr>
<!-- BEGIN grouping -->
<tr>
	<th style="text-align: left; background-color: #cecece;" colspan="17"><input type="checkbox" onclick="toggleCheckGroup(this);" value="_groupcheck_">&nbsp;&nbsp;{VAL_GROUP}</th>
</tr>
<!-- END grouping -->
<!-- BEGIN workorders -->
{HDR_GROUP}<tr style="background-color: {COLOR_ROW};">
	<td><input type="checkbox" name="selected[]" value="{VAL_WOID}.{VAL_SEQ}"></td>
	<td>{VAL_WOID}</td><td>{VAL_SEQ}</td><td>{VAL_TYPE}</td><td>{VAL_RESPONSIBLE}</td><td>{VAL_PRODUCT}</td><td>{VAL_MODULE}</td>
	<td>{VAL_ACCOUNT}
<!-- BEGIN secaccounts -->
<img src="{DIR_IMAGES}/jump-to-16.png" style="cursor: hand; cursor: pointer;" onclick="showAccounts({VAL_WOID}, {VAL_SEQ});">
<!-- END secaccounts -->
	</td>
	<td>{VAL_STATUS}</td><td>{VAL_DEADLINE}</td>
	<td style="text-align: right;">{VAL_HRS}</td><td style="text-align: right;">{VAL_ETC}</td><td style="text-align: right;">{VAL_PRJ}</td>
	<td style="text-align: right;">{VAL_PLUSMINUS}</td><td style="text-align: right;">{VAL_PCTCOMPLETE}</td><td>{VAL_SUMMARY}</td><td>{VAL_OPTIONS}</td>
</tr>
<!-- END workorders -->
</table>
</form>
</center>
