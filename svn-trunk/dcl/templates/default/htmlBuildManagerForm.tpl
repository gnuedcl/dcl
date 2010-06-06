<!-- $Id: htmlConfig.tpl,v 1.4.2.2.2.8 2003/10/20 03:45:50 mdean Exp $ -->
<form class="styled" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<input type="hidden" name="init" value="{$VAL_INIT}">
	<input type="hidden" name="productid" value="{$VAL_PRODUCTID}">
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
	<div class="required">
		<label for="product_version_id">{$smarty.const.STR_BM_VERSIONNAME}:</label>{$CMB_RELEASE}
	</div>
{if $CMB_BUILD}
	<div class="required">
		<label for="product_build_id">{$smarty.const.STR_BM_BUILDNAME}:</label>{$CMB_BUILD}
	</div>
	<div>
		<fieldset>
			<legend>Environment:</legend>
			<div>
				<input type="radio" name="env" id="env_dev" value="dev" checked="checked">&nbsp;<label for="env_dev">{$smarty.const.STR_BM_DEVENV}</label>
				<input type="radio" name="env" id="env_qa" value="qa">&nbsp;<label for="env_qa">{$smarty.const.STR_BM_QAENV}</label>
			</div>
		</fieldset>
	</div>
{/if}
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>