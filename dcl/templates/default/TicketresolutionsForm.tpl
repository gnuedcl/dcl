{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_TCK_STATUS}"),
			new ValidatorString(form.elements["resolution"], "{$smarty.const.STR_TCK_RESOLUTION}")
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
<form class="styled" name="resform" method="post" action="{$URL_MAIN_PHP}">
{if $IS_EDIT}
	<input type="hidden" name="resid" value="{$resid|escape}">
{/if}
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	<input type="hidden" name="startedon" value="{$startedon|escape}">
	<input type="hidden" name="ticketid" value="{$ticketid|escape}">
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
		<div class="required">
			<label for="status">{$smarty.const.STR_TCK_STATUS}:</label>
			{$CMB_STATUS}
		</div>
		<div class="required">
			<label for="is_public">{$smarty.const.STR_CMMN_PUBLIC}:</label>
			<input type="checkbox" id="is_public" name="is_public" value="Y"{if $VAL_ISPUBLIC == "Y"} checked{/if}>
		</div>
		<div class="required">
			<label for="copy_me_on_notification">Copy Me on Notification:</label>
			<input type="checkbox" id="copy_me_on_notification" name="copy_me_on_notification" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
		</div>
		{if !$PERM_ASSIGN && !$IS_EDIT}
		<div>
			<label for="escalate">{$smarty.const.STR_TCK_ESCALATE}</label>
			<input type="checkbox" id="escalate" name="escalate" value="1">
		</div>
		{/if}
		<div class="required">
			<label for="resolution">{$smarty.const.STR_TCK_RESOLUTION}:</label>
			<textarea id="resolution" name="resolution" rows="6" cols="70" wrap>{$VAL_RESOLUTION|escape}</textarea>
		</div>
	</fieldset>
{if !$IS_EDIT}
	<fieldset>
		<legend>{$smarty.const.STR_CMMN_OPTIONS}</legend>
	{if $PERM_ASSIGN}
		<div>
			<label for="reassign">{$smarty.const.STR_CMMN_REASSIGN}:</label>
			{$CMB_REASSIGN}
			<span>You can reassign this work order to another person by selecting their user name here.</span>
		</div>
	{/if}
	{if $PERM_MODIFYTICKET}
		<div>
			<label for="tags">{$smarty.const.STR_CMMN_TAGS|escape}:</label>
			<input type="text" name="tags" id="tags" size="50" value="{$VAL_TAGS|escape}">
			<span>{$smarty.const.STR_CMMN_TAGSHELP|escape}</span>
		</div>
	{/if}
	</fieldset>
{/if}
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
