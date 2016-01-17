{strip}
	<h4>Attachments</h4>
	<table width="100%" class="table table-striped">
		<thead>
			{if $PERM_ATTACHFILE}<tr><th colspan="4"><div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boTickets.upload&ticketid={$VAL_TICKETID}">{$smarty.const.STR_CMMN_NEW}</a></div></th></tr>{/if}
			<tr><th>Attachment</th><th>Size</th><th>Date</th><th>Options</th></tr>
		</thead>
{section name=file loop=$VAL_ATTACHMENTS}
{if $smarty.section.file.first}
		<tbody>
{/if}
			<tr>
				<td><a href="{$VAL_FORMACTION}?menuAction=htmlTicketDetail.Download&ticketid={$VAL_TICKETID}&filename={$VAL_ATTACHMENTS[file].filename|escape:"url"}">{$VAL_ATTACHMENTS[file].filename}</a></td>
				<td class="numeric">{$VAL_ATTACHMENTS[file].filesize}</td>
				<td>{$VAL_ATTACHMENTS[file].filedate}</td>
				{if $PERM_REMOVEFILE}<td><a href="{$VAL_FORMACTION}?menuAction=boTickets.deleteattachment&ticketid={$VAL_TICKETID}&filename={$VAL_ATTACHMENTS[file].filename|escape:"url"}">{$smarty.const.STR_CMMN_DELETE}</a></td>{/if}
			</tr>
{if $smarty.section.file.last}
		</tbody>
{/if}
{/section}
	</table>
{/strip}
