{extends file="_Layout.tpl"}
{block name=title}{$smarty.const.STR_PRJ_DELATTACH|escape}{/block}
{block name=content}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="Project.DestroyAttachment">
	<input type="hidden" name="projectid" value="{$VAL_PROJECTID}">
	<input type="hidden" name="filename" value="{$VAL_FILENAME|escape}">
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_DELATTACH|escape}</legend>
		<p class="alert alert-warning">{$TXT_DELCONFIRM|escape}</p>
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-danger" type="submit" value="{$smarty.const.STR_CMMN_YES|escape}">
				<a class="btn btn-success" href="{dcl_url_action controller=Project action=Detail params="id={$VAL_PROJECTID}"}">{$smarty.const.STR_CMMN_NO|escape}</a>
			</div>
		</div>
	</fieldset>
</form>
{/block}