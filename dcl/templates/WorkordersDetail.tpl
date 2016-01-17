{extends file="_Layout.tpl"}
{block name=title}[{$VAL_JCN}-{$VAL_SEQ}] {$VAL_SUMMARY|escape}{/block}
{block name=css}
	{if $PERM_MODIFY_TC && $VAL_EDITTCID}
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
	{/if}
	<style type="text/css">
		td.option { cursor: pointer; }
	</style>
{/block}
{block name=content}
<div class="panel panel-info">
	<div class="panel-heading"><h3>[{$VAL_JCN}-{$VAL_SEQ}] {$VAL_SUMMARY|escape}</h3></div>
	<div class="panel-body">
		<ul id="tabs" class="nav nav-tabs">
			<li class="active"><a href="#workorder" data-toggle="tab">Work Order</a></li>
			<li><a href="#files" data-toggle="tab">Files <span class="badge{if count($VAL_ATTACHMENTS) > 0} alert-info{/if}">{$VAL_ATTACHMENTS|@count}</span></a></li>
			<li><a href="#tasks" data-toggle="tab">Tasks <span class="badge{if count($VAL_TASKS) > 0} alert-info{/if}">{$VAL_TASKS|@count}</span></a></li>
			{include file="WorkOrderOptionsControl.tpl"}
		</ul>
		<div id="navTabContent" class="tab-content">
			<div class="tab-pane fade in active" id="workorder">
				<div class="container-fluid">
				<div class="row">
					<div class="col-xs-6">
						<h4>Details</h4>
						<ul class="list-unstyled">
							<li><span class="glyphicon glyphicon-cog"></span> {$VAL_PRODUCT|escape} {$VAL_MODULE|escape}</li>
							<li><span class="glyphicon glyphicon-user"></span> {dcl_personnel_link text=$VAL_RESPONSIBLE id=$VAL_RESPONSIBLEID}</li>
							<li><span class="glyphicon glyphicon-stats"></span> <strong class="status-type-{$VAL_STATUS_TYPE}">{$VAL_STATUS|escape}</strong> {$VAL_STATUSON|escape}</li>
							<li><span class="glyphicon glyphicon-sort-by-order"></span> {$VAL_PRIORITY|escape}</li>
							<li><span class="glyphicon glyphicon-flash"></span> {$VAL_SEVERITY|escape}</li>
							<li><span class="glyphicon glyphicon-asterisk"></span> {$VAL_TYPE|escape}</li>
							<li><span class="glyphicon glyphicon-{if $VAL_PUBLIC == $smarty.const.STR_CMMN_YES}eye-open{else}lock{/if}"></span> {if $VAL_PUBLIC == $smarty.const.STR_CMMN_YES}Public{else}Private{/if}</li>
							{if $VAL_REPORTED_VERSION}<li><span class="glyphicon glyphicon-asterisk"></span> Reported Version {$VAL_REPORTED_VERSION|escape}</li>{/if}
							{if $VAL_TARGETED_VERSION}<li><span class="glyphicon glyphicon-asterisk"></span> Targeted Version {$VAL_TARGETED_VERSION|escape}</li>{/if}
							{if $VAL_FIXED_VERSION}<li><span class="glyphicon glyphicon-asterisk"></span> Fixed Version {$VAL_FIXED_VERSION|escape}</li>{/if}
						</ul>
					</div>
					<div class="col-xs-6">
						<h4>Dates and Times</h4>
						<ul class="list-unstyled">
							<li><span class="glyphicon glyphicon-bullhorn"></span> {$smarty.const.STR_WO_OPENBY} {dcl_personnel_link text=$VAL_CREATEBY id=$WorkOrder->createby} on {$VAL_CREATEDON|escape}</li>
							{if $VAL_STATUS_TYPE == 2}<li><span class="glyphicon glyphicon-flag"></span> {$smarty.const.STR_WO_CLOSEBY} {dcl_personnel_link text=$VAL_CLOSEDBY id=$WorkOrder->closedby} on {$VAL_CLOSEDON|escape}</li>{/if}
							<li><span class="glyphicon glyphicon-time"></span> {if $VAL_TOTALHOURS != ""}{$VAL_TOTALHOURS|escape}{else}0{/if} Hours ({$VAL_ETCHOURS|escape} Remaining, {$VAL_ESTHOURS} Estimated)</li>
							<li><span class="glyphicon glyphicon-calendar"></span> {$smarty.const.STR_WO_DEADLINE} {$VAL_DEADLINEON|escape}</li>
							<li><span class="glyphicon glyphicon-calendar"></span> {$smarty.const.STR_WO_LASTACTION} {$VAL_LASTACTIONON|escape}</li>
							<li><span class="glyphicon glyphicon-calendar"></span> {$smarty.const.STR_WO_ESTSTART} {$VAL_ESTSTARTON|escape}</li>
							<li><span class="glyphicon glyphicon-calendar"></span> {$smarty.const.STR_WO_ESTEND} {$VAL_ESTENDON|escape}</li>
							<li><span class="glyphicon glyphicon-calendar"></span> {$smarty.const.STR_WO_START} {$VAL_STARTON|escape}</li>
							<li><span class="glyphicon glyphicon-calendar"></span> {$smarty.const.STR_WO_ESTEND} {$VAL_ESTENDON|escape}</li>
						</ul>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						{if $VAL_CANSCORE}<div><span class="glyphicon glyphicon-signal"></span> Score: <span id="rubric-score">{if $VAL_SCORE}{$VAL_SCORE}{else}Not Scored{/if}</span>{if $PERM_SCORE} <a class="btn btn-xs btn-primary rubric-score-update" href="javascript:;" title="Score This Work Order"><span class="glyphicon glyphicon-pencil"></span></a>{/if}</div>{/if}
						{if $VAL_TAGS}<div><span class="glyphicon glyphicon-tag"></span> {dcl_tag_link value=$VAL_TAGS}</div>{/if}
						{if $VAL_HOTLIST}<div><span class="glyphicon glyphicon-fire"></span> {dcl_hotlist_link value=$VAL_HOTLIST}</div>{/if}
						{if $VAL_PROJECTS}<div class="project-item-{$VAL_JCN}-{$VAL_SEQ}">
							<h4>{$smarty.const.STR_WO_PROJECT|escape}</h4>
							{section name=project loop=$VAL_PROJECTS}
							<a href="{$VAL_MENULINK}?menuAction=Project.Detail&id={$VAL_PROJECTS[project].project_id}">[{$VAL_PROJECTS[project].project_id}] {$VAL_PROJECTS[project].name|escape}</a>{if !$smarty.section.project.last}&nbsp;/&nbsp;{/if}
							{/section}
						</div>{/if}
						{if $VAL_CONTACTID}
							<h4>{$smarty.const.STR_WO_CONTACT|escape}</h4>
							<div>
								<span class="glyphicon glyphicon-user"></span> {if $PERM_VIEWCONTACT}<a href="{$URL_MAIN_PHP}?menuAction=htmlContactDetail.show&contact_id={$VAL_CONTACTID}">{/if}{$VAL_CONTACT|escape}{if $PERM_VIEWCONTACT}</a>{/if}
								{if $VAL_CONTACTEMAIL != ""}<span class="glyphicon glyphicon-envelope"></span> {mailto address=$VAL_CONTACTEMAIL}{/if}
								{if $VAL_CONTACTPHONE != ""}<span class="glyphicon glyphicon-phone"></span> {$VAL_CONTACTPHONE|escape}{/if}
							</div>

						{/if}
						{if count($VAL_ORGS) > 0}
							<h4>{$smarty.const.STR_CMMN_ORGANIZATION} <span class="badge alert-info">{$VAL_ORGS|@count}</span></h4>
							{section name=org loop=$VAL_ORGS}
								{if $PERM_VIEWORG}<a href="{$URL_MAIN_PHP}?menuAction=Organization.Detail&org_id={$VAL_ORGS[org].org_id}">{/if}{$VAL_ORGS[org].org_name|escape}{if $PERM_VIEWORG}</a>{/if}
								{if !$smarty.section.org.last},&nbsp;{/if}
							{/section}
						{/if}
						{if $VAL_NOTES != ""}
							<h4>{$smarty.const.STR_WO_NOTES|escape}</h4>
							<p id="notes">{$VAL_NOTES|escape|dcl_link}</p>
						{/if}
						<h4>{$smarty.const.STR_WO_DESCRIPTION|escape}</h4>
						<p>{$VAL_DESCRIPTION|escape|dcl_link}</p>
						{dcl_publish topic="WorkOrder.Detail" param=$WorkOrder}
					</div>
				</div>
				</div>
			</div>
			<div class="tab-pane fade" id="files">
				{include file="AttachmentsControl.tpl"}
			</div>
			<div class="tab-pane fade" id="tasks">
				{include file="WorkOrderTasksControl.tpl"}
			</div>
		</div>
	</div>
