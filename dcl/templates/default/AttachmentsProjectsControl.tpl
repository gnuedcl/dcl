{strip}
<table class="table table-striped">
	<thead>
		{if $PERM_ATTACHFILE}<tr><th colspan="4"><div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Project.Upload&projectid={$VAL_PROJECTID}">{$smarty.const.STR_CMMN_NEW}</a></div></th></tr>{/if}
		<tr>
			<th>Attachment</th>
			<th>Size</th>
			<th>Date</th>
			{if $PERM_REMOVEFILE}<th>Options</th>{/if}
		</tr>
	</thead>
	<tbody>
{section name=file loop=$VAL_ATTACHMENTS}
		<tr>
			<td class="html"><a href="{$URL_MAIN_PHP}?menuAction=Project.Download&projectid={$VAL_PROJECTID}&filename={$VAL_ATTACHMENTS[file].filename|escape:"rawurl"}">{$VAL_ATTACHMENTS[file].filename|escape}</a></td>
			<td class="numeric">{$VAL_ATTACHMENTS[file].filesize}</td>
			<td class="string">{$VAL_ATTACHMENTS[file].filedate}</td>
			<td>{if $PERM_REMOVEFILE}<a href="{$URL_MAIN_PHP}?menuAction=Project.DeleteAttachment&projectid={$VAL_PROJECTID}&filename={$VAL_ATTACHMENTS[file].filename|escape:"rawurl"}">{$smarty.const.STR_CMMN_DELETE}</a>{/if}</td>
		</tr>
{/section}
</tbody></table>
{/strip}