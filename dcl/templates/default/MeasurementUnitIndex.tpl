<p>
	{if $PERM_ADD}<a class="btn btn-success" href="{dcl_url_action controller=MeasurementUnit action=Create}">{$smarty.const.STR_CMMN_NEW}</a>{/if}
	{if $PERM_ADMIN}<a class="btn btn-default" href="{dcl_url_action controller=SystemSetup action=Index}">{$smarty.const.DCL_MENU_SYSTEMSETUP}</a>{/if}
</p>
<table id="grid"></table>
<div id="pager"></div>
<link rel="stylesheet" type="text/css" href="{$DIR_JS}/jqgrid/css/ui.jqgrid.css" />
<script type="text/javascript" src="{$DIR_JS}/jqgrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="{$DIR_JS}/jqgrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#grid").jqGrid({
			url: '{$URL_MAIN_PHP}?menuAction=MeasurementUnitService.GetData',
			datatype: "json",
			colNames:[
				'{$smarty.const.STR_CMMN_ID|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_NAME|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_ABBR|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_OPTIONS|escape:"javascript"}'
			],
			cmTemplate: { title: false },
			colModel:[
				{ name: 'measurement_unit_id', index: 'measurement_unit_id', width: 35, align: "right" },
				{ name: 'unit_name', index: 'unit_name', width: 100 },
				{ name: 'unit_abbr', index: 'unit_abbr', width: 35 },
				{ name: 'options', index: 'options', width: 80, search: false, sortable: false, formatter: formatOptions }
			],
			rowNum: 25,
			rowList: [25, 50, 100],
			pager: '#pager',
			sortname: 'name',
			viewrecords: true,
			hidegrid: false,
			caption: "Measurement Units"
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

	function formatOptions(value, options, row) {
		var retVal = [];

		{if $PERM_EDIT}retVal.push('<a class="btn btn-xs btn-info" href="{dcl_url_action controller=MeasurementUnit action=Edit params="id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_EDIT|escape}"><span class="glyphicon glyphicon-pencil"></span></a>');{/if}
		{if $PERM_DELETE}retVal.push('<a class="btn btn-xs btn-danger" href="{dcl_url_action controller=MeasurementUnit action=Delete params="id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_DELETE}"><span class="glyphicon glyphicon-trash"></span></a>');{/if}

		return retVal.join(' ');
	}
</script>