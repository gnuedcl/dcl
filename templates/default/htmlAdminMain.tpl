<!-- $Id: htmlAdminMain.tpl,v 1.5 2006/11/27 06:00:51 mdean Exp $ -->
<table width="100%" class="dcl_results">
	<caption>{$TXT_SETUPTITLE}</caption>
	<thead><tr><th>Option</th><th>Description</th></tr></thead>
	<tbody>
{foreach item=option key=menuAction from=$VAL_OPTIONS}
	{cycle values="odd,even" assign="rowClass"}
		<tr class="{$rowClass}"><td valign="top" nowrap="nowrap"><a href="{$URL_MAIN_PHP}?menuAction={$menuAction}">{$option.action}</a></td>
			<td>{$option.description}{if $option.note != ""} <span class="error">{$option.note}</span>{/if}</td>
		</tr>
{/foreach}
	</tbody>
</table>