<p>
{if $PERM_ADD}<a class="btn btn-success" href="{dcl_url_action controller=Role action=Create}">{$smarty.const.STR_CMMN_NEW}</a>{/if}
{if $PERM_ADMIN}<a class="btn btn-default" href="{dcl_url_action controller=SystemSetup action=Index}">{$smarty.const.DCL_MENU_SYSTEMSETUP}</a>{/if}
</p>
<table id="grid"></table>
<div id="pager"></div>
<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}jqgrid/css/ui.jqgrid.css" />
<script type="text/javascript" src="{$DIR_VENDOR}jqgrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}jqgrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
		$("#grid").jqGrid({
		   	url: '{$URL_MAIN_PHP}?menuAction=RoleService.GetData',
			datatype: "json",
		   	colNames:[
				'{$smarty.const.STR_CMMN_ID|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_ACTIVE|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_NAME|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_OPTIONS|escape:"javascript"}'
			],
			cmTemplate: { title: false },
		   	colModel:[
		   		{ name: 'role_id', index: 'role_id', width: 35, align: "right" },
		   		{ name: 'active', index: 'active', width: 35, formatter: formatYN, stype: "select", searchoptions: { value: ":All;Y:Yes;N:No" } },
		   		{ name: 'role_desc', index: 'role_desc', width: 100 },
		   		{ name: 'options', index: 'options', width: 80, search: false, sortable: false, formatter: formatOptions }
		   	],
		   	rowNum: 25,
		   	rowList: [25, 50, 100],
		   	pager: '#pager',
		   	sortname: 'role_desc',
		    viewrecords: true,
			hidegrid: false,
		    caption: "Roles"
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

		{if $PERM_ADD}retVal.push('<a class="btn btn-primary btn-xs" href="{dcl_url_action controller=Role action=Copy params="role_id="}' + row[0] + '" title="Copy"><span class="glyphicon glyphicon-copy"></span></a>');{/if}
		{if $PERM_EDIT}retVal.push('<a class="btn btn-primary btn-xs" href="{dcl_url_action controller=Role action=Edit params="role_id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_EDIT|escape}"><span class="glyphicon glyphicon-pencil"></span></a>');{/if}
		{if $PERM_DELETE}retVal.push('<a class="btn btn-danger btn-xs" href="{dcl_url_action controller=Role action=Delete params="role_id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_DELETE|escape}"><span class="glyphicon glyphicon-trash"></span></a>');{/if}

		return retVal.join(' ');
	}
</script>