<p>
	{if $PERM_ADD}<a class="positive button" href="{dcl_url_action controller=OutageType action=Create}">{$smarty.const.STR_CMMN_NEW}</a>{/if}
	{if $PERM_ADMIN}<a class="button" href="{dcl_url_action controller=SystemSetup action=Index}">{$smarty.const.DCL_MENU_SYSTEMSETUP}</a>{/if}
</p>
<table id="grid"></table>
<div id="pager"></div>
<link rel="stylesheet" type="text/css" href="{$DIR_JS}/jqgrid/css/ui.jqgrid.css" />
<script type="text/javascript" src="{$DIR_JS}/jqgrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="{$DIR_JS}/jqgrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#grid").jqGrid({
			url: '{$URL_MAIN_PHP}?menuAction=OutageTypeService.GetData',
			datatype: "json",
			colNames:[
				'{$smarty.const.STR_CMMN_ID|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_NAME|escape:"javascript"}',
				'Down?',
				'Infrastructure?',
				'Planned?',
				'{$smarty.const.STR_CMMN_OPTIONS|escape:"javascript"}'
			],
			cmTemplate: { title: false },
			colModel:[
				{ name: 'outage_type_id', index: 'outage_type_id', width: 35, align: "right" },
				{ name: 'outage_type_name', index: 'outage_type_name', width: 100 },
				{ name: 'is_down', index: 'is_down', width: 35, formatter: formatYN, stype: "select", searchoptions: { value: ":All;Y:Yes;N:No" } },
				{ name: 'is_infrastructure', index: 'is_infrastructure', width: 35, formatter: formatYN, stype: "select", searchoptions: { value: ":All;Y:Yes;N:No" } },
				{ name: 'is_planned', index: 'is_planned', width: 35, formatter: formatYN, stype: "select", searchoptions: { value: ":All;Y:Yes;N:No" } },
				{ name: 'options', index: 'options', width: 80, search: false, sortable: false, formatter: formatOptions }
			],
			rowNum: 25,
			rowList: [25, 50, 100],
			pager: '#pager',
			sortname: 'name',
			viewrecords: true,
			hidegrid: false,
			caption: "Outage Types"
		})
		.jqGrid('navGrid', '#pager', { edit: false, add: false, del: false, search: false })
		.jqGrid('filterToolbar');

		$(window).on('resize', function() {
			var $grid = $("#grid");
			var $window = $(window);
			$grid.setGridHeight($window.height() - 250);
			$grid.setGridWidth($window.width() - 250);
		}).trigger('resize');
	});

	function formatYN(value, options, row) {
		return value == 'Y' ? '{$smarty.const.STR_CMMN_YES|escape:"javascript"}' : '{$smarty.const.STR_CMMN_NO|escape:"javascript"}';
	}

	function formatOptions(value, options, row) {
		var retVal = [];

		{if $PERM_EDIT}retVal.push('<a class="button" href="{dcl_url_action controller=OutageType action=Edit params="id="}' + row[0] + '">{$smarty.const.STR_CMMN_EDIT}</a>');{/if}
		{if $PERM_DELETE}retVal.push('<a class="negative button" href="{dcl_url_action controller=OutageType action=Delete params="id="}' + row[0] + '">{$smarty.const.STR_CMMN_DELETE}</a>');{/if}

		return retVal.join(' ');
	}
</script>