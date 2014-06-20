<script language="JavaScript">

function forceSubmit(sAction)
{
	var f = document.frmProject;
	f.elements['menuAction'].value = sAction;
	processSubmit(f);
}

function processSubmit(f){
	var sAction = f.elements['menuAction'].value;
	if (sAction == 'WorkOrder.BatchDetail' || sAction == 'boTimecards.batchadd' || sAction == 'WorkOrder.BatchReassign')
	{
		if (!submitActionIfValid(sAction))
		{
			alert('You must select at least one work order.');
			return false;
		}
	}
	return true;
}

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

	return bHasChecks;
}

function showAccounts(iWOID, iSeq)
{
	var sURL = 'main.php?menuAction=htmlWindowList.FrameRender&what=dcl_wo_account.wo_id&wo_id=' + iWOID + '&seq=' + iSeq;
	var newWin = window.open(sURL, '_dcl_selector_', 'width=500,height=255');
}

</script>
<div class="dcl_detail">
	<form style="display:none;" method="post" action="{$URL_MAIN_PHP}" id="frmProject" name="frmProject" onsubmit="return processSubmit(this);">
		<input type="hidden" name="menuAction" value="">
		<input type="hidden" name="hotlist_id" value="{$VAL_HOTLISTID}">
		<input type="hidden" name="id" value="{$VAL_HOTLISTID}">
		<input type="hidden" name="name" value="FrontPage">
	</form>
	<table class="styled">
		<caption>[{$VAL_HOTLISTID}] {$VAL_NAME|escape}</caption>
		<thead>{include file="HotlistProjectOptionsControl.tpl"}</thead>
		<tbody>
			<tr><th>{$smarty.const.STR_PRJ_TOTTASKS}:</th><td>{$VAL_TOTALTASKS|escape}</td>
				<th>{$smarty.const.STR_PRJ_TASKSCOMP}:</th><td>{$VAL_TASKSCLOSED|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_HOURSPROJ}:</th><td>{$VAL_ESTHOURS|escape}</td>
				<th>{$smarty.const.STR_PRJ_PCTCOMP}:</th><td>{$VAL_PCTCOMP|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_TOTRESINCOMPWO}:</th><td>{$VAL_RESOURCES|escape}</td>
				<th>{$smarty.const.STR_PRJ_HOURSPM}:</th><td>{$VAL_HOURSPM|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_HOURSAPP}:</th><td>{$VAL_TOTALHOURS|escape}</td>
				<th>{$smarty.const.STR_PRJ_HOURSREM}:</th><td>{$VAL_ETCHOURS|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_DESCRIPTION}:</th>
				<td colspan="3">{$VAL_DESCRIPTION|escape|dcl_link}</td>
			</tr>
		</tbody>
	</table>
</div>
<p>&nbsp;</p>
{include file="HotlistProjectTasksControl.tpl"}
