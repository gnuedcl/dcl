{extends file="_Layout.tpl"}
{block name=title}{$smarty.const.STR_STAT_TABLETITLE|escape}{/block}
{block name=css}
	<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}freejqgrid/css/ui.jqgrid.css" />
{/block}
{block name=content}
<div class="btn-group">
	{if $PERM_ADD}<a class="btn btn-success" href="{dcl_url_action controller=Personnel action=Create}">{$smarty.const.STR_CMMN_NEW}</a>{/if}
	{if $PERM_SETUP}<a class="btn btn-default" href="{dcl_url_action controller=SystemSetup action=Index}">{$smarty.const.DCL_MENU_SYSTEMSETUP}</a>{/if}
</div>
<table id="grid"></table>
<div id="pager"></div>
{/block}
{block name=script}
<script type="text/javascript" src="{$DIR_VENDOR}freejqgrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}freejqgrid/js/jquery.jqgrid.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#grid").jqGrid({
			url: '{$URL_MAIN_PHP}?menuAction=PersonnelService.GetData',
			datatype: "json",
			colNames:[
				'{$smarty.const.STR_CMMN_ID|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_ACTIVE|escape:"javascript"}',
				'{$smarty.const.STR_USR_LOGIN|escape:"javascript"}',
				'Last Name',
				'First Name',
				'{$smarty.const.STR_USR_DEPARTMENT|escape:"javascript"}',
				'Phone',
				'Email',
				'Internet',
				'Options'
			],
			cmTemplate: { title: false },
			colModel:[
				{ name: 'id', index: 'id', width: 35, align: "right" },
				{ name: 'active', index: 'active', width: 35, formatter: formatYN, stype: "select", searchoptions: { value: ":All;Y:{$smarty.const.STR_CMMN_YES};N:{$smarty.const.STR_CMMN_NO};L:Locked" } },
				{ name: 'short', index: 'short', width: 75 },
				{ name: 'last_name', index: 'last_name', width: 75 },
				{ name: 'first_name', index: 'first_name', width: 75 },
				{ name: 'dept', index: 'dept', width: 105, stype: "select", searchoptions: { value: "{$VAL_DEPARTMENTOPTIONS|escape:"javascript"}" } },
				{ name: 'phone', index: 'phone', width: 100 },
				{ name: 'email', index: 'email', width: 100 },
				{ name: 'url', index: 'url', width: 100 },
				{ name: 'options', index: 'options', width: 80, search: false, sortable: false, formatter: formatOptions }
			],
			rowNum: 25,
			rowList: [25, 50, 100],
			pager: '#pager',
			sortname: 'name',
			viewrecords: true,
			hidegrid: false,
			caption: "{$smarty.const.STR_STAT_TABLETITLE|escape:"javascript"}"
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
		if (row[9] == 'Y')
			return 'Locked';

		return value == 'Y' ? '{$smarty.const.STR_CMMN_YES}' : '{$smarty.const.STR_CMMN_NO}';
	}

	function formatOptions(value, options, row) {
		var retVal = [];

		{if $PERM_MODIFY}retVal.push('<a class="btn btn-primary btn-xs" href="{dcl_url_action controller=Personnel action=Edit params="id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_EDIT|escape}"><span class="glyphicon glyphicon-pencil"></span></a>');{/if}
		{if $PERM_DELETE}retVal.push('<a class="btn btn-danger btn-xs" href="{dcl_url_action controller=Personnel action=Delete params="id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_DELETE|escape}"><span class="glyphicon glyphicon-trash"></span></a>');{/if}

		return retVal.join(' ');
	}
</script>
{/block}