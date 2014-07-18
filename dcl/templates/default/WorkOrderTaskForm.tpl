{dcl_validator_init}
<form class="form-horizontal" id="theForm" method="post" action="{$URL_MAIN_PHP}">
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
	{dcl_form_control id=label_summary_1 controlsize=4 label="Summary" required=true}
		<input type="text" class="form-control" maxlength="255" name="task_summary{if !$IS_EDIT}[]{/if}" id="task_summary_1" value="{$VAL_SUMMARY|escape}">
	{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				{if !$IS_EDIT}<input class="btn btn-success" type="button" onclick="addTask(this.form);" value="{$smarty.const.STR_CMMN_NEW}">{/if}
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_OK}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
	{if !$IS_EDIT}
	var _iCurrentID = 1;
	function canAddNewTask() {
		var $emptyInputs = $("#theForm").find("input:text[id^=task_summary_]").filter(function() { return $.trim($(this).val()) == ""; });
		if ($emptyInputs.length == 0)
			return true;

		$emptyInputs.get(0).focus();
	}

	function addTask(form) {
		if (!canAddNewTask())
			return;

		_iCurrentID++;

		var html = '<div class="form-group" data-required="required" id="div-task-id-';
		html += _iCurrentID;
		html += '"><label for="task_summary_';
		html += _iCurrentID;
		html += '" class="col-sm-2 control-label">Summary</label>';
		html += '<div class="col-sm-4"><div class="input-group"><input type="text" class="form-control" maxlength="255" name="task_summary[]" id="task_summary_';
		html += _iCurrentID;
		html += '" value="">';
		html += '<span class="input-group-btn"><button class="btn btn-danger remove-task" data-remove-id="';
		html += _iCurrentID;
		html += '"><span class="glyphicon glyphicon-trash"></span></button></span>';
		html += '</div></div></div>';

		$("#theForm").find("fieldset:first").append(html);
		$("#task_summary_" + _iCurrentID).focus();
	}
	{/if}

	function validateAndSubmitForm(form) {
		form.submit();
	}

	$(function() {
		$("#task_summary_1").focus();

		$(document).on("click", "button.remove-task", function() {
			var idToRemove = $(this).attr("data-remove-id");
			if (idToRemove != "") {
				$("div#div-task-id-" + idToRemove).remove();
			}
		});
	});
</script>
