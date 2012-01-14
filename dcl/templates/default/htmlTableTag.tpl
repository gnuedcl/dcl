<!-- $Id$ -->
<div class="dcl_filter">
	<span><label for="filterStatus">{$smarty.const.STR_CMMN_TAGS}:</label> {dcl_tag_link value=$VAL_SELECTEDTAGS selected=$VAL_SELECTEDTAGS browse=Y}</span>
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
	<li{if $smarty.section.tb.first} class="first"{/if}><a href="#" onclick="document.forms.searchAction.elements.menuAction.value='{$toolbar[tb].link}'; submitBatch();">{$toolbar[tb].text|escape}</a></li>
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
	{if $checks}{assign var=woid value=$groupcount}{assign var=seq value=$groupcount+1}<td class="rowcheck"><input type="checkbox" name="selected[]" value="{$records[row][$woid]}.{$records[row][$seq]}"></td>{/if}
	{if $rownum}<td class="rownum">{$smarty.section.row.iteration}</td>{/if}
	{section loop=$records[row] name=item}
		{if $smarty.section.item.index != 1 && $smarty.section.item.index != 2 && !in_array($smarty.section.item.index, $groups) && $smarty.section.item.index < (count($records[row]) + $VAL_ENDOFFSET)}<td class="{$columns[$smarty.section.item.index].type}">
		{if $smarty.section.item.index == 0}
			{if $records[row][0] == $smarty.const.DCL_ENTITY_WORKORDER}<a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$records[row][1]}&seq={$records[row][2]}">{$records[row][1]|escape}-{$records[row][2]|escape}</a>
			{elseif $records[row][0] == $smarty.const.DCL_ENTITY_TICKET}<a href="{$URL_MAIN_PHP}?menuAction=boTickets.view&ticketid={$records[row][1]}">{$records[row][1]|escape}</a>
			{else}{$records[row][item]|escape}
			{/if}
		{else}
			{if $columns[$smarty.section.item.index].type == "html"}{$records[row][item]}
			{else}
				{if $smarty.section.item.index == 4}{dcl_tag_link value=$records[row][item] selected=$VAL_SELECTEDTAGS browse=Y}
				{else}{$records[row][item]|escape}
				{/if}
			{/if}</td>
		{/if}
		{/if}
	{/section}
	</tr>
	{if $smarty.section.row.last}</tbody>{/if}
{/section}
</table>
{if $checks}</form>{/if}
