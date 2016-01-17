{extends file="_Layout.tpl"}
{block name=title}{$ViewData->Title|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form form-horizontal" name="woform" method="post" action="{$smarty.const.DCL_WWW_ROOT}main.php" enctype="multipart/form-data">
	<input type="hidden" name="menuActionExExExExEx" value="{$ViewData->Action}">
	<input type="hidden" name="menuAction" value="{$ViewData->Action}">
	{dcl_anti_csrf_token}
	{if $ViewData->WorkOrderId}<input type="hidden" name="jcn" value="{$ViewData->WorkOrderId}">{/if}
	{if $ViewData->Sequence}<input type="hidden" name="seq" value="{$ViewData->Sequence}">{/if}
	{if $ViewData->TicketId}<input type="hidden" name="ticketid" value="{$ViewData->TicketId}">{/if}
{if $ViewData->IsEdit}{assign var=ACTIVE_ONLY value=N}{else}{assign var=ACTIVE_ONLY value=Y}{/if}
{if $ViewData->IsPublicUser}
	<input type="hidden" name="is_public" id="is_public" value="Y">
{/if}
{if $return_to}
	<input type="hidden" name="return_to" value="{$return_to}">
{/if}
	<fieldset>
		<legend>{$ViewData->Title|escape}</legend>
		{dcl_form_control id=product controlsize=4 label=$smarty.const.STR_WO_PRODUCT required=true}
			{dcl_select_product default=$ViewData->ProductId active=$ACTIVE_ONLY}
		{/dcl_form_control}
		{dcl_form_control id=module_id controlsize=4 label=$smarty.const.STR_CMMN_MODULE required=true}
			{if $ViewData->IsEdit}{dcl_select_module default=$ViewData->ModuleId active=$ACTIVE_ONLY product=$ViewData->ProductId}{else}{dcl_select_module default=$ViewData->ModuleId active=$ACTIVE_ONLY}{/if}
		{/dcl_form_control}
		{dcl_form_control id=reported_version_id controlsize=4 label="Reported Version"}
			{dcl_select_product_version name=reported_version_id active=$ACTIVE_ONLY default=$ViewData->ReportedVersionId product=$ViewData->ProductId}
		{/dcl_form_control}
{if $ViewData->IsEdit}
	{dcl_form_control id=targeted_version_id controlsize=4 label="Targeted Version"}
	{dcl_select_product_version name=targeted_version_id active=$ACTIVE_ONLY default=$ViewData->TargetedVersionId product=$ViewData->ProductId}
	{/dcl_form_control}
	{dcl_form_control id=fixed_version_id controlsize=4 label="Fixed Version"}
	{dcl_select_product_version name=fixed_version_id active=$ACTIVE_ONLY default=$ViewData->FixedVersionId product=$ViewData->ProductId}
	{/dcl_form_control}
{/if}
{if !$ViewData->IsPublicUser}
	{dcl_form_control id=is_public controlsize=1 label=$smarty.const.STR_CMMN_PUBLIC required=true}
		<input type="checkbox" name="is_public" id="is_public" value="Y"{if $ViewData->IsPublic == 'Y'} checked{/if}>
	{/dcl_form_control}
{/if}
		{dcl_form_control id=wo_type_id controlsize=4 label=$smarty.const.STR_WO_TYPE required=true}
		{dcl_select_wo_type default=$ViewData->TypeId active=$ACTIVE_ONLY}
		{/dcl_form_control}
		{dcl_form_control id=entity_source_id controlsize=4 label=$smarty.const.STR_CMMN_SOURCE required=true}
		{dcl_select_source default=$ViewData->SourceId active=$ACTIVE_ONLY}
		{/dcl_form_control}
{if $ViewData->CanAssignWorkOrder}
	{dcl_form_control id=responsible controlsize=4 label=$smarty.const.STR_WO_RESPONSIBLE required=true}
	{dcl_select_personnel name="responsible" default=$ViewData->ResponsibleId entity=$smarty.const.DCL_ENTITY_WORKORDER perm=$smarty.const.DCL_PERM_ACTION}
	{/dcl_form_control}
	{dcl_form_control id=deadlineon controlsize=2 label=$smarty.const.STR_WO_DEADLINE required=true}
		<input type="text" class="form-control" data-input-type="date" maxlength="10" id="deadlineon" name="deadlineon" value="{$ViewData->DeadlineDate|escape}">
	{/dcl_form_control}
	{dcl_form_control id=eststarton controlsize=2 label=$smarty.const.STR_WO_ESTSTART required=true}
		<input type="text" class="form-control" data-input-type="date" maxlength="10" id="eststarton" name="eststarton" value="{$ViewData->EstStartDate|escape}">
	{/dcl_form_control}
	{dcl_form_control id=estendon controlsize=2 label=$smarty.const.STR_WO_ESTEND required=true}
		<input type="text" class="form-control" data-input-type="date" maxlength="10" id="estendon" name="estendon" value="{$ViewData->EstEndDate|escape}">
	{/dcl_form_control}
	{dcl_form_control id=esthours controlsize=2 label=$smarty.const.STR_WO_ESTHOURS required=true}
		<input type="text" name="esthours" id="esthours" class="form-control" maxlength="6" value="{$ViewData->EstHours}">
	{/dcl_form_control}
{elseif $ViewData->CanAction && !$ViewData->IsPublicUser}
	{dcl_form_control id=responsible controlsize=1 label=$smarty.const.STR_WO_RESPONSIBLE}
		<input type="checkbox" class="form-control" name="responsible" id="responsible" value="{$ViewData->ResponsibleId}"{$CHK_DCLID}>
	{/dcl_form_control}
{/if}
{if $ViewData->CanAssignWorkOrder}
	{dcl_form_control id=priority controlsize=2 label=$smarty.const.STR_WO_PRIORITY required=true}
	{dcl_select_priority default=$ViewData->PriorityId active=$ACTIVE_ONLY setid=$ViewData->AttributeSetId}
	{/dcl_form_control}
	{dcl_form_control id=severity controlsize=2 label=$smarty.const.STR_WO_SEVERITY required=true}
	{dcl_select_severity default=$ViewData->SeverityId active=$ACTIVE_ONLY setid=$ViewData->AttributeSetId}
	{/dcl_form_control}
{/if}
{if !$ViewData->IsPublicUser}
	{dcl_form_control id=contact_id controlsize=2 label=$smarty.const.STR_WO_CONTACT}
	{dcl_selector_contact name="contact_id" value=$ViewData->ContactId decoded=$ViewData->ContactName orgselector="secaccounts"}
	{/dcl_form_control}
	{dcl_form_control id=secaccounts controlsize=2 label=$smarty.const.STR_CMMN_ORGANIZATION}
	{dcl_selector_org name="secaccounts" value=$ViewData->OrganizationIdCollection decoded=$ViewData->OrganizationNameCollection multiple=$ViewData->MultiOrganizationEnabled}
	{/dcl_form_control}
	<div class="col-xs-offset-2 col-xs-10">
		<div id="div_secaccounts"></div>
	</div>
{/if}
	{dcl_form_control id=summary controlsize=10 label=$smarty.const.STR_WO_SUMMARY required=true}
		<input type="text" class="form-control" name="summary" id="summary" maxlength="100" value="{$ViewData->Summary|escape}">
	{/dcl_form_control}
	{dcl_form_control id=tags controlsize=10 label=$smarty.const.STR_CMMN_TAGS}
		<input type="text" class="form-control" name="tags" id="tags" value="{$ViewData->Tags|escape}">
		<span class="help-block">{$smarty.const.STR_CMMN_TAGSHELP|escape}</span>
	{/dcl_form_control}
	{dcl_form_control id=tags controlsize=10 label=Hotlist}
		<input type="text" class="form-control" name="hotlist" id="hotlist" value="{$ViewData->Hotlists|escape}">
		<span class="help-block">Separate multiple hotlists with commas (example: "customer critical,risk"). Maximum 20 characters per hotlist.</span>
	{/dcl_form_control}
	{dcl_form_control id=notes controlsize=10 label=$smarty.const.STR_WO_NOTES}
		<textarea class="form-control" name="notes" id="notes" rows="4" wrap valign="top">{$ViewData->Notes|escape}</textarea>
	{/dcl_form_control}
	{dcl_form_control id=description controlsize=10 label=$smarty.const.STR_WO_DESCRIPTION required=true}
		<textarea class="form-control" name="description" id="description" rows="4" wrap valign="top">{$ViewData->Description|escape}</textarea>
	{/dcl_form_control}
	{dcl_form_control id=copy_me_on_notification controlsize=1 label="Copy Me on Notification"}
		<input type="checkbox" id="copy_me_on_notification" name="copy_me_on_notification" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
	{/dcl_form_control}
{if $ViewData->CanAddTask}
{if $ViewData->ProjectLabel}
	{dcl_form_control id=projectid controlsize=10 label=$smarty.const.STR_WO_PROJECT}
		<p class="form-control-static">{$ViewData->ProjectLabel|escape}<input type="hidden" name="projectid" value="{$ViewData->ProjectId}"></p>
	{/dcl_form_control}
{elseif !$ViewData->HideProject}
	{dcl_form_control id=projectid controlsize=10 label=$smarty.const.STR_WO_PROJECT}
		{dcl_input_text id=projectid}
	{/dcl_form_control}
	{dcl_form_control id=addall controlsize=1 label=$smarty.const.STR_WO_ADDALLSEQ}
		<input type="checkbox" id="addall" name="addall" value="1">
	{/dcl_form_control}
{/if}
{/if}
{if $ViewData->CanAttachFile}
	<input type="hidden" name="MAX_FILE_SIZE" value="{$ViewData->MaxUploadFileSize}">
	{dcl_form_control id=userfile controlsize=10 label=$smarty.const.STR_WO_ATTACHFILE}
		<input type="file" id="userfile" name="userfile">
	{/dcl_form_control}
{/if}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="button" class="btn btn-primary" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="reset" class="btn btn-link" value="{$smarty.const.STR_CMMN_RESET}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
<script type="text/javascript" src="{$DIR_VENDOR}bettergrow/jquery.BetterGrow.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
{dcl_selector_init}
{dcl_validator_init}
{dcl_xmlhttp_init}
<script type="text/javascript">
	var productVersionRequired = false;
	function validateAndSubmitForm(form)
	{
		var aValidators = [
			new ValidatorInteger(form.elements["jcn"], "{$smarty.const.STR_WO_JCN}"),
			new ValidatorSelection(form.elements["product"], "{$smarty.const.STR_WO_PRODUCT}"),
			new ValidatorSelection(form.elements["module_id"], "{$smarty.const.STR_CMMN_MODULE}"),
			new ValidatorSelection(form.elements["wo_type_id"], "{$smarty.const.STR_WO_TYPE}"),
			new ValidatorSelection(form.elements["entity_source_id"], "{$smarty.const.STR_CMMN_SOURCE}"),
			new ValidatorInteger(form.elements["responsible"], "{$smarty.const.STR_WO_RESPONSIBLE}", true),
			new ValidatorDate(form.elements["deadlineon"], "{$smarty.const.STR_WO_DEADLINE}", true),
			new ValidatorDate(form.elements["eststarton"], "{$smarty.const.STR_WO_ESTSTART}", true),
			new ValidatorDate(form.elements["estendon"], "{$smarty.const.STR_WO_ESTEND}", true),
			new ValidatorDecimal(form.elements["esthours"], "{$smarty.const.STR_WO_ESTHOURS}", true),
			new ValidatorSelection(form.elements["priority"], "{$smarty.const.STR_WO_PRIORITY}"),
			new ValidatorSelection(form.elements["severity"], "{$smarty.const.STR_WO_SEVERITY}"),
			new ValidatorString(form.elements["summary"], "{$smarty.const.STR_WO_SUMMARY}"),
			new ValidatorString(form.elements["description"], "{$smarty.const.STR_WO_DESCRIPTION}")
		];

		if (productVersionRequired)
			aValidators.push(new ValidatorInteger(form.elements["projectid"], "{$smarty.const.STR_WO_PROJECT}", true));
		for (var i in aValidators)
		{
			if (!aValidators[i].isValid())
			{
				alert(aValidators[i].getError());
				if (typeof(aValidators[i]._Element.focus) == "function")
					aValidators[i]._Element.focus();
				return;
			}
		}

		form.submit();
	}

	function UpdateVersionsCallback(aItems)
	{
		if (typeof(aItems) != "object" || !aItems.data || !aItems.data.length)
			return;

		$("#reported_version_id,#targeted_version_id,#fixed_version_id").removeAttr("disabled");
		var aNames = ["reported_version_id", "targeted_version_id", "fixed_version_id"];
		for (var i in aNames)
		{
			var oSelect = document.getElementById(aNames[i]);
			if (oSelect)
			{
				var id = $("#" + aNames[i]).val();
				oSelect.options.length = 1;
				for (var i = 0; i < aItems.data.length; i++)
				{
					var o = new Option();
					o.value = aItems.data[i].id;
					o.text = aItems.data[i].text;

					if (o.value == id)
						o.selected = true;

					oSelect.options[oSelect.options.length] = o;
				}
			}
		}
	}

	function IsProjectRequiredCallback(aItems)
	{
		if (typeof(aItems) != "object" || !aItems.data || !aItems.data.length)
		{
			productVersionRequired = false;
			$("label[for=projectid]").parent().removeClass("required");
			return;
		}

		productVersionRequired = (aItems.data[0].is_project_required == "Y");
		if (productVersionRequired) {
			$("label[for=projectid]").parent().addClass("required");
		} else {
			$("label[for=projectid]").parent().removeClass("required");
		}
	}

	function UpdateOptions()
	{
		$("#reported_version_id,#targeted_version_id,#fixed_version_id").attr("disabled", "disabled");

		var oProduct = document.getElementById("product");
		if (oProduct == null || oProduct.selectedIndex == 0)
			return;
		RequestJSON("{$smarty.const.DCL_WWW_ROOT}main.php", "menuAction=Product.ListVersions{if !$ViewData->IsEdit}&active=Y{/if}&product_id=" + oProduct.options[oProduct.selectedIndex].value, UpdateVersionsCallback);
		RequestJSON("{$smarty.const.DCL_WWW_ROOT}main.php", "menuAction=Product.IsProjectRequired&product_id=" + oProduct.options[oProduct.selectedIndex].value, IsProjectRequiredCallback);
	}

	$(document).ready(function() {
		$("#product").change(function() {
			productSelChange(this.form);
			UpdateOptions();
		});

		$("input[data-input-type=date]").datepicker();

		if ($("#product").val() != "0") {
			$("#product").change();
		}

		$("textarea").BetterGrow();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });

		$("#projectid").select2({
			multiple: false,
			maximumSelectionSize: 1,
			minimumInputLength: 2,
			tags: [],
			ajax: {
				url: "{$URL_MAIN_PHP}?menuAction=ProjectService.Autocomplete",
				dataType: "json",
				type: "GET",
				data: function(term) {
					return { term: term };
				},
				results: function(data) {
					return {
						results: $.map(data, function(item) {
							return { id: item.id, text: item.label };
						})
					};
				}
			}
		});

		if (typeof render_a_secaccounts == "function") {
			render_a_secaccounts();
		}

		function split(val) {
			return val.split(/,\s*/);
		}

		function extractLast(term) {
			return split(term).pop();
		}

		$("input#tags")
			.bind("keydown", function(event) {
				if (event.keyCode === $.ui.keyCode.TAB && $(this).data("autocomplete").menu.active) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 2,
				source: function( request, response ) {
					$.getJSON("{$URL_MAIN_PHP}?menuAction=Tag.Autocomplete", { term: extractLast(request.term) }, response);
				},
				search: function() {
					var term = extractLast(this.value);
					if (term.length < 2) {
						return false;
					}
				},
				focus: function() {
					return false;
				},
				select: function(event, ui) {
					var terms = split(this.value);
					terms.pop();
					terms.push(ui.item.value);
					terms.push("");
					this.value = terms.join(", ");
					return false;
				}
			});

		$("input#hotlist")
			.bind("keydown", function(event) {
				if (event.keyCode === $.ui.keyCode.TAB && $(this).data("autocomplete").menu.active) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 2,
				source: function( request, response ) {
					$.getJSON("{$URL_MAIN_PHP}?menuAction=Hotlist.Autocomplete", { term: extractLast(request.term) }, response);
				},
				search: function() {
					var term = extractLast(this.value);
					if (term.length < 2) {
						return false;
					}
				},
				focus: function() {
					return false;
				},
				select: function(event, ui) {
					var terms = split(this.value);
					terms.pop();
					terms.push(ui.item.value);
					terms.push("");
					this.value = terms.join(", ");
					return false;
				}
			});
	});
</script>
{/block}