<!-- $Id$ -->
<table width="100%" class="dcl_results">
	<caption>Prioritize Hotlist [{$VAL_HOTLIST_NAME}]</caption>
	<thead>
		<tr class="toolbar"><th colspan="3">
			<ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlHotlists.prioritize&hotlist_id={$VAL_HOTLIST_ID}">Reload</a></li>
				<li><a href="javascript:;" onclick="submitReorder();">{$smarty.const.STR_CMMN_SAVE}</a></li>
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
ol li h3 { text-decoration: none; border: 0px none; margin: 0px; padding: 2px; font-size: 100%; color: #3A81C1; }
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
		{/if} ({$items[row][4]|escape})</h3>
	<p>{$items[row][3]|escape}</p>
</li>
{/section}
</ol>
{dcl_scriptaculous_init}
<script language="javascript">
{literal}
Sortable.create("item_list", {
	constraint: false,
	format: /^[^_\-](?:[A-Za-z0-9\-]*)[_](.*)$/,
	onChange: function() {
		var $$items = $$("ol#item_list li h2");
		for (var index = 0; index < $$items.length; index++) {
			$$items[index].update(index + 1);
		}
	}
});

function submitReorder()
{
	var aOptions = {
		method: 'post',
		postBody: "menuAction=htmlHotlists.savePriority&hotlist_id={/literal}{$VAL_HOTLIST_ID}{literal}&" + Sortable.serialize('item_list'),
		onComplete: function(oRequest) {
			location.href = "{/literal}{$URL_MAIN_PHP}{literal}?menuAction=htmlHotlistBrowse.show";
		}
	};
{/literal}
	new Ajax.Request('{$URL_MAIN_PHP}', aOptions);
{literal}
}
{/literal}
</script>
