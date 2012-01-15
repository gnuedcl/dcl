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
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}jquery-ui-1.8.2.custom.min.js"></script>
<script language="javascript">
//<![CDATA[
{literal}
$(document).ready(function() {
	$("#task_list").sortable();
	$("#task_list").disableSelection();
	$("#TaskSave").click(function() {
		$.ajax({
			type: 'POST',
			url: "{/literal}{$URL_MAIN_PHP}{literal}",
			data: "menuAction=htmlWorkOrderTask.submitReorder&wo_id={/literal}{$VAL_JCN}&seq={$VAL_SEQ}{literal}&" + $("#task_list").sortable("serialize"),
			success: function() {
				location.href = "{/literal}{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$VAL_JCN}&seq={$VAL_SEQ}{literal}";
			},
			error: function() {
				alert("Could not save task order.");
			},
			dataType: "text/html"
		});
	});
});
{/literal}
//]]>
</script>