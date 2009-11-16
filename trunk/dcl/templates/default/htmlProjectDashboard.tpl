<!-- $Id$ -->
<div class="dcl_detail">
	<table class="styled">
		<caption>[{$VAL_PROJECTID}] {$VAL_NAME|escape}</caption>
		<thead>{include file="ctlProjectOptions.tpl"}</thead>
		<tbody>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgProject.byStatus&id={$VAL_PROJECTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgProject.byDepartment&id={$VAL_PROJECTID}"></td>
			</tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgProject.bySeverity&id={$VAL_PROJECTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgProject.byPriority&id={$VAL_PROJECTID}"></td>
			<tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgProject.byModule&id={$VAL_PROJECTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgProject.byType&id={$VAL_PROJECTID}"></td>
			<tr>
		</tbody>
	</table>
</div>