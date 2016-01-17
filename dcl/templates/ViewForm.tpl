{extends file="_Layout.tpl"}
{block name=title}{$smarty.const.STR_VW_ADDVIEW|escape}{/block}
{block name=content}
<form class="form-horizontal" method="post" action="{$VAL_FORMACTION}">
	<input type="hidden" name="menuAction" value="boViews.dbadd">
	<input type="hidden" name="whoid" value="{$VAL_DCLID}">
	<input type="hidden" name="tablename" value="{$VAL_TABLENAME}">
	{$VAL_VIEWURL}
	<fieldset>
		<legend>{$smarty.const.STR_VW_ADDVIEW|escape}</legend>
		{dcl_form_control id=ispublic controlsize=1 label=$smarty.const.STR_VW_PUBLIC}
			<input type="checkbox" name="ispublic" id="ispublic" value="Y"{if $VAL_ISPUBLIC == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=name controlsize=8 label=$smarty.const.STR_VW_NAME}
			<input class="form-control" type="text" maxlength="100" name="name" id="name">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="submit" id="btn-save" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="button" id="btn-cancel" class="btn btn-link" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
<script type="text/javascript">
	$(function() {
		$("#btn-cancel").click(function() {
			history.back();
		});
	});
</script>
{/block}