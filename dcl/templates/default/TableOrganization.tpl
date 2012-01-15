<script language="JavaScript">
	var sStartsWith = '{$VAL_FILTERSTART}';
	var sActiveFilter = '{$VAL_FILTERACTIVE}';
{literal}
	var oLastButton = null;

	function selectStartsWith(oButton, sLetter)
	{
		if (oLastButton != null)
			oLastButton.className = 'dcl_startsWith';

		oLastButton = oButton;
		oButton.className = 'dcl_startsWithSelected';

		if (sLetter == 'All')
			sLetter = '';

		sStartsWith = sLetter;

		applyFilter();
	}

	function selectActive(sActive)
	{
		sActiveFilter = sActive;
		applyFilter();
	}

	function applyFilter()
	{
		var oStartsWith = document.getElementById('filterStartsWith');
		if (oStartsWith)
			oStartsWith.value = sStartsWith;

		document.forms.pager.submit();
	}

	function searchName(e)
	{
		if (e)
		{
			if (e.which != 13)
				return;
		}
		else if (event)
		{
			if (event.keyCode != 13)
				return;
		}
		else
			return;

		applyFilter();
	}

	function init()
	{
		document.getElementById('filterSearch').onkeydown = searchName;
{/literal}
		var sFilterStart = "{$VAL_FILTERSTART}";
{literal}
		if (sFilterStart == "")
			sFilterStart = "All";

		oLastButton = document.getElementById("btnStartsWith" + sFilterStart);
	}

	window.onload = init;
{/literal}
</script>
<div class="dcl_filter">
	<form name="pager" method="post" action="{$URL_MAIN_PHP}">
		{$VAL_VIEWSETTINGS}
		<input type="hidden" id="menuAction" name="menuAction" value="{$VAL_FILTERMENUACTION}" />
		<input type="hidden" id="startrow" name="startrow" value="{$VAL_FILTERSTARTROW}" />
		<input type="hidden" id="numrows" name="numrows" value="{$VAL_FILTERNUMROWS}" />
		<input type="hidden" id="jumptopage" name="jumptopage" value="{$VAL_PAGE}" />
		<input type="hidden" id="filterStartsWith" name="filterStartsWith" value="{$VAL_FILTERSTART}" />
		<span>
			<label><input type="radio" name="filterActive" id="filterActiveA" onclick="selectActive('');" value=""{if ($VAL_FILTERACTIVE == "" )} checked="checked"{/if}>&nbsp;{$smarty.const.STR_CMMN_ALL}</label>
			<label><input type="radio" name="filterActive" id="filterActiveY" onclick="selectActive('Y');" value="Y"{if ($VAL_FILTERACTIVE == "Y" )} checked="checked"{/if}>&nbsp;{$smarty.const.STR_CMMN_YES}</label>
			<label><input type="radio" name="filterActive" id="filterActiveN" onclick="selectActive('N');" value="N"{if ($VAL_FILTERACTIVE == "N" )} checked="checked"{/if}>&nbsp;{$smarty.const.STR_CMMN_NO}</label>
			&nbsp;|&nbsp;
			<input type="text" size="12" name="filterSearch" id="filterSearch" value="{$VAL_FILTERSEARCH}">
			&nbsp;|&nbsp;
			<input type="submit" name="filter" value="Filter">
		</span>
		<div class="dcl_filter_selectstart">
			{foreach from=$VAL_LETTERS item=letter}
				<div class="dcl_startsWith{if (($VAL_FILTERSTART == "" && $letter == "All") || ($VAL_FILTERSTART == $letter))}Selected{/if}" id="btnStartsWith{$letter}" onmouseover="if(this.className!='dcl_startsWithSelected')this.className='dcl_startsWithHover';" onmouseout="if(this.className!='dcl_startsWithSelected')this.className='dcl_startsWith';" onclick="selectStartsWith(this, '{$letter}');">{$letter}</div>
			{/foreach}
		</div>
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
	</form>
