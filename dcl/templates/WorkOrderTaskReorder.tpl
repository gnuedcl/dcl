{extends file="_Layout.tpl"}
{block name=title}Reorder Tasks [{$VAL_JCN}-{$VAL_SEQ}]{/block}
{block name=content}
<h3>Reorder Tasks [{$VAL_JCN}-{$VAL_SEQ}]</h3>
<div class="btn-group">
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.add&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_NEW}</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.reorder&jcn={$VAL_JCN}&seq={$VAL_SEQ}">Reload</a>
	<a class="btn btn-primary" id="TaskSave" href="javascript:;">{$smarty.const.STR_CMMN_SAVE}</a>
	<a class="btn btn-danger" href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$VAL_JCN}&seq={$VAL_SEQ}#tasks">{$smarty.const.STR_CMMN_CANCEL}</a>
</div>
<p>
<ul id="task_list" class="sortable">
{section name=task loop=$VAL_TASKS}
<li class="task_{if $VAL_TASKS[task].task_complete != "Y"}in{/if}complete" id="task_{$VAL_TASKS[task].wo_task_id}">{$VAL_TASKS[task].task_summary|escape}</li>
{/section}
</ul>
</p>
{/block}
{block name=script}
<script type="text/javascript">
$(document).ready(function() {
	$("#task_list").sortable();
	$("#task_list").disableSelection();
	$("#TaskSave").click(function() {
		$.ajax({
			type: 'POST',
			url: "{$URL_MAIN_PHP}",
			data: "menuAction=htmlWorkOrderTask.submitReorder&wo_id={$VAL_JCN}&seq={$VAL_SEQ}&" + $("#task_list").sortable("serialize"),
			success: function() {
				location.href = "{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$VAL_JCN}&seq={$VAL_SEQ}#tasks";
			},
			error: function(xhr, textStatus, errorThrown) {
				alert("Could not save task order.  " + textStatus + ": " + errorThrown);
			},
			dataType: "text"
		});
	});
});
</script>
{/block}