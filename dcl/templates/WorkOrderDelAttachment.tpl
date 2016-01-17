{extends file="_Layout.tpl"}
{block name=title}{$TXT_TITLE|escape}{/block}
{block name=content}
<form class="form-horizontal" method="post" action="{$VAL_FORMACTION}">
	<input type="hidden" name="menuAction" value="WorkOrder.DestroyAttachment">
	<input type="hidden" name="filename" value="{$VAL_FILENAME|escape}">
	<input type="hidden" name="jcn" value="{$VAL_JCN}">
	<input type="hidden" name="seq" value="{$VAL_SEQ}">
	<fieldset>
		<legend>{$TXT_TITLE|escape}</legend>
		<p class="alert alert-warning">{$TXT_DELATTCONFIRM|escape}</p>
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="submit" class="btn btn-danger" value="{$BTN_YES|escape}">
				<a class="btn btn-success" href="{dcl_url_action controller=WorkOrder action=Detail params="jcn={$VAL_JCN}&seq={$VAL_SEQ}"}#files">{$BTN_NO|escape}</a>
			</div>
		</div>
	</fieldset>
</form>
{/block}