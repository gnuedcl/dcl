<h1>{dcl_gravatar userId=`$Personnel->id` style="margin-right:4px;"} {$Contact->first_name|escape} {$Contact->last_name|escape} ({$Personnel->short|escape})</h1>
<table class="styled">
	<tbody>
		<tr><td><img src="{$URL_MAIN_PHP}?menuAction=PersonnelImage.ProductChart&id={$Personnel->id}"></td>
			<td><img src="{$URL_MAIN_PHP}?menuAction=PersonnelImage.StatusChart&id={$Personnel->id}"></td>
		</tr>
		<tr><td><img src="{$URL_MAIN_PHP}?menuAction=PersonnelImage.SeverityChart&id={$Personnel->id}"></td>
			<td><img src="{$URL_MAIN_PHP}?menuAction=PersonnelImage.PriorityChart&id={$Personnel->id}"></td>
		<tr>
		<tr><td><img src="{$URL_MAIN_PHP}?menuAction=PersonnelImage.TypeChart&id={$Personnel->id}"></td>
		<tr>
	</tbody>
</table>