<p>
	{if $PERM_ADD}<a class="btn btn-success" href="{dcl_url_action controller=Rubric action=Create}">{$smarty.const.STR_CMMN_NEW}</a>{/if}
	{if $PERM_ADMIN}<a class="btn btn-default" href="{dcl_url_action controller=SystemSetup action=Index}">{$smarty.const.DCL_MENU_SYSTEMSETUP}</a>{/if}
</p>
<table id="grid"></table>
<div id="pager"></div>
<link rel="stylesheet" type="text/css" href="{$DIR_JS}/jqgrid/css/ui.jqgrid.css" />
<script type="text/javascript" src="{$DIR_JS}/jqgrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="{$DIR_JS}/jqgrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}blockui/jquery.blockUI.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$.blockUI.defaults.css.border = "none";
		$.blockUI.defaults.css.padding = "15px";
		$.blockUI.defaults.css.backgroundColor = "#000";
		$.blockUI.defaults.css.borderRadius = "10px";
		$.blockUI.defaults.css.color = "#fff";

		$("#grid").jqGrid({
			url: '{$URL_MAIN_PHP}?menuAction=RubricService.GetData',
			datatype: "json",
			colNames:[
				'{$smarty.const.STR_CMMN_ID|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_NAME|escape:"javascript"}',
				'{$smarty.const.STR_CMMN_OPTIONS|escape:"javascript"}'
			],
			cmTemplate: { title: false },
			colModel:[
				{ name: 'rubric_id', index: 'rubric_id', width: 35, align: "right" },
				{ name: 'rubric_name', index: 'unit_name', width: 100 },
				{ name: 'options', index: 'options', width: 80, search: false, sortable: false, formatter: formatOptions }
			],
			rowNum: 25,
			rowList: [25, 50, 100],
			pager: '#pager',
			sortname: 'name',
			viewrecords: true,
			hidegrid: false,
			caption: "Rubrics"
		}).jqGrid('navGrid', '#pager', { edit: false, add: false, del: false, search: false })
		  .jqGrid('filterToolbar');

		var $content = $("#content");
		$('#grid').on('click', 'a.delete-item', function() {
			if (!confirm("Are you sure you want to delete this rubric?"))
				return;

			var id = $(this).attr("data-rubric-id");
			$content.block({ message: '<h4><img src="{$DIR_IMG}ajax-loader-bar-black.gif"> Deleting...</h4>' });
			$.ajax({
				type: "POST",
				url: "{dcl_url_action controller=Rubric action=Destroy params="id="}" + id,
				contentType: "application/json",
				dataType: "json"
			}).done(function() {
				$.gritter.add({ title: "Success", text: "Rubric deleted." });
				$("#grid").trigger('reloadGrid');
			}).fail(function(jqXHR, textStatus) {
				$.gritter.add({ title: "Error", text: "Could not delete rubric.  " + textStatus });
			}).always(function() {
				$content.unblock();
			});
		});

		$(window).on('resize', function() {
			var $grid = $("#grid");
			var $window = $(window);
			$grid.setGridHeight($window.height() - 250);
			$grid.setGridWidth($window.width() - 250);
		}).trigger('resize');
	});

	function formatOptions(value, options, row) {
		var retVal = [];

		{if $PERM_EDIT}retVal.push('<a class="btn btn-xs btn-info" href="{dcl_url_action controller=Rubric action=Edit params="id="}' + row[0] + '" title="{$smarty.const.STR_CMMN_EDIT|escape}"><span class="glyphicon glyphicon-pencil"></span></a>');{/if}
		{if $PERM_DELETE}retVal.push('<a class="btn btn-xs btn-danger delete-item" data-rubric-id="' + row[0] + '" href="javascript:;" title="{$smarty.const.STR_CMMN_DELETE}"><span class="glyphicon glyphicon-trash"></span></a>');{/if}

		return retVal.join(' ');
	}
</script>