{strip}
{section name=child loop=$VAL_CHILDPROJECTS}
{if $smarty.section.child.first}
<table class="table table-striped">
	<thead>
		<tr>
			<th></th>
			<th>{$smarty.const.STR_PRJ_TOTTASKSABB|escape}</th>
			<th>{$smarty.const.STR_PRJ_TASKSCOMPABB|escape}</th>
			<th>{$smarty.const.STR_PRJ_HOURSPROJABB|escape}</th>
			<th>{$smarty.const.STR_PRJ_HOURSAPPABB|escape}</th>
			<th>{$smarty.const.STR_PRJ_HOURSREMABB|escape}</th>
		</tr>
	</thead>
	<tbody>
{/if}
		<tr>
			<td><a href="{$URL_MAIN_PHP}?menuAction=Project.Detail&id={$VAL_CHILDPROJECTS[child].projectid}&wostatus=0">[{$VAL_CHILDPROJECTS[child].projectid}] {$VAL_CHILDPROJECTS[child].name}</a></td>
			<td class="numeric">{$VAL_CHILDPROJECTS[child].totaltasks}</td>
			<td class="numeric">{$VAL_CHILDPROJECTS[child].tasksclosed}</td>
			<td class="numeric">{$VAL_CHILDPROJECTS[child].esthours}</td>
			<td class="numeric">{$VAL_CHILDPROJECTS[child].totalhours}</td>
			<td class="numeric">{$VAL_CHILDPROJECTS[child].etchours}</td>
		</tr>
{if $smarty.section.child.last}</tbody></table></td></tr>{/if}
{/section}
{/strip}