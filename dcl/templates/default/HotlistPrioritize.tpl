{strip}<a class="left button" href="{$URL_MAIN_PHP}?menuAction=htmlHotlists.prioritize&hotlist_id={$VAL_HOTLIST_ID}">Reload</a>
<a class="middle button" id="select-all-closed" href="javascript:;">Remove All Closed</a>
<a class="positive middle button" id="ItemSave" href="javascript:;">{$smarty.const.STR_CMMN_SAVE}</a>
<a class="negative right button" href="{$URL_MAIN_PHP}?menuAction=htmlHotlistBrowse.show">{$smarty.const.STR_CMMN_CANCEL}</a>{/strip}
<table width="100%" class="dcl_results">
	<caption>Prioritize Hotlist [{$VAL_HOTLIST_NAME|escape}]</caption>
</table>
<style type="text/css">
{literal}
ol { padding: 0px; }
ol li { display: block;	float: left; width: 140px; margin-right: 4px; margin-bottom: 4px; background-color: #efefef; border: solid #999999 1px; padding: 4px; cursor: move; }
ol li.remove-item { background-color: #ffe6e6; }
ol li h2 { float: left; text-decoration: none; border: 0px none; padding: 4px; color: #333333; margin: 0px; }
ol li.remove-item h2 { color: #650000; }
ol li p { border: solid #cecece 1px; height: 60px; background-color: #ffffff; text-overflow: ellipsis; overflow: hidden; margin: 0px; padding: 2px; }
.clear-left { float: left; clear: left; }
{/literal}
</style>
<p>Drag and drop the items to define your priority order.</p>
<ol id="item_list">
{section loop=$items name=row}
<li id="item_{$items[row][0]}_{$items[row][1]}{if $items[row][0] == $smarty.const.DCL_ENTITY_WORKORDER}_{$items[row][2]}{/if}">
	<h2><a class="move-top" href="javascript:;" title="Move to Top"><span class="ui-icon ui-icon-circle-triangle-n"></span></a><span class="clear-left item-index">{counter}</span><a class="remove-item" href="javascript:;" title="Remove"><span class="ui-icon ui-icon-circle-close"></span></a></h2>
	<p><span class="status-type-{$items[row][11]}">{if $items[row][0] == $smarty.const.DCL_ENTITY_WORKORDER}{$items[row][1]|escape}-{$items[row][2]|escape}
		{elseif $items[row][0] == $smarty.const.DCL_ENTITY_TICKET}{$items[row][1]|escape}
		{/if} ({$items[row][6]|escape})</span>
	    {$items[row][3]|escape}</p>
</li>
{/section}
</ol>
<div class="clear"></div>
<script language="JavaScript" type="text/javascript" src="{$DIR_JS}jquery-ui-1.8.2.custom.min.js"></script>
<script language="javascript">
//<![CDATA[
{literal}
$(document).ready(function() {
	function updateIndexes() {
		var index = 0;
		$("#item_list li h2 span.item-index").each(function() {
			$(this).text(++index);
		});
	}
		
	function getData() {
		var regEx = /^[^_\-](?:[A-Za-z0-9\-]*)[_](.*)$/;
		var retVal = "menuAction=htmlHotlists.savePriority&hotlist_id={/literal}{$VAL_HOTLIST_ID}{literal}&" + 
			$("#item_list").sortable("serialize", { key: "item[]", expression: regEx });
				
		var $removeItems = $("#item_list li.remove-item");
		if ($removeItems.length > 0) {
			$removeItems.each(function() {
				var match = regEx.exec($(this).attr("id"));
				retVal += "&remove[]=" + encodeURIComponent(match[1]);
			});
		}

		return retVal;
	}
		
	$("#item_list").sortable({
		stop: function(event, ui) {
			updateIndexes();
		}
	});
	$("#item_list").disableSelection();
	$("#ItemSave").click(function() {
		$.ajax({
			type: 'POST',
			url: "{/literal}{$URL_MAIN_PHP}{literal}",
			data: getData(),
			success: function() {
				location.href = "{/literal}{$URL_MAIN_PHP}{literal}?menuAction=htmlHotlistBrowse.show";
			},
			error: function() {
				alert("Could not save hotlist priority order.");
			},
			dataType: "text/html"
		});
	});
	
	$("a.move-top").click(function() {
		var $listItem = $(this).parents("li:first");
		var $list = $listItem.parent();
		var $newItem = $listItem.clone(true);
		$listItem.fadeOut("fast", function() { 
			$(this).remove(); 
			$list.prepend($newItem.fadeIn("fast", function() {
				updateIndexes();
			}));
		});
	});
	
	$("a.remove-item").click(function() {
		var $list = $(this).parents("li:first");
		$list.toggleClass("remove-item");
	});
		
	$("a#select-all-closed").click(function() {
		$("#item_list li:not(.remove-item) span.status-type-2").each(function() {
			$(this).parents("li:first").find("a.remove-item").click();
		});
	});
});
{/literal}
//]]>
</script>
