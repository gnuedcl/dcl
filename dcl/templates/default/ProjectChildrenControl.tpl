{strip}
{section name=child loop=$VAL_CHILDPROJECTS}
{if $smarty.section.child.first}
<table class="dcl_results" style="width:100%;">
	<caption class="spacer">{$smarty.const.STR_PRJ_CHILDPRJ}</caption>
	<thead>
		<tr>
			<th></th>
			<th>{$smarty.const.STR_PRJ_TOTTASKSABB}</th>
			<th>{$smarty.const.STR_PRJ_TASKSCOMPABB}</th>
			<th>{$smarty.const.STR_PRJ_HOURSPROJABB}</th>
			<th>{$smarty.const.STR_PRJ_HOURSAPPABB}</th>
			<th>{$smarty.const.STR_PRJ_HOURSREMABB}</th>
		</tr>
	</thead>
	<tbody>
{/if}
		<tr{if $smarty.section.child.iteration is even} class="even"{/if}>
			<td><a href="{$URL_MAIN_PHP}?menuAction=Project.Detail&id={$VAL_CHILDPROJECTS[child].projectid}&wostatus=0">[{$VAL_CHILDPROJECTS[child].projectid}] {$VAL_CHILDPROJECTS[child].name}</a></td>
			<td class="numeric">&nbsp;{$VAL_CHILDPROJECTS[child].totaltasks}&nbsp;</td>
			<td class="numeric">&nbsp;{$VAL_CHILDPROJECTS[child].tasksclosed}&nbsp;</td>
			<td class="numeric">&nbsp;{$VAL_CHILDPROJECTS[child].esthours}&nbsp;</td>
			<td class="numeric">&nbsp;{$VAL_CHILDPROJECTS[child].totalhours}&nbsp;</td>
			<td class="numeric">&nbsp;{$VAL_CHILDPROJECTS[child].etchours}&nbsp;</td>
		</tr>
{if $smarty.section.child.last}</tbody></table></td></tr>{/if}
{/section}
{/strip}