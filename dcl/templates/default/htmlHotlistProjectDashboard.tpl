<!-- $Id$ -->
<div class="dcl_detail">
	<table class="styled">
		<caption>[{$VAL_HOTLISTID}] {$VAL_NAME|escape}</caption>
		<thead>{include file="ctlHotlistProjectOptions.tpl"}</thead>
		<tbody>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.StatusChart&id={$VAL_HOTLISTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.DepartmentChart&id={$VAL_HOTLISTID}"></td>
			</tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.SeverityChart&id={$VAL_HOTLISTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.PriorityChart&id={$VAL_HOTLISTID}"></td>
			<tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.ModuleChart&id={$VAL_HOTLISTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.TypeChart&id={$VAL_HOTLISTID}"></td>
			<tr>
		</tbody>
	</table>
</div>