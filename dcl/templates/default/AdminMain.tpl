<h4>{$TXT_SETUPTITLE|escape}</h4>
<table class="table table-striped">
	<thead><tr><th>Option</th><th>Description</th></tr></thead>
	<tbody>
{foreach item=option key=menuAction from=$VAL_OPTIONS}
		<tr><td valign="top" nowrap="nowrap"><a href="{$URL_MAIN_PHP}?menuAction={$menuAction}">{$option.action|escape}</a></td>
			<td>{$option.description|escape}{if $option.note != ""} <span class="error">{$option.note|escape}</span>{/if}</td>
		</tr>
{/foreach}
	</tbody>
</table>