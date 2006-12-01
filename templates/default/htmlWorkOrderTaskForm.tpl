<!-- $Id$ -->
{dcl_validator_init}
<script language="JavaScript">
{if !$IS_EDIT}{literal}
	var _iCurrentID = 1;
	function canAddNewTask()
	{
		var oDiv = document.getElementById("div_task_summary");
		var oSummaries = oDiv.getElementsByTagName("INPUT");
		var aValidators = new Array();
		for (var i in oSummaries)
		{
			if (typeof(oSummaries[i]) != "object") continue;
			aValidators.push(new ValidatorString(oSummaries[i], "Summary"));
		}

		for (var i in aValidators)
		{
			if (!aValidators[i].isValid())
			{
				aValidators[i]._Element.focus();
				return false;
			}
		}
		
		return true;
	}
	function addTask(form){
		if (!canAddNewTask()) return;
		var oDiv = document.getElementById("div_task_summary");
		var oNewLabel = document.createElement("LABEL");
		oNewLabel.setAttribute("for", "task_summary_" + String(++_iCurrentID));
		oNewLabel.innerHTML = document.getElementById("label_summary_1").innerHTML;
		oNewLabel.id = "label_summary_" + String(_iCurrentID);
		oDiv.appendChild(oNewLabel);
		
		var oNewTask = document.createElement("INPUT");
		oNewTask.type = "text";
		oNewTask.size = 50;
		oNewTask.maxLength = 255;
		oNewTask.name = "task_summary[]";
		oNewTask.id = oNewLabel.getAttribute("for");
		oDiv.appendChild(oNewTask);
		
		var oNewRemove = document.createElement("A");
		oNewRemove.rowNumber = _iCurrentID;
		oNewRemove.onclick = function() { removeTask(this.rowNumber); }
		oNewRemove.innerHTML = "{/literal}{$smarty.const.STR_CMMN_DELETE}{literal}";
		oNewRemove.style.hover = "cursor: pointer; cursor: hand;";
		oNewRemove.id = "link_summary_" + String(_iCurrentID);
		oNewRemove.href = "#";
		oDiv.appendChild(oNewRemove);
		
		oNewTask.focus();
	}
	function removeTask(iTaskID)
	{
		if (iTaskID < 2) return;
		var sLabelID = "label_summary_" + iTaskID;
		var sSummaryID = "task_summary_" + iTaskID;
		var sLinkID = "link_summary_" + iTaskID;
		var oDiv = document.getElementById("div_task_summary");
		oDiv.removeChild(document.getElementById(sLabelID));
		oDiv.removeChild(document.getElementById(sSummaryID));
		oDiv.removeChild(document.getElementById(sLinkID));
	}
{/literal}{/if}
{literal}
	function validateAndSubmitForm(form){
		form.submit();
	}
	window.onload = function() {
		document.getElementById("task_summary_1").focus();
	}
{/literal}
</script>
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
{if $IS_EDIT}
	<input type="hidden" name="menuAction" value="htmlWorkOrderTask.submitModify">
	<input type="hidden" name="wo_task_id" value="{$VAL_WO_TASK_ID}">
	<fieldset>
		<legend>Edit Work Order Task</legend>
{else}
	<input type="hidden" name="menuAction" value="htmlWorkOrderTask.submitAdd">
	<input type="hidden" name="wo_id" value="{$VAL_WO_ID}">
	<input type="hidden" name="seq" value="{$VAL_SEQ}">
	<fieldset>
		<legend>Add Work Order Task</legend>
{/if}
		<div id="div_task_summary">
			<label for="task_summary_1" id="label_summary_1">Summary:</label>
			<input type="text" size="50" maxlength="255" name="task_summary{if !$IS_EDIT}[]{/if}" id="task_summary_1" value="{$VAL_SUMMARY}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			{if !$IS_EDIT}<input type="button" onclick="addTask(this.form);" value="{$smarty.const.STR_CMMN_NEW}">{/if}
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_OK}">
			<input type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