</div>
{assign var=groupcount value=$groups|@count}
{assign var=colcount value=$columns|@count}
{if $rownum}{assign var=colcount value=$colcount+1}{/if}
{if $checks}{assign var=colcount value=$colcount+1}
	<form name="searchAction" method="post" action="{$URL_MAIN_PHP}"><input type="hidden" name="menuAction" value="" />{$VAL_VIEWSETTINGS}
{/if}
<table class="dcl_results{if $inline} inline{/if}">
{if $caption ne ""}<caption>{$caption|escape}</caption>{/if}
{strip}
{section loop=$columns name=col}
	{if $smarty.section.col.first}<thead>
	{if $toolbar}
	<tr class="toolbar"><th colspan="{$colcount}">
	{section loop=$toolbar name=tb}
	{if $smarty.section.tb.first}<ul>{/if}
	<li{if $smarty.section.tb.first} class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction={$toolbar[tb].link}">{$toolbar[tb].text|escape}</a></li>
	{if $smarty.section.tb.last}</ul>{/if}
	{/section}
	</th></tr>
	{/if}
	<tr>{if $checks}<th>{if $groupcount == 0}<input type="checkbox" name="group_check" onclick="javascript: toggle(this);">{/if}</th>{/if}{if $rownum}<th></th>{/if}{/if}{if !in_array($smarty.section.col.index, $groups)}<th>{$columns[col].title|escape}</th>{/if}{if $smarty.section.col.last}</tr></thead>{/if}
{/section}
{/strip}
{section loop=$footer name=item}
{if $smarty.section.item.first}<tfoot><tr>{if $checks}<td></td>{/if}{if $rownum}<td></td>{/if}{/if}{if !in_array($smarty.section.item.index, $groups)}<td class="{$columns[$smarty.section.item.index].type}">{$footer[item]|escape}</td>{/if}{if $smarty.section.item.last}</tr></tfoot>{/if}
{/section}
{section loop=$records name=row}
	{if $smarty.section.row.first}{strip}
		<tbody>
		{section loop=$groups name=group}
			{assign var=groupcol value=$groups[group]}
			{if $smarty.section.group.first}<tr class="group"><td colspan="{$colcount}">{/if}
			{if $checks}<input type="checkbox" name="group_check" onclick="javascript: toggle(this);">{/if}
			{$columns[$groupcol].title|escape}&nbsp;[&nbsp;{$records[row][$groupcol]|escape}&nbsp;]&nbsp;
			{if $smarty.section.group.last}</td></tr>{/if}
		{/section}
	{/strip}{elseif count($groups) > 0}{strip}
		{assign var=newgroup value=false}
		{foreach from=$groups item=value key=key}
			{if $records[row][$value] != $records[row.index_prev][$value]}
				{assign var=newgroup value=true}
			{/if}
		{/foreach}
		{if $newgroup == "true"}
			</tbody><tbody>
			{section loop=$groups name=group}
				{assign var=groupcol value=$groups[group]}
				{if $smarty.section.group.first}<tr class="group"><td colspan="{$colcount}">{/if}
				{if $checks}<input type="checkbox" name="group_check" onclick="javascript: toggle(this);">{/if}
				{$columns[$groupcol].title|escape}&nbsp;[&nbsp;{$records[row][$groupcol]|escape}&nbsp;]&nbsp;
				{if $smarty.section.group.last}</td></tr>{/if}
			{/section}
		{/if}{/strip}
	{/if}
	<tr{if $smarty.section.row.iteration is even} class="even"{/if}>
	{if $checks}{assign var=ticketid value=$groupcount}<td class="rowcheck"><input type="checkbox" name="selected[]" value="{$records[row][$ticketid]}"></td>{/if}
	{if $rownum}<td class="rownum">{$smarty.section.row.iteration}</td>{/if}
	{section loop=$records[row] name=item}{if !in_array($smarty.section.item.index, $groups)}<td class="{$columns[$smarty.section.item.index].type}">{if $columns[$smarty.section.item.index].type == "html"}{$records[row][item]}{else}{if $columns[$smarty.section.item.index].title == $smarty.const.STR_CMMN_NAME}<a href="{$URL_MAIN_PHP}?menuAction=Organization.Detail&org_id={$records[row][0]}">{$records[row][item]|escape}</a>{else}{$records[row][item]|escape}{/if}{/if}</td>{/if}{/section}
	</tr>
	{if $smarty.section.row.last}</tbody>{/if}
{/section}
</table>
{if $checks}</form>{/if}
