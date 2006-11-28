<!-- $Id: htmlTicketDetailPublic.tpl,v 1.1.1.1 2006/11/27 05:30:37 mdean Exp $ -->
{if $PERM_MODIFY_TR && $VAL_EDITRESID}
{dcl_calendar_init}
{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
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
{/if}
<script language="JavaScript">
{literal}
	function submitAction(sAction)
	{
		document.actionForm.menuAction.value = sAction;
		document.actionForm.submit();
	}
{/literal}
</script>
<div class="dcl_detail">
	<table class="styled" width="100%">
		<caption>{$smarty.const.STR_TCK_TICKET} [{$VAL_TICKETID}] {$VAL_SUMMARY|escape}</caption>
		<thead>{include file="ctlTicketOptions.tpl"}</thead>
		<tbody>
			<tr><th>{$smarty.const.STR_TCK_RESPONSIBLE}:</th><td>{$VAL_RESPONSIBLE|escape}</td>
				<th>{$smarty.const.STR_TCK_PRODUCT}:</th><td>{$VAL_PRODUCT|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_TCK_STATUS}:</th><td>{$VAL_STATUS|escape} ({$VAL_STATUSON|escape})</td>
				<th>{$smarty.const.STR_CMMN_MODULE}:</th><td>{$VAL_MODULE|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_TCK_PRIORITY}:</th><td>{$VAL_PRIORITY|escape}</td>
				<th>{$smarty.const.STR_TCK_VERSION}:</th><td>{$VAL_VERSION|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_TCK_TYPE}:</th><td>{$VAL_TYPE|escape}</td>
				<th>{$smarty.const.STR_TCK_ACCOUNT}:</th><td>{if $VAL_ORGID}{if $PERM_VIEWORG}<a href="{$URL_MAIN_PHP}?menuAction=htmlOrgDetail.show&org_id={$VAL_ORGID}">{/if}{$VAL_ACCOUNT|escape}{if $PERM_VIEWORG}</a>{/if}{/if}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_LASTACTION}:</th><td>{$VAL_LASTACTIONON|escape}</td>
				<th>{$smarty.const.STR_WO_CONTACT}:</th><td>{if $VAL_CONTACTID}{if $PERM_VIEWCONTACT}<a href="{$URL_MAIN_PHP}?menuAction=htmlContactDetail.show&contact_id={$VAL_CONTACTID}">{/if}{$VAL_CONTACT|escape}{if $PERM_VIEWCONTACT}</a>{/if}{/if}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_OPENBY}:</th><td>{$VAL_CREATEDBY|escape} ({$VAL_CREATEDON|escape})</td>
				<th>{$smarty.const.STR_WO_CONTACTPHONE}:</th><td>{$VAL_CONTACTPHONE|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_CLOSEBY}:</th><td>{$VAL_CLOSEDBY|escape} ({$VAL_CLOSEDON|escape})</td>
				<th>{$smarty.const.STR_WO_CONTACTEMAIL}:</th><td>{if $VAL_CONTACTEMAIL != ""}{mailto address=$VAL_CONTACTEMAIL}{/if}</td>
			</tr>
			</tr>
			<tr><th>{$smarty.const.STR_TCK_APPROXTIME}:</th><td>{$VAL_HOURSTEXT|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_TCK_ISSUE}:</th><td colspan="3">{$VAL_ISSUE|escape:"link"}</td></tr>
		</tbody>
	</table>
{include file="ctlAttachmentsTickets.tpl"}
</div>
{include file="ctlTicketResolutions.tpl"}