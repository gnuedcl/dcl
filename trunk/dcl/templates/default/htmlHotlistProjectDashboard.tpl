<!-- $Id$ -->
<div class="dcl_detail">
	<table class="styled">
		<caption>[{$VAL_HOTLISTID}] {$VAL_NAME|escape}</caption>
		<thead>{include file="ctlHotlistProjectOptions.tpl"}</thead>
		<tbody>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgHotlistProject.byStatus&id={$VAL_HOTLISTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgHotlistProject.byDepartment&id={$VAL_HOTLISTID}"></td>
			</tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgHotlistProject.bySeverity&id={$VAL_HOTLISTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgHotlistProject.byPriority&id={$VAL_HOTLISTID}"></td>
			<tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgHotlistProject.byModule&id={$VAL_HOTLISTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgHotlistProject.byType&id={$VAL_HOTLISTID}"></td>
			<tr>
		</tbody>
	</table>
</div>