{extends file="_Layout.tpl"}
{block name=title}{$TXT_TITLE|escape}{/block}
{block name=content}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}" />
	<input type="hidden" name="{$VAL_IDFIELD}" value="{$VAL_ID}" />
{if $VAL_ID2FIELD != ""}
	<input type="hidden" name="{$VAL_ID2FIELD}" value="{$VAL_ID2}" />
{/if}
	<fieldset>
		<legend>{$TXT_TITLE|escape}</legend>
		<p class="alert alert-warning">{$VAL_WARNING|escape}</p>
		{if $TXT_DEACTIVATENOTE}<p class="alert alert-info">{$TXT_DEACTIVATENOTE|escape}</p>{/if}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input class="btn btn-danger" type="submit" value="{$smarty.const.STR_CMMN_YES|escape}">
				<input class="btn btn-success" type="button" onclick="javascript: history.back();" value="{$smarty.const.STR_CMMN_NO|escape}">
			</div>
		</div>
	</fieldset>
<form>
{/block}