{strip}
	<table width="100%" class="dcl_results">
		<caption class="spacer">Attachments</caption>
		<thead>
			{if $PERM_ATTACHFILE}<tr class="toolbar"><th colspan="{if $PERM_REMOVEFILE}4{else}3{/if}"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.upload&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>{/if}
			<tr><th>Attachment</th><th>Size</th><th>Date</th>{if $PERM_REMOVEFILE}<th>Options</th>{/if}</tr>
		</thead>
{section name=file loop=$VAL_ATTACHMENTS}
{if $smarty.section.file.first}
		<tbody>
{/if}
			<tr>
				<td><a href="{$VAL_FORMACTION}?menuAction=htmlWorkOrderDetail.Download&jcn={$VAL_JCN}&seq={$VAL_SEQ}&filename={$VAL_ATTACHMENTS[file].filename|escape:"rawurl"}">{$VAL_ATTACHMENTS[file].filename}</a></td>
				<td class="numeric">{$VAL_ATTACHMENTS[file].filesize}</td>
				<td>{$VAL_ATTACHMENTS[file].filedate}</td>
				{if $PERM_REMOVEFILE}<td><a href="{$VAL_FORMACTION}?menuAction=boWorkorders.deleteattachment&jcn={$VAL_JCN}&seq={$VAL_SEQ}&filename={$VAL_ATTACHMENTS[file].filename|escape:"rawurl"}">{$smarty.const.STR_CMMN_DELETE}</a></td>{/if}
			</tr>
{if $smarty.section.file.last}
		</tbody>
{/if}
{/section}
	</table>
{/strip}