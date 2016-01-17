{extends file="_Layout.tpl"}
{block name=title}{$TXT_TITLE|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" name="resform" method="post" action="{$URL_MAIN_PHP}">
	{dcl_anti_csrf_token}
{if $IS_EDIT}
	<input type="hidden" name="resid" value="{$resid|escape}">
{/if}
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	<input type="hidden" name="startedon" value="{$startedon|escape}">
	<input type="hidden" name="ticketid" value="{$ticketid|escape}">
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
		{dcl_form_control id=status controlsize=4 label=$smarty.const.STR_TCK_STATUS required=true help="The current status is selected for you.  If your action put this work order in a new status, please select it."}
		{$CMB_STATUS}
		{/dcl_form_control}
		{dcl_form_control id=is_public controlsize=10 label=$smarty.const.STR_CMMN_PUBLIC}
			<input type="checkbox" id="is_public" name="is_public" value="Y"{if $VAL_ISPUBLIC == 'Y'} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=copy_me_on_notification controlsize=10 label="Copy Me on Notification"}
			<input type="checkbox" id="copy_me_on_notification" name="copy_me_on_notification" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
		{/dcl_form_control}
		{if !$PERM_ASSIGN && !$IS_EDIT}
			{dcl_form_control id=escalate controlsize=10 label="{$smarty.const.STR_TCK_ESCALATE}"}
				<input type="checkbox" id="escalate" name="escalate" value="1">
			{/dcl_form_control}
		{/if}
		{dcl_form_control id=resolution controlsize=10 label=$smarty.const.STR_TCK_RESOLUTION}
			<textarea class="form-control" name="resolution" rows="4" wrap valign="top">{$VAL_RESOLUTION|escape}</textarea>
		{/dcl_form_control}
	</fieldset>
{if !$IS_EDIT}
	<fieldset>
		<legend>{$smarty.const.STR_CMMN_OPTIONS}</legend>
	{if $PERM_ASSIGN}
		{dcl_form_control id=reassign controlsize=4 label=$smarty.const.STR_CMMN_REASSIGN help="You can reassign this ticket to another person by selecting their user name here."}
		{$CMB_REASSIGN}
		{/dcl_form_control}
	{/if}
	{if $PERM_MODIFYTICKET}
		{dcl_form_control id=tags controlsize=10 label=$smarty.const.STR_CMMN_TAGS help=$smarty.const.STR_CMMN_TAGSHELP}
		{dcl_input_text id=tags value=$VAL_TAGS}
		{/dcl_form_control}
	{/if}
	</fieldset>
{/if}
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" class="inputSubmit" value="{$smarty.const.STR_CMMN_SAVE}" onclick="validateAndSubmitForm(this.form);">
				<input class="btn btn-link" type="button" class="inputSubmit" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=boTickets.view&ticketid={$ticketid}';">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_validator_init}
<script type="text/javascript" src="{$DIR_VENDOR}bettergrow/jquery.BetterGrow.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("textarea").BetterGrow();
		$("#content").find("select").select2();

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

	function validateAndSubmitForm(form) {
		var aValidators = [
			new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_TCK_STATUS}"),
			new ValidatorString(form.elements["resolution"], "{$smarty.const.STR_TCK_RESOLUTION}")
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
{/block}