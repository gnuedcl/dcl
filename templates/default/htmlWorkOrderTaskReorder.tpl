<!-- $Id: htmlWorkOrderTaskReorder.tpl,v 1.1.1.1 2006/11/27 05:30:39 mdean Exp $ -->
{dcl_scriptaculous_init}
<table width="100%" class="dcl_results">
	<caption class="spacer">Reorder Tasks [{$VAL_JCN}-{$VAL_SEQ}]</caption>
	<thead>
		<tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.add&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_NEW}</a></li><li><a href="{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.reorder&jcn={$VAL_JCN}&seq={$VAL_SEQ}">Reload</a></li><li><a href="#" onclick="submitReorder();">{$smarty.const.STR_CMMN_SAVE}</a></li><li><a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.viewjcn&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_CANCEL}</a></li></ul></th></tr>
	</thead>
</table>
<ul id="task_list" class="sortable">
{section name=task loop=$VAL_TASKS}
<li class="task_{if $VAL_TASKS[task].task_complete != "Y"}in{/if}complete" id="task_{$VAL_TASKS[task].wo_task_id}">{$VAL_TASKS[task].task_summary|escape}</li>
{/section}
</ul>
<script language="javascript">
Sortable.create("task_list");
{literal}
function submitReorder()
{
	var aOptions = {
		method: 'post',
		postBody: "menuAction=htmlWorkOrderTask.submitReorder&wo_id={/literal}{$VAL_JCN}&seq={$VAL_SEQ}{literal}&" + Sortable.serialize('task_list'),
		onComplete: function(oRequest) {
			location.href = "{/literal}{$URL_MAIN_PHP}?menuAction=boWorkorders.viewjcn&jcn={$VAL_JCN}&seq={$VAL_SEQ}{literal}";
		}
	};
{/literal}
	new Ajax.Request('{$URL_MAIN_PHP}', aOptions);
{literal}
}
{/literal}
</script>