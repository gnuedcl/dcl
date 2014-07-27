<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP|escape}">
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	{if $jcn && is_array($jcn)}
		{section loop=$jcn name=row}
			<input type="hidden" name="selected[]" value="{$jcn[row]|escape}">
		{/section}
	{else}
		{if $jcn}<input type="hidden" name="jcn" value="{$jcn|escape}">{/if}
		{if $seq}<input type="hidden" name="seq" value="{$seq|escape}">{/if}
	{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=projectid controlsize=4 label=$smarty.const.STR_PM_CHOOSEPRJ required=true}
			{dcl_input_text id=projectid}
		{/dcl_form_control}
		{dcl_form_control id=addall controlsize=10 label=$smarty.const.STR_PM_ADDALLSEQ}
			<input type="checkbox" id="addall" name="addall" value="1">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="reset" value="{$smarty.const.STR_CMMN_RESET}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
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
	});
</script>