<p>
{if $PERM_ADD}<a class="positive button" href="{dcl_url_action controller=Status action=Create}">{$smarty.const.STR_CMMN_NEW}</a>{/if}
{if $PERM_ADMIN}<a class="button" href="{dcl_url_action controller=SystemSetup action=Index}">{$smarty.const.DCL_MENU_SYSTEMSETUP}</a>{/if}
</p>
<table id="grid"></table>
<div id="pager"></div>
<link rel="stylesheet" type="text/css" href="{$DIR_JS}/jqgrid/css/ui.jqgrid.css" />
<script type="text/javascript" src="{$DIR_JS}/jqgrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="{$DIR_JS}/jqgrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript">{literal}
    $(document).ready(function() {
		$("#grid").jqGrid({
		   	url: '{/literal}{$URL_MAIN_PHP}?menuAction=StatusService.GetData{literal}',
			datatype: "json",
		   	colNames:[{/literal}
				'{$smarty.const.STR_STAT_ID|escape:"javascript"}',
				'{$smarty.const.STR_STAT_ACTIVE|escape:"javascript"}',
				'{$smarty.const.STR_STAT_SHORT|escape:"javascript"}',
				'{$smarty.const.STR_STAT_NAME|escape:"javascript"}',
				'{$smarty.const.STR_STAT_TYPE|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_OPTIONS|escape:"javascript"}'
			{literal}],
			cmTemplate: {title: false},
		   	colModel:[
		   		{name: 'id', index: 'id', width: 35, align: "right"},
		   		{name: 'active', index: 'active', width: 35, formatter: formatYN, stype: "select", searchoptions: {value: ":All;Y:Yes;N:No"}},
		   		{name: 'short', index: 'short', width: 55},
		   		{name: 'name', index: 'name', width: 100},
		   		{name: 'type', index: 'type', width: 55, stype: "select", searchoptions: {value: ":All;1:Open;2:Closed;3:Deferred"}},
		   		{name: 'options', index: 'options', width: 80, search: false, formatter: formatOptions}
		   	],
		   	rowNum: 25,
		   	rowList: [25, 50, 100],
		   	pager: '#pager',
		   	sortname: 'name',
		    viewrecords: true,
			hidegrid: false,
		    caption: "{/literal}{$smarty.const.STR_STAT_TABLETITLE|escape:"javascript"}{literal}"
		})
		.jqGrid('navGrid', '#pager', {edit: false, add: false, del: false, search: false})
		.jqGrid('filterToolbar');

		$(window).on('resize', function() {
			var $grid = $("#grid");
			var $window = $(window);
			$grid.setGridHeight($window.height() - 250);
			$grid.setGridWidth($window.width() - 200);
		}).trigger('resize');
    });

	function formatYN(value, options, row) {
		{/literal}return value == 'Y' ? '{$smarty.const.STR_CMMN_YES}' : '{$smarty.const.STR_CMMN_NO}';{literal}
	}

	function formatOptions(value, options, row) {
		var retVal = [];
		{/literal}
		{if $PERM_EDIT}retVal.push('<a class="button" href="{dcl_url_action controller=Status action=Edit params="id="}' + row[0] + '">{$smarty.const.STR_CMMN_EDIT}</a>');{/if}
		{if $PERM_DELETE}retVal.push('<a class="negative button" href="{dcl_url_action controller=Status action=Delete params="id="}' + row[0] + '">{$smarty.const.STR_CMMN_DELETE}</a>');{/if}
		{literal}
		return retVal.join(' ');
	}
{/literal}</script>