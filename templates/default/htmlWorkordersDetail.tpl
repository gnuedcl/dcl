<!-- $Id$ -->
{if $PERM_MODIFY_TC && $VAL_EDITTCID}
{dcl_calendar_init}
{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorDate(form.elements["actionon"], "{$smarty.const.STR_TC_DATE}"),
			new ValidatorSelection(form.elements["action"], "{$smarty.const.STR_TC_ACTION}"),
			new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_TC_STATUS}"),
			new ValidatorDecimal(form.elements["hours"], "{$smarty.const.STR_TC_HOURS}"),
			new ValidatorString(form.elements["summary"], "{$smarty.const.STR_WO_SUMMARY}")
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
function submitAction(sFormName, sAction)
{
	var oForm = document.getElementById(sFormName);
	if (!oForm)
		return;
		
	oForm.menuAction.value = sAction;
	oForm.submit();
}
{/literal}
</script>

<div class="dcl_detail">
	<table class="styled">
		<caption>[{$VAL_JCN}-{$VAL_SEQ}] {$VAL_SUMMARY|escape}</caption>
		<thead>{include file="ctlWorkOrderOptions.tpl"}</thead>
		<tbody>
			<tr><th>{$smarty.const.STR_WO_RESPONSIBLE}:</th><td>{$VAL_RESPONSIBLE|escape}</td>
				<th>{$smarty.const.STR_WO_PRODUCT}:</th><td>{$VAL_PRODUCT|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_TYPE}:</th><td>{$VAL_TYPE|escape}</td>
				<th>{$smarty.const.STR_CMMN_MODULE}:</th><td>{$VAL_MODULE|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_STATUS}:</th><td class="highlight">{$VAL_STATUS|escape} ({$VAL_STATUSON|escape})</td>
				<th>Reported Version:</th><td>{$VAL_REPORTED_VERSION|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_PRIORITY}:</th><td>{$VAL_PRIORITY|escape}</td>
				<th>Targeted Version:</th><td>{$VAL_TARGETED_VERSION|escape}</td>
			</tr>
			<tr><th></th><td></td>
				<th>Fixed Version:</th><td>{$VAL_FIXED_VERSION|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_SEVERITY}:</th><td>{$VAL_SEVERITY|escape}</td>
				{if count($VAL_ORGS) == 1}<th>{$smarty.const.STR_WO_ACCOUNT}:</th><td>{if $PERM_VIEWORG}<a href="{$URL_MAIN_PHP}?menuAction=htmlOrgDetail.show&org_id={$VAL_ORGS[0].org_id}">{/if}{$VAL_ORGS[0].org_name|escape}{if $PERM_VIEWORG}</a>{/if}</td>{/if}
			</tr>
			<tr><th>{$smarty.const.STR_WO_DEADLINE}:</th><td>{$VAL_DEADLINEON|escape}</td>
				<th>{$smarty.const.STR_WO_CONTACT}:</th><td>{if $VAL_CONTACTID}{if $PERM_VIEWCONTACT}<a href="{$URL_MAIN_PHP}?menuAction=htmlContactDetail.show&contact_id={$VAL_CONTACTID}">{/if}{$VAL_CONTACT|escape}{if $PERM_VIEWCONTACT}</a>{/if}{/if}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_LASTACTION}:</th><td>{$VAL_LASTACTIONON|escape}</td>
				<th>{$smarty.const.STR_WO_CONTACTPHONE}:</th><td>{$VAL_CONTACTPHONE|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_OPENBY}:</th><td>{$VAL_CREATEBY|escape} ({$VAL_CREATEDON|escape})</td>
				<th>{$smarty.const.STR_WO_CONTACTEMAIL}:</th><td>{if $VAL_CONTACTEMAIL != ""}{mailto address=$VAL_CONTACTEMAIL}{/if}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_CLOSEBY}:</th><td>{$VAL_CLOSEDBY|escape} ({$VAL_CLOSEDON|escape})</td>
				<th>{$smarty.const.STR_WO_ESTHOURS}:</th><td>{$VAL_ESTHOURS|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_ESTSTART}:</th><td>{$VAL_ESTSTARTON|escape}</td>
				<th>{$smarty.const.STR_WO_ACTHOURS}:</th><td>{$VAL_TOTALHOURS|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_START}:</th><td>{$VAL_STARTON|escape}</td>
				<th>{$smarty.const.STR_WO_ETCHOURS}:</th><td>{$VAL_ETCHOURS|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_WO_ESTEND}:</th><td>{$VAL_ESTENDON|escape}</td>
				<th>{$smarty.const.STR_CMMN_PUBLIC}:</th><td>{$VAL_PUBLIC|escape}</td>
			</tr>
	{if count($VAL_ORGS) > 1}
	{section name=org loop=$VAL_ORGS}
		{if $smarty.section.org.first}<tr><th>{$smarty.const.STR_CMMN_ORGANIZATION}:</th><td colspan="3">{/if}
		{if $PERM_VIEWORG}<a href="{$URL_MAIN_PHP}?menuAction=htmlOrgDetail.show&org_id={$VAL_ORGS[org].org_id}">{/if}{$VAL_ORGS[org].org_name|escape}{if $PERM_VIEWORG}</a>{/if}
		{if $smarty.section.org.last}</td></tr>{else},&nbsp;{/if}
	{/section}
	{/if}
	{if $VAL_PROJECTS}
			<tr><th>{$smarty.const.STR_WO_PROJECT}:</th><td colspan="3">
	{section name=project loop=$VAL_PROJECTS}
	<a href="{$VAL_MENULINK}?menuAction=boProjects.viewproject&project={$VAL_PROJECTS[project].project_id}">[{$VAL_PROJECTS[project].project_id}] {$VAL_PROJECTS[project].name|escape}</a>{if !$smarty.section.project.last}&nbsp;/&nbsp;{/if}
	{/section}
				</td>
			</tr>
	{/if}
		<tr><th>{$smarty.const.STR_CMMN_TAGS}:</th><td colspan="3">{dcl_tag_link value=$VAL_TAGS}</td></tr>
	{if $VAL_NOTES != ""}
		<tr><th>{$smarty.const.STR_WO_NOTES}:</th><td colspan="3">{$VAL_NOTES|escape:"link"}</td></tr>
	{/if}
		<tr><th>{$smarty.const.STR_WO_DESCRIPTION}:</th><td colspan="3">{$VAL_DESCRIPTION|escape:"link"}</td></tr>
	</tbody>
	</table>
{include file="ctlAttachments.tpl"}
{include file="ctlWorkOrderTasks.tpl"}
</div>
{include file="ctlTimeCards.tpl"}