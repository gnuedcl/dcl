{strip}
<table class="dcl_results" style="width:100%;">
	<caption class="spacer">Attachments</caption>
	<thead>
		{if $PERM_ATTACHFILE}<tr class="toolbar"><th colspan="4"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boProjects.upload&projectid={$VAL_PROJECTID}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>{/if}
		<tr>
			<th>Attachment</th>
			<th>Size</th>
			<th>Date</th>
			{if $PERM_REMOVEFILE}<th>Options</th>{/if}
		</tr>
	</thead>
	<tbody>
{section name=file loop=$VAL_ATTACHMENTS}
		<tr{if $smarty.section.child.iteration is even} class="even"{/if}>
			<td class="html"><a href="{$URL_MAIN_PHP}?menuAction=htmlProjectsdetail.Download&projectid={$VAL_PROJECTID}&filename={$VAL_ATTACHMENTS[file].filename|escape:"rawurl"}">{$VAL_ATTACHMENTS[file].filename|escape}</a></td>
			<td class="numeric">{$VAL_ATTACHMENTS[file].filesize}</td>
			<td class="string">{$VAL_ATTACHMENTS[file].filedate}</td>
			<td>{if $PERM_REMOVEFILE}<a href="{$URL_MAIN_PHP}?menuAction=boProjects.deleteattachment&projectid={$VAL_PROJECTID}&filename={$VAL_ATTACHMENTS[file].filename|escape:"rawurl"}">{$smarty.const.STR_CMMN_DELETE}</a>{/if}</td>
		</tr>
{/section}
</tbody></table>
{/strip}