</div>
{include file="TimeCardsControl.tpl"}
{if $PERM_SCORE && $VAL_CANSCORE}
	<div id="rubric-dialog" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" data-bind="text: name">Rubric</h4>
				</div>
				<div class="modal-body">
					<table class="table">
						<thead><tr><th>Criterion</th><th>Level 1</th><th>Level 2</th><th>Level 3</th><th>Level 4</th></tr></thead>
						<tbody id="criteria" data-bind="foreach: criteria">
						<tr data-bind="attr: { 'data-id': id, 'data-idx': $index }">
							<td data-bind="text: name"></td>
							<td class="option" data-level="1" data-bind="{ css: { 'option-selected bg-info': score() == 1, 'text-muted': score() != 1 }, click: setScore.bind($data, 1) }"><span class="glyphicon glyphicon-check"></span> <span data-bind="text: level1"></span></td>
							<td class="option" data-level="2" data-bind="{ css: { 'option-selected bg-info': score() == 2, 'text-muted': score() != 2 }, click: setScore.bind($data, 2) }"><span class="glyphicon glyphicon-check"></span> <span data-bind="text: level2"></span></td>
							<td class="option" data-level="3" data-bind="{ css: { 'option-selected bg-info': score() == 3, 'text-muted': score() != 3 }, click: setScore.bind($data, 3) }"><span class="glyphicon glyphicon-check"></span> <span data-bind="text: level3"></span></td>
							<td class="option" data-level="4" data-bind="{ css: { 'option-selected bg-info': score() == 4, 'text-muted': score() != 4 }, click: setScore.bind($data, 4) }"><span class="glyphicon glyphicon-check"></span> <span data-bind="text: level4"></span></td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button id="save-score" type="button" class="btn btn-success">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
{/if}
{/block}
{block name=script}
	{if $PERM_MODIFY_TC && $VAL_EDITTCID}
		{dcl_validator_init}
		<script type="text/javascript">
			function validateAndSubmitForm(form) {
				var aValidators = [
					new ValidatorDate(form.elements["actionon"], "{$smarty.const.STR_TC_DATE}"),
					new ValidatorSelection(form.elements["action"], "{$smarty.const.STR_TC_ACTION}"),
					new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_TC_STATUS}"),
					new ValidatorDecimal(form.elements["hours"], "{$smarty.const.STR_TC_HOURS}"),
					new ValidatorString(form.elements["summary"], "{$smarty.const.STR_WO_SUMMARY}")
				];

				for (var i in aValidators) {
					if (!aValidators[i].isValid()) {
						alert(aValidators[i].getError());
						if (typeof(aValidators[i]._Element.focus) == "function")
							aValidators[i]._Element.focus();
						return;
					}
				}

				form.submit();
			}
		</script>
	{/if}
	<script type="text/javascript" src="{$DIR_VENDOR}readmore/readmore.min.js"></script>
	<script type="text/javascript" src="{$DIR_VENDOR}knockout/knockout-3.3.0.js"></script>
	<script src="{$DIR_VENDOR}blockui/jquery.blockUI.js"></script>
	<script type="text/javascript">
		function submitAction(sFormName, sAction) {
			var oForm = document.getElementById(sFormName);
			if (!oForm)
				return;

			oForm.menuAction.value = sAction;
			oForm.submit();
		}

		$(function() {
			$.blockUI.defaults.css.border = "none";
			$.blockUI.defaults.css.padding = "15px";
			$.blockUI.defaults.css.backgroundColor = "#000";
			$.blockUI.defaults.css.borderRadius = "10px";
			$.blockUI.defaults.css.color = "#fff";

			var hash = location.hash;
			if (hash) {
				var $tabs = $('#tabs').find('a[href="' + hash + '"]');
				$tabs.tab('show');
			}

			$("#notes").readmore({
				moreLink: '<a href="javascript:;" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-collapse-down"></span> Show More...</a>',
				lessLink: '<a href="javascript:;" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-collapse-up"></span> Show Less...</a>'
			});

			$("body").on("click", "a.remove-from-project", function() {
				var $link = $(this);
				var jcn = $link.attr("data-jcn");
				var seq = $link.attr("data-seq");

				if (!confirm("Are you sure you want to remove this work order from its project?"))
					return;

				$.ajax({
					type: "POST",
					url: "{$URL_MAIN_PHP}?menuAction=Project.RemoveTask",
					data: { "jcn": jcn, "seq": seq },
					success: function() {
						$.gritter.add({ title: "Success", text: "Successfully removed from project." });
						$("div.panel").find("div.project-item-" + jcn + "-" + seq).hide("slow", function() { $(this).remove(); });
					},
					error: function() {
						$.gritter.add({ title: "Error", text: "Could not remove the work order from its project." });
					}
				});
			});
			{if $PERM_SCORE && $VAL_CANSCORE}

			var $criteria = $("#criteria");
			var $content = $("#content");
			function CriterionModel() {
				var self = this;

				self.id = ko.observable(0);
				self.name = ko.observable("");
				self.level1 = ko.observable("");
				self.level2 = ko.observable("");
				self.level3 = ko.observable("");
				self.level4 = ko.observable("");
				self.score = ko.observable(0);

				self.setScore = function(newScore) {
					self.score(newScore);
				}
			}

			var viewModel = {
				id: ko.observable(0),
				name: ko.observable(""),
				criteria: ko.observableArray([new CriterionModel()])
			};

			viewModel.getScoresForSubmit = ko.computed(function() {
				var retVal = [];
				ko.utils.arrayForEach(this.criteria(), function(item) {
					retVal.push({ criterionId: parseInt(item.id(), 10), level: parseInt(item.score(), 10) });
				});

				return retVal;
			}, viewModel);

			ko.applyBindings(viewModel, document.getElementById("rubric-dialog"));

			$content.on("click", "a.rubric-score-update", function() {
				$content.block({ message: '<h4><img src="{$DIR_IMG}ajax-loader-bar-black.gif"> Loading...</h4>' });
				$.ajax({
					type: "GET",
					url: "{$URL_MAIN_PHP}?menuAction=WorkOrderService.GetRubric",
					contentType: "application/json",
					data: { wo_id: {$VAL_JCN}, seq: {$VAL_SEQ} }
				}).done(function(data) {
					viewModel.id(data.id);
					viewModel.name(data.name);

					var criteriaArray = [];
					$.each(data.criteria, function(idx, item) {
						var model = new CriterionModel();
						model.id(item.id);
						model.name(item.name);
						model.level1(item.level1);
						model.level2(item.level2);
						model.level3(item.level3);
						model.level4(item.level4);
						model.score(item.score);

						criteriaArray.push(model);
					});

					viewModel.criteria(criteriaArray);
				}).fail(function(jqXHR, textStatus) {
					$.gritter.add({ title: "Error", text: "Could not load rubric.  " + textStatus });
				}).always(function() {
					$content.unblock();
				});

				$("#rubric-dialog").modal();
			});

			$content.on("click", "#save-score", function() {
				var $selected = $criteria.find("td.option-selected");
				var submitData = {
					id: {$VAL_JCN},
					seq: {$VAL_SEQ},
					criteria: viewModel.getScoresForSubmit()
				};

				$content.block({ message: '<h4><img src="{$DIR_IMG}ajax-loader-bar-black.gif"> Saving...</h4>' });
				$.ajax({
					type: "POST",
					url: "{$URL_MAIN_PHP}?menuAction=WorkOrder.UpdateScore",
					contentType: "application/json",
					data: JSON.stringify(submitData),
					dataType: "json"
				}).done(function(data) {
					if (data.status == "OK") {
						$.gritter.add({ title: "Success", text: "Score updated." });
						$("#rubric-score").text(data.score);
					} else {
						if (data.status && data.message) {
							$.gritter.add({ title: data.status, text: data.message });
						} else {
							$.gritter.add({ title: "Error", text: "Unrecognized response from server." });
						}
					}
				}).fail(function(jqXHR, textStatus) {
					$.gritter.add({ title: "Error", text: "Could not save rubric.  " + textStatus });
				}).always(function() {
					$content.unblock();
				});

				$("#rubric-dialog").modal("hide");
			});
			{/if}
		});
	</script>
{/block}