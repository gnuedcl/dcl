<!-- $Id: htmlWorkOrderResults.tpl,v 1.1.1.1 2006/11/27 05:30:37 mdean Exp $ -->
<!-- BEGIN header -->
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
	var sAction = f.elements.menuAction.value;

	if (sAction == 'boWorkorders.batchdetail' || sAction == 'boTimecards.batchadd' || sAction == 'boWorkorders.batchassign' || sAction == 'htmlProjectmap.move' || sAction == 'htmlProjectmap.batchmove' || sAction == 'boBuildManager.SubmitWO')
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
<table style="border: 0px none;" cellspacing="0">
	<tr><th class="detailTitle">{TXT_TITLE}</th>
		<th class="detailLinks">
			<form name="actionForm">
				{VAL_VIEWSETTINGS}
				<input type="hidden" name="menuAction" value="" />
<!-- BEGIN actionFormOptions -->{VAL_SPACER}<a class="adark" href="#" onclick="document.forms.searchAction.elements.menuAction.value='{VAL_ACTIONOPTION}'; submitBatch();">{TXT_ACTIONOPTION}</a>
<!-- END actionFormOptions -->
			</form>
		</th>
	</tr>
	<tr>
		<td colspan="2">
<!-- END header -->
<!-- BEGIN nomatches -->
			<span class="error">{TXT_NOMATCHES}</span>
<!-- END nomatches -->
<!-- BEGIN matchesStart -->
			<form name="searchAction" method="post" action="{VAL_SEARCHACTION}">
				<input type="hidden" name="menuAction" value="" />
				<input type="hidden" name="product" value="{HID_PRODUCT}" />
				{VAL_VIEWSETTINGS}
				<table style="border: 0px none;">
<!-- END matchesStart -->
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
<!-- BEGIN matchesEnd -->
				</table>
			</form>
<!-- END matchesEnd -->
<!-- BEGIN footer -->
		</td>
	</tr>
</table>
<!-- END footer -->