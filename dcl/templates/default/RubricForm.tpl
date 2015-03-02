{dcl_validator_init}
<form id="theForm" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	{if $rubric_id}<input type="hidden" name="rubric_id" data-bind="value: id">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=rubric_name controlsize=4 label=$smarty.const.STR_CMMN_NAME required=true}
			<input class="form-control" type="text" id="rubric_name" name="rubric_name" maxlength="50" data-bind="textInput: name">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>Criteria <a id="new-criterion" class="btn btn-xs btn-success" href="javascript:;"><span class="glyphicon glyphicon-plus"></span> {$smarty.const.STR_CMMN_NEW}</a></legend>
		<table class="table">
			<thead><tr><th></th><th>Criterion</th><th>Level 1</th><th>Level 2</th><th>Level 3</th><th>Level 4</th></tr></thead>
			<tbody id="criteria" data-bind="foreach: criteria">
				<tr data-bind="attr: { 'data-id': id, 'data-idx': $index }">
					<td><a class="remove-criterion btn btn-xs btn-danger" href="javascript:;"><span class="glyphicon glyphicon-trash"></span></a></td>
					<td><textarea class="criterion-name form-control" rows="4" data-bind="textInput: name"></textarea></td>
					<td><textarea class="criterion-level1 form-control" rows="4" data-bind="textInput: level1"></textarea></td>
					<td><textarea class="criterion-level2 form-control" rows="4" data-bind="textInput: level2"></textarea></td>
					<td><textarea class="criterion-level3 form-control" rows="4" data-bind="textInput: level3"></textarea></td>
					<td><textarea class="criterion-level4 form-control" rows="4" data-bind="textInput: level4"></textarea></td>
				</tr>
			</tbody>
		</table>
		{* <pre data-bind="text: ko.toJSON($root, null, 2)"></pre> *}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" id="save" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=Rubric.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_VENDOR}knockout/knockout-3.2.0.js"></script>
<script src="{$DIR_VENDOR}blockui/jquery.blockUI.js"></script>
<script type="text/javascript">
	$(function() {
		$.blockUI.defaults.css.border = "none";
		$.blockUI.defaults.css.padding = "15px";
		$.blockUI.defaults.css.backgroundColor = "#000";
		$.blockUI.defaults.css.borderRadius = "10px";
		$.blockUI.defaults.css.color = "#fff";

		var $criteria = $("#criteria");
		var $content = $("#content");
		$("#rubric_name").focus();
		$criteria.sortable({
			update: function(event, ui) {
				var newIdx = ui.item.index();
				var oldIdx = ui.item.attr("data-idx");

				if (newIdx == oldIdx)
					return;

				ui.item.remove();

				var item = viewModel.criteria.splice(oldIdx, 1)[0];
				viewModel.criteria.splice(newIdx, 0, item);
			}
		});

		function CriterionModel() {
			var self = this;

			self.id = ko.observable(0);
			self.name = ko.observable("");
			self.level1 = ko.observable("");
			self.level2 = ko.observable("");
			self.level3 = ko.observable("");
			self.level4 = ko.observable("");
		}

		var viewModel = {
			id: ko.observable(0),
			name: ko.observable(""),
			criteria: ko.observableArray([new CriterionModel()])
		};

		ko.applyBindings(viewModel, document.getElementById("theForm"));

		{if $ViewData->rubric_id}
		$content.block({ message: '<h4><img src="{$DIR_IMG}ajax-loader-bar-black.gif"> Loading...</h4>' });
		$.ajax({
			type: "GET",
			url: "{$URL_MAIN_PHP}?menuAction=RubricService.Item",
			contentType: "application/json",
			data: { id: {$ViewData->rubric_id} }
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

				criteriaArray.push(model);
			});

			viewModel.criteria(criteriaArray);

			updateState();
		}).fail(function(jqXHR, textStatus) {
			$.gritter.add({ title: "Error", text: "Could not load rubric.  " + textStatus });
		}).always(function() {
			$content.unblock();
		});
		{/if}

		function hasEmptyCriterionItems() {
			return $criteria.find("textarea").filter(function() {
				return $.trim($(this).val()) == "";
			}).length > 0;
		}

		function hasMultipleCriteria() {
			return $criteria.find("tr").length > 1;
		}

		function updateRemoveCriterionState() {
			if (hasMultipleCriteria())
				$criteria.find("a.remove-criterion").removeClass("disabled");
			else
				$criteria.find("a.remove-criterion").addClass("disabled");
		}

		function updateNewCriterionState() {
			if (hasEmptyCriterionItems())
				$("#new-criterion").addClass("disabled");
			else
				$("#new-criterion").removeClass("disabled");
		}

		$("#new-criterion").on("click", function() {
			viewModel.criteria.push(new CriterionModel());
			$criteria.find("tr:last").find("textarea.criterion-name").focus();
			updateState();
		});

		$criteria.on("click", "a.remove-criterion", function() {
			if (!confirm("Are you sure you want to delete this criterion?")) {
				return false;
			}

			var idx = $(this).parents("tr:first").index();
			viewModel.criteria.splice(idx, 1);
			updateState();
		});

		function updateState() {
			updateNewCriterionState();
			updateRemoveCriterionState();
		}

		$criteria.on("keyup", "textarea", updateState);

		updateState();

		$("#save").on("click", function() {
			var data = ko.toJS(viewModel);

			$content.block({ message: '<h4><img src="{$DIR_IMG}ajax-loader-bar-black.gif"> Saving...</h4>' });
			$.ajax({
				type: "POST",
				url: "{$URL_MAIN_PHP}?menuAction={$menuAction}",
				contentType: "application/json",
				data: JSON.stringify(data),
				dataType: "json"
			}).done(function() {
				location.href = '{$URL_MAIN_PHP}?menuAction=Rubric.Index';
			}).fail(function(jqXHR, textStatus) {
				$.gritter.add({ title: "Error", text: "Could not save rubric.  " + textStatus });
			}).always(function() {
				$content.unblock();
			});
		});
	});
</script>
