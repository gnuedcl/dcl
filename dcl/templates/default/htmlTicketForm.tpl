<!-- $Id$ -->
{dcl_calendar_init}
{dcl_selector_init}
{dcl_validator_init}
<script language="JavaScript">
function validate(form, status)
{literal}
{
{/literal}
	form.elements["status"].value=status;
	var aValidators = new Array(
			new ValidatorSelection(form.elements["entity_source_id"], "{$smarty.const.STR_CMMN_SOURCE}"),
			new ValidatorSelection(form.elements["product"], "{$smarty.const.STR_TCK_PRODUCT}"),
			new ValidatorSelection(form.elements["module_id"], "{$smarty.const.STR_CMMN_MODULE}"),
			new ValidatorSelection(form.elements["type"], "{$smarty.const.STR_TCK_TYPE}"),
			new ValidatorSelection(form.elements["responsible"], "{$smarty.const.STR_TCK_RESPONSIBLE}"),
			new ValidatorInteger(form.elements["account"], "{$smarty.const.STR_TCK_ACCOUNT}", true),
			new ValidatorInteger(form.elements["contact_id"], "{$smarty.const.STR_TCK_CONTACT}", true),
			new ValidatorSelection(form.elements["priority"], "{$smarty.const.STR_TCK_PRIORITY}"),
			new ValidatorString(form.elements["summary"], "{$smarty.const.STR_TCK_SUMMARY}"),
			new ValidatorString(form.elements["issue"], "{$smarty.const.STR_TCK_ISSUE}")
		);

	if (status == "2" && form.elements["resolution"])
		aValidators.push(new ValidatorString(form.elements["resolution"], "{$smarty.const.STR_TCK_RESOLUTION}"));
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
{if $IS_EDIT}{assign var=ACTIVE_ONLY value=N}{else}{assign var=ACTIVE_ONLY value=Y}{/if}
{if $PERM_ACTION && !$IS_EDIT}
<form class="styled" action="{$VAL_FORMACTION}" method="post">
	<input type="hidden" name="menuAction" value="boTicketresolutions.add">
	<fieldset>
		<div class="required"><label for="ticketid">{$smarty.const.STR_TCK_JUMPTOTICKETID}:</label><input type="text" id="ticketid" name="ticketid" size="8"></div>
	</fieldset>
	<fieldset><div class="submit"><input type="submit" value="{$smarty.const.STR_CMMN_FIND}"></div></fieldset>
</form>
{/if}
<form class="styled" name="tckform" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuActionExExExExEx" value="{$VAL_MENUACTION}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<input type="hidden" name="status" value="{$VAL_STATUS}">
	{if $VAL_STARTEDON}<input type="hidden" name="startedon" value="{$VAL_STARTEDON}">{/if}
	{if $VAL_TICKETID}<input type="hidden" name="ticketid" value="{$VAL_TICKETID}">{/if}
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
{if $PERM_ASSIGNWO}
		<div class="required">
			<label for="responsible">{$smarty.const.STR_TCK_RESPONSIBLE}:</label>
			{dcl_select_personnel name="responsible" default="$VAL_RESPONSIBLE" entity=$smarty.const.DCL_ENTITY_TICKET perm=$smarty.const.DCL_PERM_ACTION}
		</div>
	{if !$PERM_ISPUBLIC}
		<div>
			<label for="is_public">{$smarty.const.STR_CMMN_PUBLIC}:</label>
			<input type="checkbox" name="is_public" id="is_public" value="Y"{if $VAL_ISPUBLIC == "Y"} checked{/if}>
		</div>
	{/if}
{elseif $PERM_ACTION && !$VAL_ISEDIT}
		<div class="required">
			<label for="responsible">{$smarty.const.STR_TCK_ASSIGNTOME}:</label>
			<input type="checkbox" name="responsible" value="{$VAL_DCLID}" checked>
		</div>
{/if}
		<div class="required">
			<label for="entity_source_id">{$smarty.const.STR_CMMN_SOURCE}:</label>
			{dcl_select_source default="$VAL_SOURCE" active="$ACTIVE_ONLY"}
		</div>
{if !$PERM_ISPUBLIC}
		<div class="required">
			<label for="contact_id">{$smarty.const.STR_WO_CONTACT}:</label>
			{dcl_selector_contact name="contact_id" value="$VAL_CONTACTID" decoded="$VAL_CONTACTNAME" orgselector="account"}
		</div>
		<div class="required">
			<label for="account">{$smarty.const.STR_CMMN_ORGANIZATION}:</label>
			{dcl_selector_org name="account" value="$VAL_ORGID" decoded="$VAL_ORGNAME" multiple="N"}
		</div>
{/if}
		<div class="required">
			<label for="product">{$smarty.const.STR_TCK_PRODUCT}:</label>
			{dcl_select_product default="$VAL_PRODUCT" active="$ACTIVE_ONLY" onchange="productSelChange(this.form);"}
		</div>
		<div class="required">
			<label for="module_id">{$smarty.const.STR_CMMN_MODULE}:</label>
			{dcl_select_module default="$VAL_MODULE" active="$ACTIVE_ONLY"}
		</div>
		<div>
			<label for="version">{$smarty.const.STR_TCK_VERSION}:</label>
			<input type="text" id="version" name="version" size="20" maxlength="20" value="{$VAL_VERSION|escape}">
		</div>
		<div class="required">
			<label for="priority">{$smarty.const.STR_TCK_PRIORITY}:</label>
			{dcl_select_priority default="$VAL_PRIORITY" active="$ACTIVE_ONLY" setid="$VAL_SETID"}
		</div>
		<div class="required">
			<label for="type">{$smarty.const.STR_TCK_TYPE}:</label>
			{dcl_select_severity default="$VAL_TYPE" active="$ACTIVE_ONLY" setid="$VAL_SETID" name="type"}
		</div>
		<div class="required">
			<label for="summary">{$smarty.const.STR_TCK_SUMMARY}:</label>
			<input type="text" name="summary" size="70" maxlength="100" value="{$VAL_SUMMARY|escape}">
		</div>
		<div>
			<label for="tags">{$smarty.const.STR_CMMN_TAGS|escape}:</label>
			<input type="text" name="tags" id="tags" size="60" value="{$VAL_TAGS|escape}">
			<span>{$smarty.const.STR_CMMN_TAGSHELP|escape}</span>
		</div>
		<div class="required">
			<label for="issue">{$smarty.const.STR_TCK_ISSUE}:</label>
			<textarea name="issue" rows="6" cols="70" wrap valign="top">{$VAL_ISSUE|escape}</textarea>
		</div>
		<div class="required">
			<label for="copy_me_on_notification">Copy Me on Notification:</label>
			<input type="checkbox" id="copy_me_on_notification" name="copy_me_on_notification" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
		</div>
{if $PERM_ATTACHFILE && !$VAL_ISEDIT && $VAL_MAXUPLOADFILESIZE > 0}
		<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
		<div>
			<label for="userfile">{$smarty.const.STR_WO_ATTACHFILE}:</label>
			<input type="file" id="userfile" name="userfile">
		</div>
{/if}
{if $PERM_ACTION && !$VAL_ISEDIT}
		<div>
			<label for="resolution">{$smarty.const.STR_TCK_RESOLUTION}:</label>
			<textarea name="resolution" rows="6" cols="70" wrap valign="top"></textarea>
		</div>
{/if}
	</fieldset>
	<fieldset>
		<div class="submit">
{if $PERM_ACTION && !$VAL_ISEDIT}<input type="button" onclick="validate(this.form, '2');" value="{$smarty.const.STR_TCK_SAVEANDCLOSE}">{/if}
		<input type="button" onclick="validate(this.form, '1');" value="{$smarty.const.STR_CMMN_SAVE}">
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
	});
	//]]>{/literal}
</script>
