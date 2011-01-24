<!-- $Id$ -->
{dcl_calendar_init}
{dcl_validator_init}
{if !$IS_BATCH && (($PERM_MODIFYWORKORDER && $VAL_MULTIORG && !$PERM_ISPUBLIC) || ($PERM_ADDTASK))}{dcl_selector_init}{/if}
<script language="JavaScript">
{literal}
function updateEtc(form)
{
	{/literal}var bUpdateEtc = {$VAL_UPDATEWOETCHOURS};
	var oValidator = new ValidatorDecimal(form.elements["hours"], "{$smarty.const.STR_TC_HOURS}");{literal}
	if (bUpdateEtc && oValidator.isValid() && form.elements["etchours"].value == "")
	{
		{/literal}var fHours = {$VAL_WOETCHOURS} - form.elements["hours"].value;{literal}
		if (fHours < 0)
			fHours = 0.0;

		form.elements["etchours"].value = fHours;
	}
}

function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorDate(form.elements["actionon"], "{$smarty.const.STR_TC_DATE}", true),
			new ValidatorSelection(form.elements["action"], "{$smarty.const.STR_TC_ACTION}"),
			{if !$IS_BATCH}new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_TC_STATUS}"),{/if}
			new ValidatorDecimal(form.elements["hours"], "{$smarty.const.STR_TC_HOURS}", true),
			new ValidatorDecimal(form.elements["etchours"], "{$smarty.const.STR_TC_ETC}", {if !$IS_BATCH}true{else}false{/if}),
			new ValidatorString(form.elements["summary"], "{$smarty.const.STR_TC_SUMMARY}")
		);
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
{/literal}
</script>
<form class="styled" name="NewAction" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuActionExExExExEx" value="{$VAL_MENUACTION}">
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
		<legend>{if $IS_BATCH}{$smarty.const.STR_TC_BATCHUPDATE}{elseif $IS_EDIT}{$smarty.const.STR_TC_EDIT}{else}Add New Time Card{/if}</legend>
		<div class="required">
			<label for="actionon">{$smarty.const.STR_TC_DATE}:</label>
			{dcl_calendar name="actionon" value="$VAL_ACTIONON"}
		</div>
		{if !$PERM_ISPUBLIC && $VAL_ENABLEPUBLIC == 'Y'}
		<div class="required">
			<label for="is_public">{$smarty.const.STR_CMMN_PUBLIC}:</label>
			<input type="checkbox" id="is_public" name="is_public" value="Y"{if $VAL_ISPUBLIC == 'Y'} checked{/if}>
		</div>
		{/if}
		<div class="required">
			<label for="copy_me_on_notification">Copy Me on Notification:</label>
			<input type="checkbox" id="copy_me_on_notification" name="copy_me_on_notification" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
		</div>
		<div{if !$IS_BATCH} class="required"{/if}>
			<label for="status">{$smarty.const.STR_TC_STATUS}:</label>
			{$CMB_STATUS}
			<span>{if !$IS_BATCH}The current status is selected for you.  If your action put this work order in a new status, please select it.{else}If you want to change all selected work orders to the same status, please select it.{/if}</span>
		</div>
		<div class="required">
			<label for="action">{$smarty.const.STR_TC_ACTION}:</label>
			{dcl_select_action name="action" active=$IS_EDIT setid=$VAL_SETID}
			<span>Select the description that best describes the action taken.</span>
		</div>
		<div class="required">
			<label for="hours">{$smarty.const.STR_TC_HOURS}:</label>
			<input type="text" name="hours" size="6" maxlength="6" value="{$VAL_HOURS}" onblur="javascript:updateEtc(this.form)">
			<span>Enter the number of hours spent on this action.  Fractional hours are allowed (i.e., 2.5 is 2 and one-half hours).</span>
		</div>
		<div{if !$IS_BATCH} class="required"{/if}>
			<label for="etchours">{$smarty.const.STR_TC_ETC}:</label>
			<input type="text" name="etchours" size="6" maxlength="6" value="{$VAL_ETCHOURS}">
			<span>{if !$IS_BATCH}Enter an estimate of how many hours remain for this work order to be completed.{else}If you want to change all selected work orders to the same ETC, enter it here.{/if}</span>
		</div>
		<div class="required">
			<label for="summary">{$smarty.const.STR_TC_SUMMARY}:</label>
			<input type="text" name="summary" size="50" maxlength="100" value="{$VAL_SUMMARY|escape}">
			<span>Enter a short summary of the work performed.</span>
		</div>
		<div>
			<label for="description">{$smarty.const.STR_TC_DESCRIPTION}:</label>
			<textarea name="description" rows="4" cols="50" wrap valign="top">{$VAL_DESCRIPTION|escape}</textarea>
		</div>
	</fieldset>
{if !$IS_EDIT}
	<fieldset>
		<legend>{$smarty.const.STR_CMMN_OPTIONS}</legend>
	{if $PERM_REASSIGN}
		<div>
			<label for="reassign">{$smarty.const.STR_CMMN_REASSIGN}:</label>
			{$CMB_REASSIGN}
			<span>You can reassign this work order to another person by selecting their user name here.</span>
		</div>
	{/if}
	{if $PERM_MODIFYWORKORDER}
	<div>
		<label for="tags">{$smarty.const.STR_CMMN_TAGS|escape}:</label>
		<input type="text" name="tags" id="tags" size="60" value="{$VAL_TAGS|escape}">
		<span>{$smarty.const.STR_CMMN_TAGSHELP|escape}</span>
	</div>
	<div>
		<label for="hotlist">Hotlists:</label>
		<input type="text" name="hotlist" id="hotlist" size="60" value="{$VAL_HOTLISTS|escape}">
		<span>Separate multiple hotlists with commas (example: "customer critical,risk"). Maximum 20 characters per hotlist.</span>
	</div>
	{/if}
	{if $VAL_PRODUCT && $VAL_ISVERSIONED}
	<div>
		<label for="revision">Targeted Version:</label>
		{dcl_select_product_version name=targeted_version_id active="Y" default="$VAL_TARGETED_VERSION" product="$VAL_PRODUCT"}
	</div>
	<div>
		<label for="revision">Fixed Version:</label>
		{dcl_select_product_version name=fixed_version_id active="Y" default="$VAL_FIXED_VERSION" product="$VAL_PRODUCT"}
	</div>
	{/if}
	{if !$IS_BATCH}
		{if $PERM_ADDTASK}
		<div>
			<label for="projectid">{$smarty.const.STR_WO_PROJECT}:</label>
			{dcl_selector_project name="projectid" value="$VAL_PROJECTS" decoded="$VAL_PROJECT"}
		</div>
		{/if}
		{if $PERM_MODIFYWORKORDER && $VAL_MULTIORG && !$PERM_ISPUBLIC}
		<div>
			<label for="secaccounts">{$smarty.const.STR_CMMN_ORGANIZATION}:</label>
			{dcl_selector_org name="secaccounts" value="$VAL_ORGID" decoded="$VAL_ORGNAME" multiple="$VAL_MULTIORG"}
		</div>
		<div class="noinput">
			<div id="div_secaccounts" style="width: 100%;"><script language="JavaScript">render_a_secaccounts();</script></div>
		</div>
		{/if}
		{if $PERM_ATTACHFILE && $VAL_MAXUPLOADFILESIZE > 0}
		<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
		<div>
			<label for="userfile">{$smarty.const.STR_WO_ATTACHFILE}:</label>
			<input type="file" id="userfile" name="userfile">
		</div>
		{/if}
	{/if}
	</fieldset>
{/if}
	<fieldset>
		<div class="submit">
			<input type="button" class="inputSubmit" value="{$smarty.const.STR_CMMN_SAVE}" onclick="validateAndSubmitForm(this.form);">
			<input type="button" class="inputSubmit" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=boWorkorders.viewjcn&jcn={$VAL_JCN}&seq={$VAL_SEQ}';">
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_JS}/bettergrow/jquery.BetterGrow.min.js"></script>
<script type="text/javascript">
	//<![CDATA[{literal}
	$(document).ready(function() {
		$("textarea").BetterGrow();

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
