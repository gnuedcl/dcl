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
			if (oSummaries[i].type != "text") continue;
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
		
		var oNewFileLink = document.createElement("A");
		oNewFileLink.rowNumber = _iCurrentID;
		oNewFileLink.onclick = function() { toggleFile(this.rowNumber); }
		oNewFileLink.innerHTML = "{/literal}File{literal}";
		oNewFileLink.style.hover = "cursor: pointer; cursor: hand;";
		oNewFileLink.id = "link_file_" + String(_iCurrentID);
		oNewFileLink.href = "javascript:;";
		oDiv.appendChild(oNewFileLink);
		
		var oNewRemove = document.createElement("A");
		oNewRemove.rowNumber = _iCurrentID;
		oNewRemove.onclick = function() { removeTask(this.rowNumber); }
		oNewRemove.innerHTML = " {/literal}{$smarty.const.STR_CMMN_DELETE}{literal}";
		oNewRemove.style.hover = "cursor: pointer; cursor: hand;";
		oNewRemove.id = "link_summary_" + String(_iCurrentID);
		oNewRemove.href = "javascript:;";
		oDiv.appendChild(oNewRemove);
		
		var oNewFileDiv = document.createElement("DIV");
		oNewFileDiv.id = "div_file_" + String(_iCurrentID);
		oNewFileDiv.style.display = "none";
		
		var oNewFileLabel = document.createElement("LABEL");
		oNewFileLabel.setAttribute("for", "user_file_" + String(_iCurrentID));
		oNewFileLabel.innerHTML = document.getElementById("label_file_1").innerHTML;
		oNewFileLabel.id = "label_file_" + String(_iCurrentID);
		oNewFileDiv.appendChild(oNewFileLabel);
		
		var oNewFile = document.createElement("INPUT");
		oNewFile.type = "file";
		oNewFile.name = "user_file[]";
		oNewFile.id = oNewFileLabel.getAttribute("for");
		oNewFileDiv.appendChild(oNewFile);
		
		oDiv.appendChild(oNewFileDiv);

		oNewTask.focus();
	}
	function removeTask(iTaskID)
	{
		if (iTaskID < 2) return;
		var sLabelID = "label_summary_" + iTaskID;
		var sLabelFileID = "label_file_" + iTaskID;
		var sSummaryID = "task_summary_" + iTaskID;
		var sFileID = "user_file_" + iTaskID;
		var sLinkID = "link_summary_" + iTaskID;
		var oDiv = document.getElementById("div_task_summary");
		oDiv.removeChild(document.getElementById(sLabelID));
		oDiv.removeChild(document.getElementById(sSummaryID));
		oDiv.removeChild(document.getElementById(sLinkID));
		oDiv.removeChild(document.getElementById(sLabelFileID));
		oDiv.removeChild(document.getElementById(sFileID));
	}
	function toggleFile(iTaskID)
	{
		var oFileDiv = document.getElementById("div_file_" + iTaskID);
		if (oFileDiv)
			oFileDiv.style.display = oFileDiv.style.display == "none" ? "" : "none";
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
			<input type="text" size="50" maxlength="255" name="task_summary{if !$IS_EDIT}[]{/if}" id="task_summary_1" value="{$VAL_SUMMARY}"><a href="javascript:;" onclick="toggleFile(1);">File</a>
			<div style="display:none;" id="div_file_1">
				<label for="user_file_1" id="label_file_1">File:</label>
				<input style="display:block;margin-left:126px;" type="file" name="user_file{if !$IS_EDIT}[]{/if}" id="user_file_1">
			</div>
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
