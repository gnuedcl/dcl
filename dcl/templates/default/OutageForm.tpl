{dcl_bootstrapvalidator}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
<link rel="stylesheet" href="{$DIR_VENDOR}timepicker/jquery-ui-timepicker-addon.css">
<style type="text/css">
	#selected_orgs span { margin-right: 4px; margin-bottom: 4px; }
</style>
{dcl_validator_errors errors=$ERRORS}
<form id="theForm" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	{if $ViewData->OutageId}<input type="hidden" name="id" value="{$ViewData->OutageId}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=outage_title controlsize=4 label=$smarty.const.STR_CMMN_TITLE required=true}
			<input class="form-control" type="text" maxlength="100" id="outage_title" name="outage_title" value="{$ViewData->Title|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_type_id controlsize=4 label="Outage Type" required=true}
		{dcl_select_outage_type default=$ViewData->OutageType active=$ACTIVE_ONLY}
		{/dcl_form_control}
		{if $IS_EDIT}
			{dcl_form_control id=outage_status_id controlsize=4 label="Outage Status" required=true}
			{dcl_select_outage_status default=$ViewData->Status isplanned=$ViewData->IsPlanned}
			{/dcl_form_control}
		{/if}
		{dcl_form_control id=outage_sched_start controlsize=3 label="Scheduled Start" required=true}
			<input type="text" class="form-control" data-input-type="datetime" maxlength="16" id="outage_sched_start" name="outage_sched_start" value="{$ViewData->SchedStart|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_sched_end controlsize=3 label="Scheduled End" required=false}
			<input type="text" class="form-control" data-input-type="datetime" maxlength="16" id="outage_sched_end" name="outage_sched_end" value="{$ViewData->SchedEnd|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_start controlsize=3 label="Start" required=true}
			<input type="text" class="form-control" data-input-type="datetime" maxlength="16" id="outage_start" name="outage_start" value="{$ViewData->Start|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_end controlsize=3 label="End" required=false}
			<input type="text" class="form-control" data-input-type="datetime" maxlength="16" id="outage_end" name="outage_end" value="{$ViewData->End|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_description controlsize=10 label=Description required=true}
			<textarea class="form-control" name="outage_description" id="outage_description" rows="4" wrap valign="top">{$ViewData->Description|escape}</textarea>
		{/dcl_form_control}
		{dcl_form_control id=environment_id controlsize=10 label=Environments help="Selecting an environment will automatically select all organizations that were installed on that environment at the start time of the outage."}
		{dcl_select_environment default=$ViewData->Environments size=8 active=N}
		{/dcl_form_control}
		{dcl_form_control id=orgs controlsize=2 label=$smarty.const.STR_CMMN_ORGANIZATION}
			<a id="orgsLink" href="javascript:;">Select</a>
			<input type="hidden" id="outage_orgs" name="outage_orgs" value="{$ViewData->Orgs}">
		{/dcl_form_control}
		<div id="selected_orgs" class="col-xs-offset-2 col-xs-10">
		</div>
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input name="submitForm" type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input type="button" class="btn btn-link" onclick="location.href = '{dcl_url_action controller=Outage action=Index}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
			</div>
		</div>
	</fieldset>
</form>
<div id="dialog" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<table id="grid"></table>
				<div id="pager"></div>
			</div>
			<div class="modal-footer">
				<button id="saveOrganizations" type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" type="text/css" href="{$DIR_JS}/jqgrid/css/ui.jqgrid.css" />
