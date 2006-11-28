{if $VAL_PROJECTS}
		<tr><td style="border: solid #cecece 2px;" colspan="2">
			<table border="0" width="100%">
				<tr><th class="sectionHeader">{$smarty.const.STR_WO_PROJECT}</th></tr>
			</table>
{section name=project loop=$VAL_PROJECTS}
<a href="{$VAL_MENULINK}?menuAction=boProjects.viewproject&project={$VAL_PROJECTS[project].project_id}">{$VAL_PROJECTS[project].name|escape}</a>{if !$smarty.section.project.last}&nbsp;/&nbsp;{/if}
{/section}
			</td>
		</tr>
{/if}