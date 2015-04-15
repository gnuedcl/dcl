{strip}
	<table class="table table-striped">
		<thead>
			{if $PERM_ATTACHFILE}<tr class="toolbar"><th colspan="{if $PERM_REMOVEFILE}4{else}3{/if}"><div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Attachment&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_NEW}</a></div></th></tr>{/if}
			<tr><th>Attachment</th><th>Size</th><th>Date</th>{if $PERM_REMOVEFILE}<th>Options</th>{/if}</tr>
		</thead>
{section name=file loop=$VAL_ATTACHMENTS}
{if $smarty.section.file.first}
		<tbody>
{/if}
			<tr>
				<td><a href="{$VAL_FORMACTION}?menuAction=WorkOrder.DownloadAttachment&jcn={$VAL_JCN}&seq={$VAL_SEQ}&filename={$VAL_ATTACHMENTS[file].filename|escape:"url"}">{$VAL_ATTACHMENTS[file].filename}</a></td>
				<td class="numeric">{$VAL_ATTACHMENTS[file].filesize}</td>
				<td>{$VAL_ATTACHMENTS[file].filedate}</td>
				{if $PERM_REMOVEFILE}<td><a href="{$VAL_FORMACTION}?menuAction=WorkOrder.DeleteAttachment&jcn={$VAL_JCN}&seq={$VAL_SEQ}&filename={$VAL_ATTACHMENTS[file].filename|escape:"url"}">{$smarty.const.STR_CMMN_DELETE}</a></td>{/if}
			</tr>
{if $smarty.section.file.last}
		</tbody>
{/if}
{/section}
	</table>
{/strip}
