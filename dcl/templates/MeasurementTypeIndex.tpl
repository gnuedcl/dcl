{extends file="_Layout.tpl"}
{block name=title}Measurement Types{/block}
{block name=css}
	<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}freejqgrid/css/ui.jqgrid.css" />
{/block}
{block name=content}
<p>
	{if $PERM_ADD}<a class="btn btn-success" href="{dcl_url_action controller=MeasurementType action=Create}">{$smarty.const.STR_CMMN_NEW}</a>{/if}
	{if $PERM_ADMIN}<a class="btn btn-default" href="{dcl_url_action controller=SystemSetup action=Index}">{$smarty.const.DCL_MENU_SYSTEMSETUP}</a>{/if}
</p>
<table id="grid"></table>
<div id="pager"></div>
{/block}
{block name=script}
<script type="text/javascript" src="{$DIR_VENDOR}freejqgrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}freejqgrid/js/jquery.jqgrid.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#grid").jqGrid({
			url: '{$URL_MAIN_PHP}?menuAction=MeasurementTypeService.GetData',
			datatype: "json",
			colNames:[
				'{$smarty.const.STR_CMMN_ID|escape:"javascript"}',
				'Unit',
				'{$smarty.const.STR_CMMN_NAME|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_OPTIONS|escape:"javascript"}'
			],
			cmTemplate: { title: false },
			colModel:[
				{ name: 'measurement_type_id', index: 'measurement_type_id', width: 35, align: "right" },
				{ name: 'unit_name', index: 'unit_name', width: 100 },
				{ name: 'measurement_name', index: 'measurement_name', width: 100 },
				{ name: 'options', index: 'options', width: 80, search: false, sortable: false, formatter: formatOptions }
			],
			rowNum: 25,
			rowList: [25, 50, 100],
			pager: '#pager',
			sortname: 'measurement_name',
			viewrecords: true,
			hidegrid: false,
			caption: "Measurement Types"
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

		{if $PERM_EDIT}retVal.push('<a class="btn btn-xs btn-primary" href="{dcl_url_action controller=MeasurementType action=Edit params="id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_EDIT|escape}"><span class="glyphicon glyphicon-pencil"></span></a>');{/if}
		{if $PERM_DELETE}retVal.push('<a class="btn btn-xs btn-danger" href="{dcl_url_action controller=MeasurementType action=Delete params="id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_DELETE}"><span class="glyphicon glyphicon-trash"></span></a>');{/if}

		return retVal.join(' ');
	}
</script>
{/block}