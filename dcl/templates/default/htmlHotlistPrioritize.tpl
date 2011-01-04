<!-- $Id$ -->
<table width="100%" class="dcl_results">
	<caption>Prioritize Hotlist [{$VAL_HOTLIST_NAME}]</caption>
	<thead>
		<tr class="toolbar"><th colspan="3">
			<ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlHotlists.prioritize&hotlist_id={$VAL_HOTLIST_ID}">Reload</a></li>
				<li><a id="ItemSave" href="javascript:;">{$smarty.const.STR_CMMN_SAVE}</a></li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlHotlistBrowse.show">{$smarty.const.STR_CMMN_CANCEL}</a></li>
			</ul>
		</th></tr>
	</thead>
</table>
<style type="text/css">
{literal}
ol { padding: 0px; }
ol li { display: block;	float: left; width: 140px; margin-right: 8px; background-color: #efefef; border: solid #999999 1px; padding: 4px; cursor: move; }
ol li h2 { float: left; text-decoration: none; border: 0px none; padding: 4px; color: #666666; }
ol li h3 { text-decoration: none; border: 0px none; margin: 0px; padding: 2px; font-size: 100%; color: #3A81C1; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
ol li p { border: solid #cecece 1px; height: 60px; background-color: #ffffff; text-overflow: ellipsis; overflow: hidden; margin: 0px; padding: 2px; }
{/literal}
</style>
<p>Drag and drop the items to define your priority order.</p>
<ol id="item_list">
{section loop=$items name=row}
<li id="item_{$items[row][0]}_{$items[row][1]}{if $items[row][0] == $smarty.const.DCL_ENTITY_WORKORDER}_{$items[row][2]}{/if}">
	<h2>{counter}</h2>
	<h3>{if $items[row][0] == $smarty.const.DCL_ENTITY_WORKORDER}{$items[row][1]|escape}-{$items[row][2]|escape}
		{elseif $items[row][0] == $smarty.const.DCL_ENTITY_TICKET}{$items[row][1]|escape}
		{/if} ({$items[row][6]|escape})</h3>
	<p>{$items[row][3]|escape}</p>
</li>
{/section}
</ol>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}jquery-ui-1.8.2.custom.min.js"></script>
<script language="javascript">
//<![CDATA[
{literal}
$(document).ready(function() {
	$("#item_list").sortable({
		stop: function(event, ui) {
			var index = 0;
			$("#item_list li h2").each(function() {
				$(this).text(++index);
			});
		}
	});
	$("#item_list").disableSelection();
	$("#ItemSave").click(function() {
		$.ajax({
			type: 'POST',
			url: "{/literal}{$URL_MAIN_PHP}{literal}",
			data: "menuAction=htmlHotlists.savePriority&hotlist_id={/literal}{$VAL_HOTLIST_ID}{literal}&" + $("#item_list").sortable("serialize", { key: "item[]", expression: /^[^_\-](?:[A-Za-z0-9\-]*)[_](.*)$/ }),
			success: function() {
				location.href = "{/literal}{$URL_MAIN_PHP}{literal}?menuAction=htmlHotlistBrowse.show";
			},
			error: function() {
				alert("Could not save hotlist priority order.");
			},
			dataType: "text/html"
		});
	});
});
{/literal}
//]]>
</script>
