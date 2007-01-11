<!-- $Id$ -->
{dcl_calendar_init}
{dcl_selector_init}
{dcl_validator_init}
<script language="JavaScript">
{literal}
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
<form class="styled" name="woform" method="post" action="{$smarty.const.DCL_WWW_ROOT}main.php" enctype="multipart/form-data">
	<input type="hidden" name="menuActionExExExExEx" value="{$VAL_MENUACTION}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{if $VAL_WOID}<input type="hidden" name="jcn" value="{$VAL_WOID}">{/if}
	{if $VAL_SEQ}<input type="hidden" name="seq" value="{$VAL_SEQ}">{/if}
	{if $VAL_TICKETID}<input type="hidden" name="ticketid" value="{$VAL_TICKETID}">{/if}
{if $IS_EDIT}{assign var=ACTIVE_ONLY value=N}{else}{assign var=ACTIVE_ONLY value=Y}{/if}
{if $PERM_ISPUBLICUSER}
	<input type="hidden" name="is_public" id="is_public" value="Y">
{/if}
{if $return_to}
	<input type="hidden" name="return_to" value="{$return_to}">
{/if}
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
		<div class="required">
			<label for="product">{$smarty.const.STR_WO_PRODUCT}:</label>
			{dcl_select_product default="$VAL_PRODUCT" active="$ACTIVE_ONLY" onchange="productSelChange(this.form);"}
		</div>
		<div class="required">
			<label for="module_id">{$smarty.const.STR_CMMN_MODULE}:</label>
			{if $IS_EDIT}{dcl_select_module default="$VAL_MODULE" active="$ACTIVE_ONLY" product="$VAL_PRODUCT"}{else}{dcl_select_module default="$VAL_MODULE" active="$ACTIVE_ONLY"}{/if}
		</div>
{if !$PERM_ISPUBLICUSER}
		<div class="required">
			<label for="is_public">{$smarty.const.STR_CMMN_PUBLIC}:</label>
			<input type="checkbox" name="is_public" id="is_public" value="Y"{if $VAL_ISPUBLIC == 'Y'} checked{/if}>
		</div>
{/if}
		<div class="required">
			<label for="wo_type_id">{$smarty.const.STR_WO_TYPE}:</label>
			{dcl_select_wo_type default="$VAL_TYPE" active="$ACTIVE_ONLY"}
		</div>
		<div class="required">
			<label for="entity_source_id">{$smarty.const.STR_CMMN_SOURCE}:</label>
			{dcl_select_source default="$VAL_SOURCE" active="$ACTIVE_ONLY"}
		</div>
{if $PERM_ASSIGNWO}
		<div class="required">
			<label for="responsible">{$smarty.const.STR_WO_RESPONSIBLE}:</label>
			{dcl_select_personnel name="responsible" default="$VAL_RESPONSIBLE" entity=$smarty.const.DCL_ENTITY_WORKORDER perm=$smarty.const.DCL_PERM_ACTION}
		</div>
		<div class="required">
			<label for="deadlineon">{$smarty.const.STR_WO_DEADLINE}:</label>
			{dcl_calendar name="deadlineon" value="$VAL_DEADLINEON"}
		</div>
		<div class="required">
			<label for="eststarton">{$smarty.const.STR_WO_ESTSTART}:</label>
			{dcl_calendar name="eststarton" value="$VAL_ESTSTARTON"}
		</div>
		<div class="required">
			<label for="estendon">{$smarty.const.STR_WO_ESTEND}:</label>
			{dcl_calendar name="estendon" value="$VAL_ESTENDON"}
		</div>
		<div class="required">
			<label for="esthours">{$smarty.const.STR_WO_ESTHOURS}:</label>
			<input type="text" name="esthours" size="6" maxlength="6" value="{$VAL_ESTHOURS}">
		</div>
{elseif $PERM_ACTION && !$PERM_ISPUBLICUSER}
		<div>
			<label for="responsible">{$smarty.const.STR_WO_RESPONSIBLE}:</label>
			<input type="checkbox" name="responsible" id="responsible" value="{$VAL_DCLID}"{$CHK_DCLID}>
		</div>
{/if}
		<div>
			<label for="revision">{$smarty.const.STR_WO_REVISION}:</label>
			<input type="text" name="revision" size="20" maxlength="20" value="{$VAL_REVISION|escape}">
		</div>
{if $PERM_ASSIGNWO}
		<div class="required">
			<label for="priority">{$smarty.const.STR_WO_PRIORITY}:</label>
			{dcl_select_priority default="$VAL_PRIORITY" active="$ACTIVE_ONLY" setid="$VAL_SETID"}
		</div>
		<div class="required">
			<label for="severity">{$smarty.const.STR_WO_SEVERITY}:</label>
			{dcl_select_severity default="$VAL_SEVERITY" active="$ACTIVE_ONLY" setid="$VAL_SETID"}
		</div>
	</tr>
{/if}
{if !$PERM_ISPUBLICUSER}
		<div>
			<label for="secaccounts">{$smarty.const.STR_CMMN_ORGANIZATION}:</label>
			{dcl_selector_org name="secaccounts" value="$VAL_ORGID" decoded="$VAL_ORGNAME" multiple="$VAL_MULTIORG"}
		</div>
		<div class="noinput">
			<div id="div_secaccounts" style="width: 100%;"><script language="JavaScript">render_a_secaccounts();</script></div>
		</div>
		<div>
			<label for="contact_id">{$smarty.const.STR_WO_CONTACT}:</label>
			{dcl_selector_contact name="contact_id" value="$VAL_CONTACTID" decoded="$VAL_CONTACTNAME"}
		</div>
	</tr>
{/if}
		<div class="required">
			<label for="summary">{$smarty.const.STR_WO_SUMMARY}:</label>
			<input type="text" name="summary" size="60" maxlength="100" value="{$VAL_SUMMARY|escape}">
		</div>
		<div>
			<label for="tags">{$smarty.const.STR_CMMN_TAGS|escape}:</label>
			<input type="text" name="tags" id="tags" size="60" value="{$VAL_TAGS|escape}">
			<span>{$smarty.const.STR_CMMN_TAGSHELP|escape}</span>
		</div>
		<div>
			<label for="notes">{$smarty.const.STR_WO_NOTES}:</label>
			<textarea name="notes" rows="4" cols="70" wrap valign="top">{$VAL_NOTES|escape}</textarea>
		</div>
		<div class="required">
			<label for="description">{$smarty.const.STR_WO_DESCRIPTION}:</label>
			<textarea name="description" rows="4" cols="70" wrap valign="top">{$VAL_DESCRIPTION|escape}</textarea>
		</div>
		<div class="required">
			<label for="copy_me_on_notification">Copy Me on Notification:</label>
			<input type="checkbox" id="copy_me_on_notification" name="copy_me_on_notification" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
		</div>
{if $PERM_ADDTASK}
{if $TXT_WILLBEPARTOFPROJECT}
		<div class="noinput">{$TXT_WILLBEPARTOFPROJECT}<input type="hidden" name="projectid" value="{$VAL_PROJECTS}"></div>
{elseif !$VAL_HIDEPROJECT}
		<div>
			<label for="projectid">{$smarty.const.STR_WO_PROJECT}:</label>
			{dcl_selector_project name="projectid" value="$VAL_PROJECTS"}
		</div>
		<div>
			<label for="addall">{$smarty.const.STR_WO_ADDALLSEQ}</label>
			<input type="checkbox" name="addall" id="addall" value="1">
		</div>
{/if}
{/if}
{if $PERM_ATTACHFILE}
		<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
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
