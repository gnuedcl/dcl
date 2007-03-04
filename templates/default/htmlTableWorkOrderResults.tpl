<!-- $Id$ -->
<script language="JavaScript">
{literal}
function toggle(btnSender)
{
	var bChk = btnSender.checked;
	var bOK = false;
	var e=btnSender.form.elements;
	for (var i=0;i<e.length;i++)
	{
		if (!bOK && e[i] == btnSender)
			bOK = true;
		else if (bOK && (e[i].type != "checkbox" || e[i].name == "group_check"))
			return;
		else if (bOK && e[i].type == "checkbox")
			e[i].checked = bChk;
	}
}
function showAccounts(iWOID, iSeq)
{
	var sURL = 'main.php?menuAction=htmlWindowList.FrameRender&what=dcl_wo_account.wo_id&wo_id=' + iWOID + '&seq=' + iSeq;
	var newWin = window.open(sURL, '_dcl_selector_', 'width=500,height=255');
}

function submitBatch()
{
	var f = document.forms.searchAction;
	var sAction = f.elements.menuAction.value;

	if (sAction == 'boWorkorders.batchdetail' || sAction == 'boTimecards.batchadd' || sAction == 'boWorkorders.batchassign' || sAction == 'htmlProjectmap.move' || sAction == 'htmlProjectmap.batchmove' || sAction == 'boBuildManager.SubmitWO')
	{
		var bHasCheck = false;
		for (var i = 0; i < f.elements.length && !bHasCheck; i++)
		{
			bHasCheck = (f.elements[i].type == "checkbox" && f.elements[i].name != "group_check" && f.elements[i].checked);
		}

		if (!bHasCheck)
		{
			alert('You must select one or more items!');
			return;
		}
	}
	f.submit();
}
{/literal}
</script>
{assign var=groupcount value=$groups|@count}
{assign var=colcount value=$columns|@count}
{if $rownum}{assign var=colcount value=$colcount+1}{/if}
{if $checks}{assign var=colcount value=$colcount+1}{/if}
<form name="searchAction" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="" />
	<input type="hidden" name="product" value="{$HID_PRODUCT}" />
	{$VAL_VIEWSETTINGS}
<table class="dcl_results{if $inline} inline{/if}"{if $width > 0} style="width:{$width};"{/if}>
{if $caption ne ""}<caption{if $spacer} class="spacer"{/if}>{$caption|escape}</caption>{/if}
{strip}
{section loop=$columns name=col}
{if $columns[col].title == $smarty.const.STR_WO_ID}{assign var=wo_id value=$smarty.section.col.index}{/if}
{if $columns[col].title == $smarty.const.STR_WO_SEQ}{assign var=seq value=$smarty.section.col.index}{/if}
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
	<tr{if $smarty.section.row.iteration is even} class="even"{/if}>
	{if $checks}<td class="rowcheck"><input type="checkbox" name="selected[]" value="{$records[row][$wo_id_ordinal]}.{$records[row][$seq_ordinal]}"></td>{/if}
	{if $rownum}<td class="rownum">{$smarty.section.row.iteration}</td>{/if}
	{strip}
	{section loop=$records[row] name=item}
		{if !in_array($smarty.section.item.index, $groups) && $smarty.section.item.index < (count($records[row]) + $VAL_ENDOFFSET)}
			<td class="{$columns[$smarty.section.item.index].type}">
			{if $smarty.section.item.index == $wo_id_ordinal || $smarty.section.item.index == $seq_ordinal}<a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.viewjcn&jcn={$records[row][$wo_id_ordinal]}&seq={$records[row][$seq_ordinal]}">{$records[row][item]}</a>
			{elseif $smarty.section.item.index == $tag_ordinal && $records[row][$num_tags_ordinal] > 1}{dcl_get_entity_tags entity=$smarty.const.DCL_ENTITY_WORKORDER key_id=$records[row][$wo_id_ordinal] key_id2=$records[row][$seq_ordinal] link=Y}
			{elseif $smarty.section.item.index == $tag_ordinal && $records[row][$num_tags_ordinal] == 1}{dcl_tag_link value=$records[row][item]}
			{elseif $columns[$smarty.section.item.index].type == "html"}{$records[row][item]}
			{else}{$records[row][item]|escape}{if $records[row][$num_accounts_ordinal] > 1 && $smarty.section.item.index == $org_ordinal}<img src="{$DIR_IMG}/jump-to-16.png" style="cursor: hand; cursor: pointer;" onclick="showAccounts({$records[row][$wo_id_ordinal]}, {$records[row][$seq_ordinal]});">{/if}
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
