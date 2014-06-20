{if $VAL_STATUS_TYPE != 2}

<script language="javascript">
function toggleTaskComplete(iTaskID, oCheckBox)
{

	location.href = "{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.submitToggle&wo_task_id=" + iTaskID + "&task_complete=" + (oCheckBox.checked ? "Y" : "N");

}
</script>

{/if}
{strip}
	<table width="100%" class="dcl_results">
		<caption class="spacer">{$VAL_TASKS|@count} Tasks</caption>
		<thead>
			{if $PERM_ACTION && $VAL_STATUS_TYPE != 2}<tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.add&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_NEW}</a></li><li><a href="{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.reorder&jcn={$VAL_JCN}&seq={$VAL_SEQ}">Reorder</a></li></ul></th></tr>{/if}
			<tr>{if $PERM_ACTION && $VAL_STATUS_TYPE != 2}<th style="width:2%;"></th>{/if}<th>Summary</th>{if $PERM_ACTION && $VAL_STATUS_TYPE != 2}<th style="width:10%;">{$smarty.const.STR_CMMN_OPTIONS}</th>{/if}</tr>
		</thead>
{section name=task loop=$VAL_TASKS}
{if $smarty.section.task.first}
		<tbody>
{/if}
			<tr>
				{if $PERM_ACTION && $VAL_STATUS_TYPE != 2}<td><input type="checkbox" onclick="toggleTaskComplete({$VAL_TASKS[task].wo_task_id}, this);" value="{$VAL_TASKS[task].wo_task_id}"{if $VAL_TASKS[task].task_complete == "Y"} checked{/if}></td>{/if}
				<td class="task_{if $VAL_TASKS[task].task_complete != "Y"}in{/if}complete">{$VAL_TASKS[task].task_summary|escape} <span class="task_audit">(Added by {dcl_metadata_display type='personnel' value=$VAL_TASKS[task].task_create_by|escape} on {$VAL_TASKS[task].task_create_dt}{if $VAL_TASKS[task].task_complete == "Y"}; Completed by {dcl_metadata_display type='personnel' value=$VAL_TASKS[task].task_complete_by|escape} on {$VAL_TASKS[task].task_complete_dt}{/if})</span></td>
				{if $PERM_ACTION && $VAL_STATUS_TYPE != 2}<td>{if $VAL_TASKS[task].task_complete != "Y"}
				<a href="{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.modify&wo_task_id={$VAL_TASKS[task].wo_task_id}">{$smarty.const.STR_CMMN_EDIT}</a>
				&nbsp;|&nbsp;
				<a href="{$URL_MAIN_PHP}?menuAction=htmlWorkOrderTask.delete&wo_task_id={$VAL_TASKS[task].wo_task_id}">{$smarty.const.STR_CMMN_DELETE}</a>
				{/if}</td>{/if}
			</tr>
{if $smarty.section.task.last}
		</tbody>
{/if}
{/section}
	</table>
{/strip}