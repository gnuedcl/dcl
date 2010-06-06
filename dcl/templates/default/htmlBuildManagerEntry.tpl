<!-- $Id$ -->
<script language="JavaScript">
function toggle(btnSender)
{
	var bChk = btnSender.checked;
	var bOK = false;
	var e=btnSender.form.elements;
	for (var i=0;i<e.length;i++)
	{
		if (!bOK && e[i] == btnSender)
			bOK = true;
		else if (bOK && (e[i].type != "checkbox" || e[i].name == "group_check"))
			return;
		else if (bOK && e[i].type == "checkbox")
			e[i].checked = bChk;
	} 
}
function showAccounts(iWOID, iSeq)
{
	var sURL = 'main.php?menuAction=htmlWindowList.FrameRender&what=dcl_wo_account.wo_id&wo_id=' + iWOID + '&seq=' + iSeq;
	var newWin = window.open(sURL, '_dcl_selector_', 'width=500,height=255');
}
 
function submitBatch()
{
	var f = document.forms.searchAction;
	var tf = document.forms.actionForm;
	var iSelected = tf.elements.menuAction.selectedIndex;
	if (iSelected == -1)
		return;

	f.elements.menuAction.value = tf.elements.menuAction.options[tf.elements.menuAction.selectedIndex].value;
	var sAction = f.elements.menuAction.value;

	if (sAction == 'boWorkorders.batchdetail' || sAction == 'boTimecards.batchadd' || sAction == 'boWorkorders.batchassign' || sAction == 'htmlProjectmap.move' || sAction == 'htmlProjectmap.batchmove')
	{
		var bHasCheck = false;
		for (var i = 0; i < f.elements.length && !bHasCheck; i++)
		{
			bHasCheck = (f.elements[i].type == "checkbox" && f.elements[i].name != "group_check" && f.elements[i].checked);
		}

		if (!bHasCheck)
		{
			alert('You must select one or more items!');
			return;
		}
	}

	f.submit();
}
</script>
<center>
<table style="border: 0px none;" cellspacing="0">
	<tr><th class="detailTitle">{TXT_TITLE}</th>
		<th class="detailLinks">
			<form name="actionForm">
				{VAL_VIEWSETTINGS}
				<select name="menuAction">
<!-- BEGIN actionFormOptions -->
					<option value="{VAL_ACTIONOPTION}">{TXT_ACTIONOPTION}</option>
<!-- END actionFormOptions -->
				</select>
				<input type="button" onclick="submitBatch();" value="{TXT_GO}">
			</form>
		</th>
	</tr>
<!-- BEGIN pager -->
<!-- This template always has a pager -->
<!-- END pager -->
	<tr>
		<td colspan="2">
<!-- BEGIN nomatches -->
			<span class="error">{TXT_NOMATCHES}</span>
<!-- END nomatches -->
<!-- BEGIN matches -->
			<form name="searchAction" method="post" action="{VAL_SEARCHACTION}">
				<input type="hidden" name="menuAction" value="" />
				{VAL_VIEWSETTINGS}
				<table style="border: 0px none;">
<!-- BEGIN section -->
	<!-- BEGIN group -->
					<tr><th class="{VAL_GROUPCLASS}" style="padding-left: {VAL_GROUPPADDING}px;" colspan="{VAL_GROUPCOLSPAN}">{VAL_GROUP}</th></tr>
	<!-- END group -->
	<!-- BEGIN detailHeader -->
					<tr>
		<!-- BEGIN detailHeaderCells -->
			<!-- BEGIN detailHeaderPadding -->
						<td style="padding-left: {VAL_DETAILHEADERPADDING}px;"></td>
			<!-- END detailHeaderPadding -->
			<!-- BEGIN detailHeaderCheckbox -->
						<th class="reportHeader"><input type="checkbox" name="group_check" onclick="javascript: toggle(this);"></th>
			<!-- END detailHeaderCheckbox -->
			<!-- BEGIN detailHeaderColumnText -->
						<th class="reportHeader">{VAL_COLUMNHEADER}</th>
			<!-- END detailHeaderColumnText -->
			<!-- BEGIN detailHeaderColumnLink -->
						<th class="reportHeader"><a class="adark" href="{LNK_COLUMNHEADER}">{VAL_COLUMNHEADER}</a></th>
			<!-- END detailHeaderColumnLink -->
		<!-- END detailHeaderCells -->
					</tr>
	<!-- END detailHeader -->
	<!-- BEGIN detailRows -->
		<!-- BEGIN detail -->
					<tr class="{VAL_DETAILCLASS}">
			<!-- BEGIN detailCells -->
				<!-- BEGIN detailPadding -->
						<td style="background-color: #ffffff;">&nbsp;</td>
				<!-- END detailPadding -->
				<!-- BEGIN detailCheckbox -->
						<td><input type="checkbox" name="selected[]" value="{VAL_ROWSELECT}" /></td>
				<!-- END detailCheckbox -->
				<!-- BEGIN detailColumnText -->
						<td>{VAL_COLUMNVALUE}</td>
				<!-- END detailColumnText -->
				<!-- BEGIN detailColumnAccount -->
						<td>{VAL_COLUMNVALUE}<img src="{IMG_DIR}/jump-to-16.png" style="cursor: pointer; cursor: hand;" onclick="showAccounts({VAL_WOID}, {VAL_SEQ});" /></td>
				<!-- END detailColumnAccount -->
				<!-- BEGIN detailColumnLink -->
						<td><a class="adark" href="{LNK_COLUMNVALUE}">{VAL_COLUMNVALUE}</a></td>
				<!-- END detailColumnLink -->
			<!-- END detailCells -->
					</tr>
		<!-- END detail -->
	<!-- END detailRows -->
<!-- END section -->
				</table>
			</form>
<!-- END matches -->
		</td>
	</tr>
</table>
</center>