<script type="text/javascript" src="{$DIR_JS}/jqgrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="{$DIR_JS}/jqgrid/js/jquery.jqGrid.min.js"></script>
<script type="text/javascript" src="{$DIR_JS}/bettergrow/jquery.BetterGrow.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}timepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
	$(function() {
		$("#outage_title").focus();
		$("input[data-input-type=date]").datepicker();
		$("input[data-input-type=datetime]").datetimepicker();
		$("textarea").BetterGrow();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });

		var $form = $("#theForm");

		$form.bootstrapValidator({
			container: "tooltip",
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				outage_title: {
					validators: {
						notEmpty: {
							message: "Title is required."
						}
					}
				}
			}
		}).on('error.field.bv', function (e, data) {
			var $parent = data.element.parents('.form-group'),
					$icon = $parent.find('.form-control-feedback[data-bv-icon-for="' + data.field + '"]'),
					title = $icon.data('bs.tooltip').getTitle();

			$icon.tooltip('destroy').tooltip({
				html: true,
				placement: 'right',
				title: title,
				container: 'body'
			});
		});

		// Outage type selection
		var $scheduledControls = $("#outage_sched_start,#outage_sched_end");
		var $scheduledControlsGroups = $scheduledControls.parents("div.form-group");
		var $startControlGroup = $("#outage_start").parents("div.form-group:first");

		function updateFormForOutageType() {
			var isScheduled = $("#outage_type_id").find(":selected[data-is-scheduled]").attr("data-is-scheduled");
			if (isScheduled == "Y") {
				$scheduledControls.removeAttr("disabled");
				$scheduledControlsGroups.addClass("required");
				$startControlGroup.removeClass("required");
			} else {
				$scheduledControls.attr("disabled", true);
				$scheduledControlsGroups.removeClass("required");
				$startControlGroup.addClass("required");
			}
		}

		$form.on("change", "#outage_type_id", updateFormForOutageType);
		updateFormForOutageType();

		// Orgs grid
		var selectedOrgs = [];

		$("#orgsLink").on("click", function () {
			updateSelectedOrgs();
			$("#grid").trigger("reloadGrid");
			$("#dialog").modal();
		});

		function loadGridSelection() {
			var $grid = $("#grid");
			var gridIds = $grid.jqGrid("getDataIDs");

			$.each(gridIds, function (idx, value) {
				if (selectedOrgs[value]) {
					$grid.jqGrid("setSelection", value);
				}
			});
		}

		function saveGridSelection() {
			var selectedIds = [];
			$.each(selectedOrgs, function (key, value) {
				if (value) {
					selectedIds.push(key);
				}
			});

			$("#outage_orgs").val(selectedIds.join(","));
			updateSelectedOrgNames();
		}

		function updateSelectedOrgs() {
			selectedOrgs = [];
			var formOrgs = String($("#outage_orgs").val()).split(",");
			$.each(formOrgs, function (idx, value) {
				selectedOrgs[value] = { name: "", selected: true };
			});
		}

		var $div = $("<div/>");

		function htmlEncode(val) {
			if (val === undefined)
				return "";

			return $div.text(val).html();
		}

		function updateSelectedOrgNames() {
			var formOrgs = String($("#outage_orgs").val()).split(",");
			var selectedOrgNames = [];
			$.each(formOrgs, function (idx, value) {
				selectedOrgNames.push(orgNames[value]);
			});

			selectedOrgNames.sort();

			var html = "";
			$.each(selectedOrgNames, function (key, value) {
				html += '<span class="badge alert-info">' + htmlEncode(value) + "</span>";
			});

			$("#selected_orgs").html(html);
		}

		$("#saveOrganizations").on("click", saveGridSelection);

		var orgNames = [];
		var orgList = [];
		var envOrgList = [];
		var orgEnvList = [];

		function getEnvironmentOrgData() {
			return $.getJSON("{dcl_url_action controller=EnvironmentOrgService action=GetData}");
		}

		function getOrgData() {
			return $.getJSON("{dcl_url_action controller=OrganizationService action=GetData}", { rows: -1, page: 1, sidx: "name", sord: "asc" });
		}

		function updateGridSelection(data) {
			orgList = data.rows;
			$.each(data.rows, function (key, value) {
				orgNames[value.id] = value.name;
			});

			updateSelectedOrgNames();
		}

		function updateEnvOrgList(data) {
			orgEnvList = [];
			envOrgList = data;
			$.each(data, function(k, v) {
				$.each(v, function(k1, v1) {
					orgEnvList[v1] = k;
				});
			});
		}

		function initGrid() {
			$("#grid").jqGrid({
				data: orgList,
				datatype: "local",
				colNames: [
					'{$smarty.const.STR_CMMN_ID|escape:"javascript"}',
					'{$smarty.const.STR_CMMN_NAME|escape:"javascript"}',
					'Phone',
					'Email',
					'URL'
				],
				cmTemplate: { title: false },
				colModel: [
					{ name: 'id', index: 'id', width: 35, align: "right" },
					{ name: 'name', index: 'name', width: 100 },
					{ name: 'phone', index: 'phone', width: 55 },
					{ name: 'email', index: 'email', width: 55 },
					{ name: 'url', index: 'url', width: 55 }
				],
				width: 850,
				height: 480,
				rowNum: 25,
				rowList: [25, 50, 100],
				pager: '#pager',
				sortname: 'name',
				viewrecords: true,
				hidegrid: false,
				multiselect: true,
				ignoreCase: true,
				caption: "Select Organizations",
				gridComplete: loadGridSelection,
				onSelectRow: function (rowid, status, e) {
					selectedOrgs[rowid] = status;
				},
				onSelectAll: function (aRowids, status) {
					for (var i = 0; i < aRowids.length; i++)
						selectedOrgs[aRowids[i]] = status;
				}
			})
			.jqGrid('navGrid', '#pager', { edit: false, add: false, del: false, search: false })
			.jqGrid('filterToolbar');
		}

		$.when(getEnvironmentOrgData(), getOrgData()).done(function(envOrg, org) {
			updateGridSelection(org[0]);
			initGrid();
			updateEnvOrgList(envOrg[0]);
		});
	});
</script>