{dcl_calendar_init}
{dcl_selector_init}
{dcl_validator_init}
{dcl_xmlhttp_init}
<script language="JavaScript">
{literal}
var productVersionRequired = false;
function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
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
		);
		
	if (productVersionRequired)
		aValidators.push(new ValidatorInteger(form.elements["projectid"], "{$smarty.const.STR_WO_PROJECT}", true));
{literal}
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
	
	var aNames = ["reported_version_id", "targeted_version_id", "fixed_version_id"];
	for (var i in aNames)
	{
		var oSelect = document.getElementById(aNames[i]);
		if (oSelect)
		{
			oSelect.disabled = false;
			oSelect.options.length = 1;
			for (var i = 0; i < aItems.data.length; i++)
			{
				var o = new Option();
				o.value = aItems.data[i].id;
				o.text = aItems.data[i].text;
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
	var aNames = ["reported_version_id", "targeted_version_id", "fixed_version_id"];
	for (var i in aNames)
	{
		var oSelect = document.getElementById(aNames[i]);
		if (oSelect)
		{
			oSelect.options.length = 1;
			oSelect.disabled = true;
		}
	}

	var oProduct = document.getElementById("product");
	if (oProduct == null || oProduct.selectedIndex == 0)
		return;
{/literal}
	RequestJSON("{$smarty.const.DCL_WWW_ROOT}main.php", "menuAction=Product.ListVersions{if !$ViewData->IsEdit}&active=Y{/if}&product_id=" + oProduct.options[oProduct.selectedIndex].value, UpdateVersionsCallback);
	RequestJSON("{$smarty.const.DCL_WWW_ROOT}main.php", "menuAction=Product.IsProjectRequired&product_id=" + oProduct.options[oProduct.selectedIndex].value, IsProjectRequiredCallback);
{literal}
}
{/literal}
</script>
<form class="styled" name="woform" method="post" action="{$smarty.const.DCL_WWW_ROOT}main.php" enctype="multipart/form-data">
	<input type="hidden" name="menuActionExExExExEx" value="{$ViewData->Action}">
	<input type="hidden" name="menuAction" value="{$ViewData->Action}">
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
		<legend>{$ViewData->Title}</legend>
		<div class="required">
			<label for="product">{$smarty.const.STR_WO_PRODUCT}:</label>
			{dcl_select_product default="`$ViewData->ProductId`" active="$ACTIVE_ONLY"}
		</div>
		<div class="required">
			<label for="module_id">{$smarty.const.STR_CMMN_MODULE}:</label>
			{if $ViewData->IsEdit}{dcl_select_module default="`$ViewData->ModuleId`" active="$ACTIVE_ONLY" product="`$ViewData->ProductId`"}{else}{dcl_select_module default="`$ViewData->ModuleId`" active="$ACTIVE_ONLY"}{/if}
		</div>
		<div>
			<label for="revision">Reported Version:</label>
			{dcl_select_product_version name=reported_version_id active="$ACTIVE_ONLY" default="`$ViewData->ReportedVersionId`" product="`$ViewData->ProductId`"}
		</div>
{if $ViewData->IsEdit}
		<div>
			<label for="revision">Targeted Version:</label>
			{dcl_select_product_version name=targeted_version_id active="$ACTIVE_ONLY" default="`$ViewData->TargetedVersionId`" product="`$ViewData->ProductId`"}
		</div>
		<div>
			<label for="revision">Fixed Version:</label>
			{dcl_select_product_version name=fixed_version_id active="$ACTIVE_ONLY" default="`$ViewData->FixedVersionId`" product="`$ViewData->ProductId`"}
		</div>
{/if}
{if !$ViewData->IsPublicUser}
		<div class="required">
			<label for="is_public">{$smarty.const.STR_CMMN_PUBLIC}:</label>
			<input type="checkbox" name="is_public" id="is_public" value="Y"{if $ViewData->IsPublic == 'Y'} checked{/if}>
		</div>
{/if}
		<div class="required">
			<label for="wo_type_id">{$smarty.const.STR_WO_TYPE}:</label>
			{dcl_select_wo_type default="`$ViewData->TypeId`" active="$ACTIVE_ONLY"}
		</div>
		<div class="required">
			<label for="entity_source_id">{$smarty.const.STR_CMMN_SOURCE}:</label>
			{dcl_select_source default="`$ViewData->SourceId`" active="$ACTIVE_ONLY"}
		</div>
{if $ViewData->CanAssignWorkOrder}
		<div class="required">
			<label for="responsible">{$smarty.const.STR_WO_RESPONSIBLE}:</label>
			{dcl_select_personnel name="responsible" default="`$ViewData->ResponsibleId`" entity=$smarty.const.DCL_ENTITY_WORKORDER perm=$smarty.const.DCL_PERM_ACTION}
		</div>
		<div class="required">
			<label for="deadlineon">{$smarty.const.STR_WO_DEADLINE}:</label>
			{dcl_calendar name="deadlineon" value="`$ViewData->DeadlineDate`"}
		</div>
		<div class="required">
			<label for="eststarton">{$smarty.const.STR_WO_ESTSTART}:</label>
			{dcl_calendar name="eststarton" value="`$ViewData->EstStartDate`"}
		</div>
		<div class="required">
			<label for="estendon">{$smarty.const.STR_WO_ESTEND}:</label>
			{dcl_calendar name="estendon" value="`$ViewData->EstEndDate`"}
		</div>
		<div class="required">
			<label for="esthours">{$smarty.const.STR_WO_ESTHOURS}:</label>
			<input type="text" name="esthours" size="6" maxlength="6" value="{$ViewData->EstHours}">
		</div>
{elseif $ViewData->CanAction && !$ViewData->IsPublicUser}
		<div>
			<label for="responsible">{$smarty.const.STR_WO_RESPONSIBLE}:</label>
			<input type="checkbox" name="responsible" id="responsible" value="{$ViewData->ResponsibleId}"{$CHK_DCLID}>
		</div>
{/if}
{if $ViewData->CanAssignWorkOrder}
		<div class="required">
			<label for="priority">{$smarty.const.STR_WO_PRIORITY}:</label>
			{dcl_select_priority default="`$ViewData->PriorityId`" active="$ACTIVE_ONLY" setid="`$ViewData->AttributeSetId`"}
		</div>
		<div class="required">
			<label for="severity">{$smarty.const.STR_WO_SEVERITY}:</label>
			{dcl_select_severity default="`$ViewData->SeverityId`" active="$ACTIVE_ONLY" setid="`$ViewData->AttributeSetId`"}
		</div>
	</tr>
{/if}
{if !$ViewData->IsPublicUser}
		<div>
			<label for="contact_id">{$smarty.const.STR_WO_CONTACT}:</label>
			{dcl_selector_contact name="contact_id" value="`$ViewData->ContactId`" decoded="`$ViewData->ContactName`" orgselector="secaccounts"}
		</div>
		<div>
			<label for="secaccounts">{$smarty.const.STR_CMMN_ORGANIZATION}:</label>
			{dcl_selector_org name="secaccounts" value="`$ViewData->OrganizationIdCollection`" decoded="`$ViewData->OrganizationNameCollection`" multiple="`$ViewData->MultiOrganizationEnabled`"}
		</div>
		<div class="noinput">
			<div id="div_secaccounts" style="width: 100%;"><script language="JavaScript">render_a_secaccounts();</script></div>
		</div>
	</tr>
{/if}
		<div class="required">
			<label for="summary">{$smarty.const.STR_WO_SUMMARY}:</label>
			<input type="text" name="summary" size="60" maxlength="100" value="{$ViewData->Summary|escape}">
		</div>
		<div>
			<label for="tags">{$smarty.const.STR_CMMN_TAGS|escape}:</label>
			<input type="text" name="tags" id="tags" size="60" value="{$ViewData->Tags|escape}">
			<span>{$smarty.const.STR_CMMN_TAGSHELP|escape}</span>
		</div>
		<div>
			<label for="hotlist">Hotlist:</label>
			<input type="text" name="hotlist" id="hotlist" size="60" value="{$ViewData->Hotlists|escape}">
			<span>Separate multiple hotlists with commas (example: "customer critical,risk"). Maximum 20 characters per hotlist.</span>
		</div>
		<div>
			<label for="notes">{$smarty.const.STR_WO_NOTES}:</label>
			<textarea name="notes" rows="4" cols="70" wrap valign="top">{$ViewData->Notes|escape}</textarea>
		</div>
		<div class="required">
			<label for="description">{$smarty.const.STR_WO_DESCRIPTION}:</label>
			<textarea name="description" rows="4" cols="70" wrap valign="top">{$ViewData->Description|escape}</textarea>
		</div>
		<div class="required">
			<label for="copy_me_on_notification">Copy Me on Notification:</label>
			<input type="checkbox" id="copy_me_on_notification" name="copy_me_on_notification" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
		</div>
{if $ViewData->CanAddTask}
{if $ViewData->ProjectLabel}
		<div class="noinput">{$ViewData->ProjectLabel|escape}<input type="hidden" name="projectid" value="{$ViewData->ProjectId}"></div>
{elseif !$ViewData->HideProject}
		<div>
			<label for="projectid">{$smarty.const.STR_WO_PROJECT}:</label>
			{dcl_selector_project name="projectid" value="`$ViewData->ProjectId`" decoded="`$ViewData->ProjectName`"}
		</div>
		<div>
			<label for="addall">{$smarty.const.STR_WO_ADDALLSEQ}</label>
			<input type="checkbox" name="addall" id="addall" value="1">
		</div>
{/if}
{/if}
{if $ViewData->CanAttachFile}
		<input type="hidden" name="MAX_FILE_SIZE" value="{$ViewData->MaxUploadFileSize}">
		<div>
			<label for="userfile">{$smarty.const.STR_WO_ATTACHFILE}:</label>
			<input type="file" id="userfile" name="userfile">
		</div>
{/if}
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_JS}/bettergrow/jquery.BetterGrow.min.js"></script>
<script type="text/javascript">
	//<![CDATA[{literal}
	$(document).ready(function() {
		$("#product").change(function() {
			productSelChange(this.form);
			UpdateOptions();
		});

		if ($("#product").val() != "0") {
			$("#product").change();
		}

		$("textarea").BetterGrow();

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
					$.getJSON("{/literal}{$URL_MAIN_PHP}{literal}?menuAction=Tag.Autocomplete", { term: extractLast(request.term) }, response);
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
					$.getJSON("{/literal}{$URL_MAIN_PHP}{literal}?menuAction=Hotlist.Autocomplete", { term: extractLast(request.term) }, response);
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
	//]]>{/literal}
</script>