<!-- $Id$ -->
<form method="POST" action="{$URL_MAIN_PHP}" name="frmWorkorders">
	<input type="hidden" name="menuAction" value="">
	<input type="hidden" name="return_to" value="boProjects.viewproject">
	<input type="hidden" name="project" value="{$VAL_PROJECTID}">
	<div class="dcl_filter">
		<span><label for="wostatus">{$smarty.const.STR_PRJ_FILTERWOBYSTATUS}:</label> {dcl_select_status default=$VAL_FILTERSTATUS name=wostatus allowHideOrOnlyClosed=Y}</span>
		<span><label for="woresponsible">{$smarty.const.STR_WO_RESPONSIBLE}:</label> {dcl_select_personnel default=$VAL_FILTERRESPONSIBLE name=woresponsible project=$VAL_PROJECTID}</span>
		<span><label for="wogroupby">Group By:</label> {html_options name=wogroupby options=$OPT_GROUPBY selected=$VAL_FILTERGROUPBY}</span>
		<input type="button" value="{$smarty.const.STR_CMMN_FILTER}" onclick="this.form.elements['menuAction'].value='boProjects.viewproject';this.form.submit();">
		{if $VAL_PAGES > 1}
			{strip}<div><ul>
			{if $VAL_PAGE > 1}
			<li class="first"><a href="#" onclick="forms.pager.jumptopage.value={$VAL_PAGE-1};forms.pager.submit();">&lt;&lt;</a></li>
			{/if}
			{if $VAL_PAGE > 5}{assign var=startpage value=$VAL_PAGE-5}{else}{assign var=startpage value=1}{/if}
			{if $VAL_PAGE < ($VAL_PAGES-6)}{assign var=endpage value=$VAL_PAGE+6}{else}{assign var=endpage value=$VAL_PAGES+1}{/if}
			{section name=iPage start=$startpage loop=$endpage step=1}
			<li{if $smarty.section.iPage.first && $VAL_PAGE < 2} class="first"{/if}>{if $smarty.section.iPage.index == $VAL_PAGE}<strong>{$VAL_PAGE}</strong>{else}<a href="#" onclick="forms.pager.jumptopage.value={$smarty.section.iPage.index};forms.pager.submit();">{$smarty.section.iPage.index}</a>{/if}</li>
			{/section}
			{if $VAL_PAGE < $VAL_PAGES}
			<li><a href="#" onclick="forms.pager.jumptopage.value={$VAL_PAGE+1};forms.pager.submit();">&gt;&gt;</a></li>
			{/if}
			</ul></div>{/strip}
		{/if}
	</div>
	<table class="dcl_results"><caption>{$smarty.const.STR_PRJ_TASKLIST}</caption>
		<thead>
			<tr class="toolbar"><th colspan="17"><ul>
				<li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.newjcn&projectid={$VAL_PROJECTID}">{$smarty.const.STR_CMMN_NEW}</a></li>
				{if count($VAL_TASKS) > 0}
				<li><a href="javascript:forceSubmit('boWorkorders.batchdetail');">Detail</a></li>
				<li><a href="javascript:forceSubmit('boTimecards.batchadd');">Time Card</a></li>
				<li><a href="javascript:forceSubmit('boWorkorders.batchassign');">Assign</a></li>
				<li><a href="javascript:forceSubmit('htmlProjectmap.batchMove');">Move</a></li>
				{/if}
			</ul></th></tr>
			<tr><th>{if $VAL_FILTERGROUPBY == "none"}<input type="checkbox" name="group_check" onclick="javascript: toggleCheckGroup(this);">{/if}</th>
			<th>{$smarty.const.STR_WO_JCN}</th>
			<th>{$smarty.const.STR_WO_SEQ}</th>
			<th>{$smarty.const.STR_WO_TYPE}</th>
			<th>{$smarty.const.STR_WO_RESPONSIBLE}</th>
			<th>{$smarty.const.STR_WO_PRODUCT}</th>
			<th>{$smarty.const.STR_CMMN_MODULE}</th>
			<th>{$smarty.const.STR_WO_ACCOUNT}</th>
			<th>{$smarty.const.STR_WO_STATUS}</th>
			<th>{$smarty.const.STR_WO_DEADLINE}</th>
			<th>{$smarty.const.STR_WO_HOURSABB}</th>
			<th>{$smarty.const.STR_WO_ETC}</th>
			<th>{$smarty.const.STR_WO_PRJHRSABB}</th>
			<th>+/-</th>
			<th>% Complete</th>
			<th>{$smarty.const.STR_WO_SUMMARY}</th>
		</tr></thead>
{section loop=$VAL_TASKS name=row}
	{if $smarty.section.row.first}{strip}
		<tbody>
		{if $VAL_FILTERGROUPBY != "none"}
			{assign var=groupitemcount value=0}
			{section loop=$VAL_TASKS name=groupitemcountrow start=$smarty.section.row.index}
				{if $VAL_TASKS[row][$VAL_GROUPBY] == $VAL_TASKS[groupitemcountrow][$VAL_GROUPBY]}
					{assign var=groupitemcount value=$groupitemcount+1}
				{/if}
			{/section}
			<tr class="group"><td colspan="17"><input type="checkbox" onclick="toggleCheckGroup(this);" value="_groupcheck_">&nbsp;
			[&nbsp;{$VAL_TASKS[row][$VAL_GROUPBY]|escape}&nbsp;]&nbsp;({$groupitemcount})
			</td></tr>
		{/if}
	{/strip}{elseif $VAL_FILTERGROUPBY != "none"}{strip}
		{assign var=newgroup value=false}
		{if $VAL_TASKS[row][$VAL_GROUPBY] != $VAL_TASKS[row.index_prev][$VAL_GROUPBY]}
			{assign var=newgroup value=true}
		{/if}
		{if $newgroup == "true"}
			</tbody><tbody>
			{assign var=groupitemcount value=0}
			{section loop=$VAL_TASKS name=groupitemcountrow start=$smarty.section.row.index}
				{if $VAL_TASKS[row][$VAL_GROUPBY] == $VAL_TASKS[groupitemcountrow][$VAL_GROUPBY]}
					{assign var=groupitemcount value=$groupitemcount+1}
				{/if}
			{/section}
			{if $VAL_FILTERGROUPBY != "none"}
				<tr class="group"><td colspan="17"><input type="checkbox" onclick="toggleCheckGroup(this);" value="_groupcheck_">&nbsp;
				[&nbsp;{$VAL_TASKS[row][$VAL_GROUPBY]|escape}&nbsp;]&nbsp;({$groupitemcount})
				</td></tr>
			{/if}
		{/if}{/strip}
	{/if}
		<tr{if $smarty.section.row.iteration is even} class="even"{/if}>
			{assign var=woid value=$groupcount}
			{assign var=seq value=$groupcount+1}
			<td class="rowcheck"><input type="checkbox" name="selected[]" value="{$VAL_TASKS[row].woid}.{$VAL_TASKS[row].seq}"></td>
			<td class="html"><a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.viewjcn&jcn={$VAL_TASKS[row].woid}&seq={$VAL_TASKS[row].seq}">{$VAL_TASKS[row].woid}</a></td>
			<td class="string">{$VAL_TASKS[row].seq}</td>
			<td class="string">{$VAL_TASKS[row].type|escape}</td>
			<td class="string">{$VAL_TASKS[row].responsible|escape}</td>
			<td class="string">{$VAL_TASKS[row].product|escape}</td>
			<td class="string">{$VAL_TASKS[row].module|escape}</td>
			<td class="string">{$VAL_TASKS[row].org}{if $VAL_TASKS[row].secorgs}<a href="{$URL_MAIN_PHP}?menuAction=htmlWindowList.Main&what=dcl_wo_account.wo_id&wo_id={$VAL_TASKS[row].woid}&seq={$VAL_TASKS[row].seq}" class="dcl-lightbox"><img alt="Organizations for Work Order {$VAL_TASKS[row].woid}-{$VAL_TASKS[row].seq}" src="{$DIR_IMG}/jump-to-16.png" /></a>{/if}</td>
			<td class="string">{$VAL_TASKS[row].status|escape}</td>
			<td class="string">{$VAL_TASKS[row].deadline|escape}</td>
			<td class="numeric">{$VAL_TASKS[row].hours}</td>
			<td class="numeric">{$VAL_TASKS[row].etc}</td>
			<td class="numeric">{$VAL_TASKS[row].projected}</td>
			<td class="numeric">{$VAL_TASKS[row].plusminus}</td>
			<td class="numeric">{$VAL_TASKS[row].pctcomplete}</td>
			<td class="string">{$VAL_TASKS[row].summary}</td>
		</tr>
	{if $smarty.section.row.last}</tbody>{/if}
{/section}
	</table>
</form>
