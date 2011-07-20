{section name=org loop=$VAL_ORGS}
{if $smarty.section.org.first}
		<tr><td style="border: solid #cecece 2px;" colspan="2">
			<table border="0" width="100%">
				<tr><th class="sectionHeader">{$smarty.const.STR_CMMN_ORGANIZATION}</th></tr>
			</table>
			<table cellspacing="1" cellpadding="2" style="width: 100%;"><tr>
{/if}
			<td><a href="{$URL_MAIN_PHP}?menuAction=Organization.Detail&org_id={$VAL_ORGS[org].org_id}">{$VAL_ORGS[org].org_name}</a></td>
{if $smarty.section.org.index > 0 && $smarty.section.org.index % 4 == 0}</tr><tr>{/if}
{if $smarty.section.org.last}</tr></table></td></tr>{/if}
{/section}