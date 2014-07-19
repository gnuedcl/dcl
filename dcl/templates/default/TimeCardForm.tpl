{dcl_validator_init}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{if !$IS_BATCH && (($PERM_MODIFYWORKORDER && $VAL_MULTIORG && !$PERM_ISPUBLIC) || ($PERM_ADDTASK))}{dcl_selector_init}{/if}
<form class="form-horizontal" name="NewAction" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuActionExExExExEx" value="{$VAL_MENUACTION}">
	{dcl_anti_csrf_token}
	{if $IS_BATCH}<input type="hidden" name="menuAction" value="boTimecards.dbbatchadd">
	{elseif $IS_EDIT}<input type="hidden" name="menuAction" value="boTimecards.dbmodify">
	{else}<input type="hidden" name="menuAction" value="boTimecards.dbadd">
	{/if}
	{if $VAL_ID}<input type="hidden" name="id" value="{$VAL_ID}">{/if}
	{if $VAL_RETURNTO}<input type="hidden" name="return_to" value="{$VAL_RETURNTO}">{/if}
	{if $VAL_PROJECT}<input type="hidden" name="project" value="{$VAL_PROJECT}">{/if}
	{if $VAL_JCN}<input type="hidden" name="jcn" value="{$VAL_JCN}">{/if}
	{if $VAL_SEQ}<input type="hidden" name="seq" value="{$VAL_SEQ}">{/if}
	{$VAL_VIEWFORM}
	{section name=selected loop=$VAL_SELECTED}<input type="hidden" name="selected[]" value="{$VAL_SELECTED[selected]}">{/section}
	<fieldset>
		<legend>{if $IS_BATCH}{$smarty.const.STR_TC_BATCHUPDATE|escape}{elseif $IS_EDIT}{$smarty.const.STR_TC_EDIT|escape}{else}Add New Time Card{/if}</legend>
		{dcl_form_control id=actionon controlsize=2 label=$smarty.const.STR_TC_DATE required=true}
		{dcl_input_date id=actionon value=$VAL_ACTIONON}
		{/dcl_form_control}
		{if !$PERM_ISPUBLIC && $VAL_ENABLEPUBLIC == 'Y'}
			{dcl_form_control id=is_public controlsize=10 label=$smarty.const.STR_CMMN_PUBLIC}
				<input type="checkbox" id="is_public" name="is_public" value="Y"{if $VAL_ISPUBLIC == 'Y'} checked{/if}>
			{/dcl_form_control}
		{/if}
		{dcl_form_control id=copy_me_on_notification controlsize=10 label="Copy Me on Notification"}
			<input type="checkbox" id="copy_me_on_notification" name="copy_me_on_notification" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=status controlsize=4 label=$smarty.const.STR_TC_STATUS required=!$IS_BATCH help="{if !$IS_BATCH}The current status is selected for you.  If your action put this work order in a new status, please select it.{else}If you want to change all selected work orders to the same status, please select it.{/if}"}
		{$CMB_STATUS}
		{/dcl_form_control}
		{dcl_form_control id=action controlsize=4 label=$smarty.const.STR_TC_ACTION required=true help="Select the description that best describes the action taken."}
		{dcl_select_action name="action" active=$IS_EDIT setid=$VAL_SETID}
		{/dcl_form_control}
		{dcl_form_control id=2 controlsize=2 label=$smarty.const.STR_TC_HOURS required=true help="Enter the number of hours spent on this action.  Fractional hours are allowed (i.e., 2.5 is 2 and one-half hours)."}
		{dcl_input_text id=hours maxlength=6 value=$VAL_HOURS}
		{/dcl_form_control}
		{dcl_form_control id=etchours controlsize=2 label=$smarty.const.STR_TC_ETC required=!$IS_BATCH help="{if !$IS_BATCH}Enter an estimate of how many hours remain for this work order to be completed.{else}If you want to change all selected work orders to the same ETC, enter it here.{/if}"}
		{dcl_input_text id=etchours maxlength=6 value=$VAL_ETCHOURS}
		{/dcl_form_control}
		{dcl_form_control id=summary controlsize=10 label=$smarty.const.STR_TC_SUMMARY required=true help="Enter a short summary of the work performed."}
		{dcl_input_text id=summary maxlength=100 value=$VAL_SUMMARY}
		{/dcl_form_control}
		{dcl_form_control id=description controlsize=10 label=$smarty.const.STR_TC_DESCRIPTION required=true}
			<textarea class="form-control" name="description" rows="4" wrap valign="top">{$VAL_DESCRIPTION|escape}</textarea>
		{/dcl_form_control}
	</fieldset>
{if !$IS_EDIT}
	<fieldset>
		<legend>{$smarty.const.STR_CMMN_OPTIONS}</legend>
	{if $PERM_REASSIGN}
		{dcl_form_control id=reassign controlsize=4 label=$smarty.const.STR_CMMN_REASSIGN help="You can reassign this work order to another person by selecting their user name here."}
		{$CMB_REASSIGN}
		{/dcl_form_control}
	{/if}
	{if $PERM_MODIFYWORKORDER}
		{dcl_form_control id=tags controlsize=10 label=$smarty.const.STR_CMMN_TAGS help=$smarty.const.STR_CMMN_TAGSHELP}
		{dcl_input_text id=tags value=$VAL_TAGS}
		{/dcl_form_control}
		{dcl_form_control id=hotlist controlsize=10 label=Hotlists help="Separate multiple hotlists with commas (example: \"customer critical,risk\"). Maximum 20 characters per hotlist."}
		{dcl_input_text id=hotlist value=$VAL_HOTLISTS}
		{/dcl_form_control}
	{/if}
	{if $VAL_PRODUCT && $VAL_ISVERSIONED}
		{dcl_form_control id=targeted_version_id controlsize=4 label="Targeted Version"}
		{dcl_select_product_version name=targeted_version_id active="Y" default="$VAL_TARGETED_VERSION" product="$VAL_PRODUCT"}
		{/dcl_form_control}
		{dcl_form_control id=fixed_version_id controlsize=4 label="Fixed Version"}
		{dcl_select_product_version name=fixed_version_id active="Y" default="$VAL_FIXED_VERSION" product="$VAL_PRODUCT"}
		{/dcl_form_control}
	{/if}
	{if !$IS_BATCH}
		{if $PERM_ADDTASK}
			{dcl_form_control id=projectid controlsize=10 label=$smarty.const.STR_WO_PROJECT}
			{dcl_selector_project name="projectid" value="$VAL_PROJECTS" decoded="$VAL_PROJECT"}
			{/dcl_form_control}
		{/if}
		{if $PERM_MODIFYWORKORDER && $VAL_MULTIORG && !$PERM_ISPUBLIC}
			{dcl_form_control id=secaccounts controlsize=10 label=$smarty.const.STR_CMMN_ORGANIZATION}
			{dcl_selector_org name=secaccounts value=$VAL_ORGID decoded=$VAL_ORGNAME multiple=$VAL_MULTIORG}
				<div class="noinput">
					<div id="div_secaccounts""></div>
				</div>
			{/dcl_form_control}
		{/if}
		{if $PERM_ATTACHFILE && $VAL_MAXUPLOADFILESIZE > 0}
		<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
			{dcl_form_control id=userfile controlsize=10 label=$smarty.const.STR_WO_ATTACHFILE required=true}
				<input type="file" id="userfile" name="userfile">
			{/dcl_form_control}
		{/if}
	{/if}
	</fieldset>
{/if}
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" class="inputSubmit" value="{$smarty.const.STR_CMMN_SAVE}" onclick="validateAndSubmitForm(this.form);">
				<input class="btn btn-link" type="button" class="inputSubmit" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$VAL_JCN}&seq={$VAL_SEQ}';">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_JS}/bettergrow/jquery.BetterGrow.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("textarea").BetterGrow();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
		$("input[data-input-type=date]").datepicker();
		$("#hours").on("blur", function() {
			updateEtc($(this).get(0).form);
		});

		if (typeof render_a_secaccounts == "function") {
			render_a_secaccounts();
		}

		function split(val) {
			return val.split(/,\s*/);
		}

		function extractLast( term ) {
			return split( term ).pop();
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

	function updateEtc(form)
	{
		var bUpdateEtc = {$VAL_UPDATEWOETCHOURS};
		var oValidator = new ValidatorDecimal(form.elements["hours"], "{$smarty.const.STR_TC_HOURS}");
		if (bUpdateEtc && oValidator.isValid() && form.elements["etchours"].value == "")
		{
			var fHours = {$VAL_WOETCHOURS} - form.elements["hours"].value;
			if (fHours < 0)
				fHours = 0.0;

			form.elements["etchours"].value = fHours;
		}
	}

	function validateAndSubmitForm(form)
	{

		var aValidators = [
				new ValidatorDate(form.elements["actionon"], "{$smarty.const.STR_TC_DATE}", true),
				new ValidatorSelection(form.elements["action"], "{$smarty.const.STR_TC_ACTION}"),
				{if !$IS_BATCH}new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_TC_STATUS}"),{/if}
				new ValidatorDecimal(form.elements["hours"], "{$smarty.const.STR_TC_HOURS}", true),
				new ValidatorDecimal(form.elements["etchours"], "{$smarty.const.STR_TC_ETC}", {if !$IS_BATCH}true{else}false{/if}),
				new ValidatorString(form.elements["summary"], "{$smarty.const.STR_TC_SUMMARY}")
		];

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
</script>
