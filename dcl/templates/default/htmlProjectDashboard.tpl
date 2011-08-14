<!-- $Id$ -->
<div class="dcl_detail">
	<table class="styled">
		<caption>[{$VAL_PROJECTID}] {$VAL_NAME|escape}</caption>
		<thead>{include file="ctlProjectOptions.tpl"}</thead>
		<tbody>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.StatusChart&id={$VAL_PROJECTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.DepartmentChart&id={$VAL_PROJECTID}"></td>
			</tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.SeverityChart&id={$VAL_PROJECTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.PriorityChart&id={$VAL_PROJECTID}"></td>
			<tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.ModuleChart&id={$VAL_PROJECTID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.TypeChart&id={$VAL_PROJECTID}"></td>
			<tr>
		</tbody>
	</table>
</div>