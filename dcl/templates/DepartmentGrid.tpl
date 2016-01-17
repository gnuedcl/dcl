{extends file="_Layout.tpl"}
{block name=title}{$smarty.const.STR_DEPT_TABLETITLE|escape}{/block}
{block name=css}
	<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}freejqgrid/css/ui.jqgrid.css" />
{/block}
{block name=content}
<p>
{if $PERM_ADD}<a class="btn btn-success" href="{dcl_url_action controller=Department action=Create}">{$smarty.const.STR_CMMN_NEW}</a>{/if}
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
		   	url: '{$URL_MAIN_PHP}?menuAction=DepartmentService.GetData',
			datatype: "json",
		   	colNames:[
				'{$smarty.const.STR_DEPT_ID|escape:"javascript"}',
				'{$smarty.const.STR_DEPT_ACTIVE|escape:"javascript"}',
				'{$smarty.const.STR_DEPT_SHORT|escape:"javascript"}',
				'{$smarty.const.STR_DEPT_NAME|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_OPTIONS|escape:"javascript"}'
			],
			cmTemplate: { title: false },
		   	colModel:[
		   		{ name: 'id', index: 'id', width: 35, align: "right" },
		   		{ name: 'active', index: 'active', width: 35, formatter: formatYN, stype: "select", searchoptions: { value: ":All;Y:Yes;N:No" } },
		   		{ name: 'short', index: 'short', width: 55 },
		   		{ name: 'name', index: 'name', width: 100 },
		   		{ name: 'options', index: 'options', width: 80, search: false, sortable: false, formatter: formatOptions }
		   	],
		   	rowNum: 25,
		   	rowList: [25, 50, 100],
		   	pager: '#pager',
		   	sortname: 'name',
		    viewrecords: true,
			hidegrid: false,
		    caption: "{$smarty.const.STR_DEPT_TABLETITLE|escape:"javascript"}"
		})
		.jqGrid('navGrid', '#pager', { edit: false, add: false, del: false, search: false })
		.jqGrid('filterToolbar');

		$(window).on('resize', function() {
			var $grid = $("#grid");
			var $window = $(window);
			$grid.setGridHeight($window.height() - 250);
			$grid.setGridWidth($window.width() - 200);
		}).trigger('resize');
    });

	function formatYN(value, options, row) {
		return value == 'Y' ? '{$smarty.const.STR_CMMN_YES}' : '{$smarty.const.STR_CMMN_NO}';
	}

	function formatOptions(value, options, row) {
		var retVal = [];

		{if $PERM_EDIT}retVal.push('<a class="btn btn-primary btn-xs" href="{dcl_url_action controller=Department action=Edit params="id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_EDIT}"><span class="glyphicon glyphicon-pencil"></span></a>');{/if}
		{if $PERM_DELETE}retVal.push('<a class="btn btn-danger btn-xs" href="{dcl_url_action controller=Department action=Delete params="id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_DELETE}"><span class="glyphicon glyphicon-trash"></span></a>');{/if}

		return retVal.join(' ');
	}
</script>
{/block}