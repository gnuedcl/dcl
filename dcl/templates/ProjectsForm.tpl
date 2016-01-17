{extends file="_Layout.tpl"}
{block name=title}{if $IS_EDIT}{$smarty.const.STR_PRJ_EDIT|escape}{else}{$smarty.const.STR_PRJ_ADD|escape}{/if}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" name="PROJECTFORM" method="post" action="{$URL_MAIN_PHP}">
	{dcl_anti_csrf_token}
{if $IS_EDIT}
	<input type="hidden" name="menuAction" value="Project.Update">
	<input type="hidden" name="projectid" value="{$ViewData->Id}">
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_EDIT|escape}</legend>
{else}
	<input type="hidden" name="menuAction" value="Project.Insert">
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_ADD|escape}</legend>
{/if}
		{dcl_form_control id=name controlsize=4 label=$smarty.const.STR_PRJ_NAME required=true}
		<input class="form-control" type="text" maxlength="100" id="name" name="name" value="{$ViewData->Name|escape}">
		{/dcl_form_control}
{if $IS_EDIT}
	{dcl_form_control id=status controlsize=4 label=$smarty.const.STR_PRJ_STATUS required=true}
	{dcl_select_status default=$ViewData->StatusId}
	{/dcl_form_control}
{/if}
		{dcl_form_control id=reportto controlsize=4 label=$smarty.const.STR_PRJ_LEAD required=true}
		{dcl_select_personnel name=reportto default=$ViewData->ResponsibleId}
		{/dcl_form_control}
		{dcl_form_control id=projectdeadline controlsize=2 label=$smarty.const.STR_PRJ_DEADLINE}
			<input type="text" class="form-control" data-input-type="date" maxlength="10" id="projectdeadline" name="projectdeadline" value="{$ViewData->Deadline|escape}">
		{/dcl_form_control}
		{dcl_form_control id=parentprojectid controlsize=4 label=$smarty.const.STR_PRJ_PARENTPRJ}
			{dcl_input_text id=parentprojectid}
		{/dcl_form_control}
		{dcl_form_control id=description controlsize=4 label=$smarty.const.STR_PRJ_DESCRIPTION required=true}
			<textarea name="description" rows="4" cols="70" wrap valign="top">{$ViewData->Description|escape}</textarea>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="button" id="btn-save" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="button" id="btn-cancel" class="btn btn-link" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_validator_init}
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#name").focus();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
		$("input[data-input-type=date]").datepicker();

		$("#parentprojectid").select2({
			multiple: false,
			maximumSelectionSize: 1,
			minimumInputLength: 2,
			tags: [],
			initSelection: function(element, callback) {
				callback(initialProject);
			},
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

		{if $ViewData->ParentId}
		var initialProject = { id: {$ViewData->ParentId}, text: "{$ViewData->ParentName|escape:'javascript'}" };
		$("#parentprojectid").select2('val', initialProject);
		{/if}

		$("#btn-cancel").click(function() {
			history.go(-1);
		});

		$("#btn-save").click(function() {
			validateAndSubmitForm($("form[name=PROJECTFORM]").get(0));
		});
	});

	function validateAndSubmitForm(form) {
		var aValidators = [
				new ValidatorInteger(form.elements["reportto"], "{$smarty.const.STR_PRJ_LEAD}"),
				new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_PRJ_STATUS}"),
				new ValidatorString(form.elements["name"], "{$smarty.const.STR_PRJ_NAME}"),
				new ValidatorString(form.elements["description"], "{$smarty.const.STR_PRJ_DESCRIPTION}")
		];

		for (var i in aValidators) {
			if (!aValidators[i].isValid()) {
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