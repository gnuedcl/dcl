{strip}
	<div class="btn-group">
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Hotlist.Prioritize&hotlist_id={$VAL_HOTLIST_ID}">Reload</a>
		<a class="btn btn-default" id="select-all-closed" href="javascript:;">Select All Closed</a>
		<a class="btn btn-success" id="ItemSave" href="javascript:;">{$smarty.const.STR_CMMN_SAVE}</a>
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlHotlistBrowse.show">Browse</a>
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlHotlistProject.View&id={$VAL_HOTLIST_ID}">View as Project</a>
	</div>
{/strip}
<h4>Prioritize Hotlist [{$VAL_HOTLIST_NAME|escape}]</h4>
<style type="text/css">
ol { padding: 0px; list-style-type: none; }
ol li { margin-bottom: 4px; background-color: #efefef; border: solid #999999 1px; padding: 4px; cursor: move; }
ol li input.item-index { width: 50px; }
ol li span.item-description { border: solid #cecece 1px; background-color: #ffffff; margin: 0px; padding: 2px; }
div.scrollable { overflow: auto; }
</style>
<p>Drag and drop the items to define your priority order.</p>
<div class="scrollable">
<ol id="item_list">
{section loop=$items name=row}
<li id="item_{$items[row][0]}_{$items[row][1]}{if $items[row][0] == $smarty.const.DCL_ENTITY_WORKORDER}_{$items[row][2]}{/if}">
	<input type="text" class="item-index" value="{counter}"> <a class="move-top" href="javascript:;" title="Move to Top"><span class="glyphicon glyphicon-chevron-up"></span></a> <a class="remove-item" href="javascript:;" title="Remove"><span class="glyphicon glyphicon-trash"></span></a></h2>
	<span class="item-description"><span class="status-type-{$items[row][12]}">{if $items[row][0] == $smarty.const.DCL_ENTITY_WORKORDER}<a href="{dcl_url_action controller=WorkOrder action=Detail params="jcn=`$items[row][1]`&seq=`$items[row][2]`"}">{$items[row][1]|escape}-{$items[row][2]|escape}</a>
		{elseif $items[row][0] == $smarty.const.DCL_ENTITY_TICKET}{$items[row][1]|escape}
		{/if} ({$items[row][6]|escape})</span>
	    {$items[row][3]|escape} <span class="hidden text-danger">{dcl_get_entity_hotlist entity=$items[row][0] key_id=$items[row][1] key_id2=$items[row][2] link=N}</span></span>
</li>
{/section}
</ol>
</div>
<script type="text/javascript">
$(document).ready(function() {
	function updateIndexes() {
		var index = 0;
		$("#item_list").find("li input.item-index").each(function() {
			$(this).val(++index);
		});
	}
		
	function getData() {
		var regEx = /^[^_\-](?:[A-Za-z0-9\-]*)[_](.*)$/;
		var retVal = "menuAction=Hotlist.SavePriority&hotlist_id={$VAL_HOTLIST_ID}&" +
			$("#item_list").sortable("serialize", { key: "item[]", expression: regEx });
				
		var $removeItems = $("#item_list").find("li.remove-item");
		if ($removeItems.length > 0) {
			$removeItems.each(function() {
				var match = regEx.exec($(this).attr("id"));
				retVal += "&remove[]=" + encodeURIComponent(match[1]);
			});
		}

		return retVal;
	}

	$(window).on('resize', function() {
		var $scrollable = $("div.scrollable");
		var $window = $(window);
		$scrollable.css({ height: $window.height() - $scrollable.offset().top });
	}).trigger('resize');
		
	$("#item_list").sortable({
		stop: function(event, ui) {
			updateIndexes();
		}
	});

	function removeItemsAfterSave() {
		return $("#item_list").find("li.remove-item").fadeOut(500);
	}

	$("#ItemSave").click(function() {
		$.ajax({
			type: 'POST',
			url: "{$URL_MAIN_PHP}",
			data: getData(),
			success: function() {
				$.when(removeItemsAfterSave()).done(function() { $("#item_list").find("li.remove-item").remove(); updateIndexes(); });

				$.gritter.add({
					title: "Success",
					text: "Hotlist saved."
				});
			},
			error: function() {
				$.gritter.add({
					title: "Error",
					text: "Could not save hotlist."
				});
			},
			dataType: "json"
		});
	});

	function moveItemToPosition($li, index) {
		var $current = $("#item_list").find("li").eq(index);
		if ($current.length > 0) {
			$li.insertBefore($current);
		}
	}
	
	$("a.move-top").click(function() {
		var $listItem = $(this).parents("li:first");
		moveItemToPosition($listItem, 0);
		updateIndexes();
	});

	$("input.item-index").on("blur", function() {
		$(this).val($(this).parents("li:first").index() + 1);
		$(this).removeClass("bg-warning");
	}).keydown(function (e) {
		if (e.keyCode == 13) {
			e.preventDefault();
			var targetIndex = parseInt($(this).val(), 10) - 1;
			if (targetIndex > -1) {
				moveItemToPosition($(this).parents("li:first"), targetIndex);
			}

			updateIndexes();
			$(this).removeClass("bg-warning");
			return;
		}

		if ($.inArray(e.keyCode, [46, 8, 9, 27, 110, 190]) !== -1 ||
				(e.keyCode == 65 && e.ctrlKey === true) ||
				(e.keyCode >= 35 && e.keyCode <= 39)) {
			return;
		}

		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			e.preventDefault();
		}

		$(this).addClass("bg-warning");
	});
	
	$("a.remove-item").click(function() {
		var $list = $(this).parents("li:first");
		$list.toggleClass("remove-item bg-danger");
	});
		
	$("a#select-all-closed").click(function() {
		$("#item_list").find("li:not(.remove-item) span.status-type-2").each(function() {
			$(this).parents("li:first").find("a.remove-item").click();
		});
	});
});
</script>
