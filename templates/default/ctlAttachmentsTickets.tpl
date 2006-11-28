<!-- $Id: ctlAttachmentsTickets.tpl,v 1.1.1.1 2006/11/27 05:30:36 mdean Exp $ -->
{strip}
	<table width="100%" class="dcl_results">
		<caption class="spacer">Attachments</caption>
		<thead>
			{if $PERM_ATTACHFILE}<tr class="toolbar"><th colspan="4"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boTickets.upload&ticketid={$VAL_TICKETID}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>{/if}
			<tr><th>Attachment</th><th>Size</th><th>Date</th><th>Options</th></tr>
		</thead>
{section name=file loop=$VAL_ATTACHMENTS}
{if $smarty.section.file.first}
		<tbody>
{/if}
			<tr>
				<td><a href="{$VAL_FORMACTION}?menuAction=htmlTicketDetail.Download&ticketid={$VAL_TICKETID}&filename={$VAL_ATTACHMENTS[file].filename|escape:"rawurl"}">{$VAL_ATTACHMENTS[file].filename}</a></td>
				<td class="numeric">{$VAL_ATTACHMENTS[file].filesize}</td>
				<td>{$VAL_ATTACHMENTS[file].filedate}</td>
				{if $PERM_REMOVEFILE}<td><a href="{$VAL_FORMACTION}?menuAction=boTickets.deleteattachment&ticketid={$VAL_TICKETID}&filename={$VAL_ATTACHMENTS[file].filename|escape:"rawurl"}">{$smarty.const.STR_CMMN_DELETE}</a></td>{/if}
			</tr>
{if $smarty.section.file.last}
		</tbody>
{/if}
{/section}
	</table>
{/strip}