<!-- $Id$ -->
{dcl_calendar_init}
{dcl_validator_init}
<script language="JavaScript">
{literal}
function submitForm(f)
{
	var aValidators = new Array(
		new ValidatorDate(form.elements["begindate"], "{$smarty.const.STR_WOST_BEGIN}"),
		new ValidatorDate(form.elements["enddate"], "{$smarty.const.STR_WOST_ENDING}")
	);
{literal}
	for (var i in aValidators)
	{
		if (!aValidators[i].isValid())
		{
			alert(aValidators[i].getError());
			if (typeof(aValidators[i]._Element.focus) == "function")
				aValidators[i]._Element.focus();
			return false;
		}
	}

	return true;
}
{/literal}
</script>
<form class="styled" onsubmit="return submitForm(this);" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="htmlWOStatistics.ShowUserVsProductStatus">
	<fieldset>
		<legend>{$smarty.const.STR_WOST_SSTITLE}</legend>
		<div>
			<label for="people">{$smarty.const.STR_WOST_PERSONNEL}:</label>
			{$CMB_PEOPLE}
		</div>
		<div>
			<label for="products">{$smarty.const.STR_WOST_PRODUCTS}:</label>
			{$CMB_PRODUCTS}
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_WOST_CLOSEDWO}</legend>
		<div>
			<label for="begindate">{$smarty.const.STR_WOST_BEGIN}:</label>
			{dcl_calendar name="begindate"}
		</div>
		<div>
			<label for="enddate">{$smarty.const.STR_WOST_ENDING}:</label>
			{dcl_calendar name="enddate"}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_CMMN_GO}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>