<table width="100%" class="dcl_results">
	<caption class="spacer">Reorder Tasks [{$VAL_JCN}-{$VAL_SEQ}]</caption>
	<thead>
		<tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.add&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_NEW}</a></li><li><a href="{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.reorder&jcn={$VAL_JCN}&seq={$VAL_SEQ}">Reload</a></li><li><a id="TaskSave" href="javascript:;">{$smarty.const.STR_CMMN_SAVE}</a></li><li><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_CANCEL}</a></li></ul></th></tr>
	</thead>
</table>
<ul id="task_list" class="sortable">
{section name=task loop=$VAL_TASKS}
<li class="task_{if $VAL_TASKS[task].task_complete != "Y"}in{/if}complete" id="task_{$VAL_TASKS[task].wo_task_id}">{$VAL_TASKS[task].task_summary|escape}</li>
{/section}
</ul>
<script language="javascript">
//<![CDATA[

$(document).ready(function() {
	$("#task_list").sortable();
	$("#task_list").disableSelection();
	$("#TaskSave").click(function() {
		$.ajax({
			type: 'POST',
			url: "{$URL_MAIN_PHP}",
			data: "menuAction=htmlWorkOrderTask.submitReorder&wo_id={$VAL_JCN}&seq={$VAL_SEQ}&" + $("#task_list").sortable("serialize"),
			success: function() {
				location.href = "{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$VAL_JCN}&seq={$VAL_SEQ}";
			},
			error: function() {
				alert("Could not save task order.");
			},
			dataType: "text/html"
		});
	});
});

//]]>
</script>