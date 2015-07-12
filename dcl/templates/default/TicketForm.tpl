{dcl_selector_init}
{dcl_validator_init}
{if $IS_EDIT}{assign var=ACTIVE_ONLY value=N}{else}{assign var=ACTIVE_ONLY value=Y}{/if}
<form class="form-horizontal" name="tckform" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuActionExExExExEx" value="{$VAL_MENUACTION}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<input type="hidden" name="status" value="{$VAL_STATUS}">
	{if $VAL_STARTEDON}<input type="hidden" name="startedon" value="{$VAL_STARTEDON}">{/if}
	{if $VAL_TICKETID}<input type="hidden" name="ticketid" value="{$VAL_TICKETID}">{/if}
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
{if $PERM_ASSIGNWO}
	{dcl_form_control id=responsible controlsize=4 label=$smarty.const.STR_TCK_RESPONSIBLE required=true}
	{dcl_select_personnel name="responsible" default="$VAL_RESPONSIBLE" entity=$smarty.const.DCL_ENTITY_TICKET perm=$smarty.const.DCL_PERM_ACTION}
	{/dcl_form_control}
	{if !$PERM_ISPUBLIC}
		{dcl_form_control id=is_public controlsize=1 label=$smarty.const.STR_CMMN_PUBLIC}
			<input class="form-control" type="checkbox" name="is_public" id="is_public" value="Y"{if $VAL_ISPUBLIC == "Y"} checked{/if}>
		{/dcl_form_control}
	{/if}
{elseif $PERM_ACTION && !$IS_EDIT}
	{dcl_form_control id=responsible controlsize=1 label=$smarty.const.STR_TCK_ASSIGNTOME}
		<input class="form-control" type="checkbox" name="responsible" id="responsible" value="{$VAL_DCLID}" checked>
	{/dcl_form_control}
{/if}
	{dcl_form_control id=entity_source_id controlsize=4 label=$smarty.const.STR_CMMN_SOURCE required=true}
	{dcl_select_source default="$VAL_SOURCE" active="$ACTIVE_ONLY"}
	{/dcl_form_control}
{if !$PERM_ISPUBLIC}
	{dcl_form_control id=contact_id controlsize=4 label=$smarty.const.STR_TCK_CONTACT required=true}
	{dcl_selector_contact name="contact_id" value="$VAL_CONTACTID" decoded="$VAL_CONTACTNAME" orgselector="account"}
	{/dcl_form_control}
	{dcl_form_control id=account controlsize=4 label=$smarty.const.STR_CMMN_ORGANIZATION required=true}
	{dcl_selector_org name="account" value="$VAL_ORGID" decoded="$VAL_ORGNAME" multiple="N"}
	{/dcl_form_control}
{/if}
	{dcl_form_control id=product controlsize=4 label=$smarty.const.STR_TCK_PRODUCT required=true}
	{dcl_select_product default="$VAL_PRODUCT" active="$ACTIVE_ONLY" onchange="productSelChange(this.form);"}
	{/dcl_form_control}
	{dcl_form_control id=module_id controlsize=4 label=$smarty.const.STR_CMMN_MODULE required=true}
	{dcl_select_module default="$VAL_MODULE" active="$ACTIVE_ONLY"}
	{/dcl_form_control}
	{dcl_form_control id=version controlsize=4 label=$smarty.const.STR_TCK_VERSION}
		<input class="form-control" type="text" id="version" name="version" maxlength="20" value="{$VAL_VERSION|escape}">
	{/dcl_form_control}
	{dcl_form_control id=priority controlsize=4 label=$smarty.const.STR_TCK_PRIORITY required=true}
	{dcl_select_priority default="$VAL_PRIORITY" active="$ACTIVE_ONLY" setid="$VAL_SETID"}
	{/dcl_form_control}
	{dcl_form_control id=type controlsize=4 label=$smarty.const.STR_TCK_TYPE required=true}
	{dcl_select_severity default="$VAL_TYPE" active="$ACTIVE_ONLY" setid="$VAL_SETID" name="type"}
	{/dcl_form_control}
	{dcl_form_control id=summary controlsize=10 label=$smarty.const.STR_TCK_SUMMARY required=true}
		<input class="form-control" type="text" name="summary" id="summary" maxlength="100" value="{$VAL_SUMMARY|escape}">
	{/dcl_form_control}
	{dcl_form_control id=tags controlsize=10 label=$smarty.const.STR_CMMN_TAGS required=true}
		<input class="form-control" type="text" name="tags" id="tags" size="50" value="{$VAL_TAGS|escape}">
		<span class="help-block">{$smarty.const.STR_CMMN_TAGSHELP|escape}</span>
	{/dcl_form_control}
	{dcl_form_control id=issue controlsize=10 label=$smarty.const.STR_TCK_ISSUE required=true}
		<textarea class="form-control" name="issue" id="issue" wrap valign="top">{$VAL_ISSUE|escape}</textarea>
	{/dcl_form_control}
	{dcl_form_control id=copy_me_on_notification controlsize=1 label="Copy Me on Notification"}
		<input class="form-control" type="checkbox" id="copy_me_on_notification" name="copy_me_on_notification" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
	{/dcl_form_control}
{if $PERM_ATTACHFILE && !$IS_EDIT && $VAL_MAXUPLOADFILESIZE > 0}
		<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
	{dcl_form_control id=userfile controlsize=10 label=$smarty.const.STR_TCK_ATTACHFILE}
		<input type="file" id="userfile" name="userfile">
	{/dcl_form_control}
{/if}
{if $PERM_ACTION && !$IS_EDIT}
	{dcl_form_control id=resolution controlsize=10 label=$smarty.const.STR_TCK_RESOLUTION required=true}
		<textarea class="form-control" name="resolution" id="resolution" wrap valign="top"></textarea>
	{/dcl_form_control}
{/if}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="button" class="btn btn-primary" onclick="validate(this.form, '1');" value="{$smarty.const.STR_CMMN_SAVE}">
				{if $PERM_ACTION && !$IS_EDIT}<input type="button" class="btn btn-success" onclick="validate(this.form, '2');" value="{$smarty.const.STR_TCK_SAVEANDCLOSE}">{/if}
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_VENDOR}bettergrow/jquery.BetterGrow.min.js"></script>
<script type="text/javascript">
	function validate(form, status)
	{
		form.elements["status"].value=status;
		var aValidators = [
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
		];

		if (status == "2" && form.elements["resolution"])
			aValidators.push(new ValidatorString(form.elements["resolution"], "{$smarty.const.STR_TCK_RESOLUTION}"));

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
	});
</script>
