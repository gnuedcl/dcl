{assign var=groupcount value=$groups|@count}
{assign var=colcount value=$columns|@count}
{if $rownum}{assign var=colcount value=$colcount+1}{/if}
{if $checks}{assign var=colcount value=$colcount+1}{/if}
<form name="searchAction" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="" />
	{$VAL_VIEWSETTINGS}
{if $caption ne ""}<h4>{$caption|escape}</h4>{/if}
<table class="table table-striped">
{strip}
{section loop=$columns name=col}
{if $columns[col].title == $smarty.const.STR_WO_ID}{assign var=wo_id value=$smarty.section.col.index}{/if}
{if $columns[col].title == $smarty.const.STR_WO_SEQ}{assign var=seq value=$smarty.section.col.index}{/if}
	{if $smarty.section.col.first}<thead>
	{if $toolbar}
	<tr><th colspan="{$colcount}"><div class="btn-group">
	{section loop=$toolbar name=tb}
	<a class="btn btn-default" href="javascript:;" onclick="document.forms.searchAction.elements.menuAction.value='{$toolbar[tb].link}'; submitBatch();">{$toolbar[tb].text|escape}</a>
	{/section}
	</div></th></tr>
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
			{if $smarty.section.group.first}<tr class="group"><td colspan="{$colcount}">
				{if $checks}<input type="checkbox" name="group_check" onclick="javascript: toggle(this);">{/if}
			{/if}
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
				{if $smarty.section.group.first}<tr class="group"><td colspan="{$colcount}">
					{if $checks}<input type="checkbox" name="group_check" onclick="javascript: toggle(this);">{/if}
				{/if}
				{$columns[$groupcol].title|escape}&nbsp;[&nbsp;{$records[row][$groupcol]|escape}&nbsp;]&nbsp;
				{if $smarty.section.group.last}</td></tr>{/if}
			{/section}
		{/if}{/strip}
	{/if}
	<tr>
	{if $checks}<td class="rowcheck"><input type="checkbox" name="selected[]" value="{$records[row][$groupcount]}"></td>{/if}
	{if $rownum}<td class="rownum">{$smarty.section.row.iteration}</td>{/if}
	{strip}
	{section loop=$records[row] name=item}
		{if !in_array($smarty.section.item.index, $groups) && $smarty.section.item.index < (count($records[row]) + $VAL_ENDOFFSET)}
			<td class="{$columns[$smarty.section.item.index].type}">
			{if $smarty.section.item.index == $ticket_id_ordinal}<a href="{$URL_MAIN_PHP}?menuAction=boTickets.view&ticketid={$records[row][$ticket_id_ordinal]}">{$records[row][item]}</a>
			{elseif $smarty.section.item.index == $tag_ordinal && $records[row][$num_tags_ordinal] > 1}{dcl_get_entity_tags entity=$smarty.const.DCL_ENTITY_TICKET key_id=$records[row][$ticket_id_ordinal] link=Y}
			{elseif $columns[$smarty.section.item.index].type == "html"}{$records[row][item]}
			{else}{$records[row][item]|escape}
			{/if}
			</td>
		{/if}
	{/section}
	{/strip}
	</tr>
	{if $smarty.section.row.last}</tbody>{/if}
{/section}
</table>
</form>
<script type="text/javascript">
	function submitBatch()
	{
		document.forms.searchAction.submit();
	}
</script>
