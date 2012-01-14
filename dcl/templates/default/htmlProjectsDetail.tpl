<link rel="stylesheet" href="{$DIR_JS}fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />
<script language="JavaScript">
{literal}
function forceSubmit(sAction)
{
	var f = document.frmProject;
	f.elements['menuAction'].value = sAction;
	processSubmit(f);
}

function processSubmit(f){
	var sAction = f.elements['menuAction'].value;
	if (sAction == 'WorkOrder.BatchDetail' || sAction == 'boTimecards.batchadd' || sAction == 'WorkOrder.BatchAssign' || sAction == 'htmlProjectmap.batchMove')
	{
		if (!submitActionIfValid(sAction))
		{
			alert('You must select at least one work order.');
			return false;
		}
	}
	return true;
}

function toggleCheckGroup(btnSender)
{
	var bChk = btnSender.checked;
	var bOK = false;
	var e=btnSender.form.elements;
	for (var i=0;i<e.length;i++){
		if (!bOK && e[i] == btnSender)
			bOK = true;
		else if (bOK && (e[i].type != "checkbox" || e[i].value == "_groupcheck_"))
			return;
		else if (bOK && e[i].type == "checkbox" && e[i].value != "_groupcheck_")
			e[i].checked = bChk;
	}
}

function submitActionIfValid(sAction){
	var bHasChecks = false;
	var f = document.forms['frmWorkorders'];
	for (var i = 0; i < f.elements.length && !bHasChecks; i++){
		bHasChecks = (f.elements[i].type == "checkbox" && f.elements[i].checked)
	}
	if (bHasChecks){
		f.elements['menuAction'].value = sAction;
		f.submit();
	}

	return bHasChecks;
}
{/literal}
</script>
<div class="dcl_detail">
	<form style="display:none;" method="post" action="{$URL_MAIN_PHP}" id="frmProject" name="frmProject" onsubmit="return processSubmit(this);">
		<input type="hidden" name="menuAction" value="">
		<input type="hidden" name="projectid" value="{$VAL_PROJECTID}">
		<input type="hidden" name="whatid1" value="{$VAL_PROJECTID}">
		<input type="hidden" name="typeid" value="{$VAL_WATCHTYPE}">
		<input type="hidden" name="type" value="{$VAL_WIKITYPE}">
		<input type="hidden" name="id" value="{$VAL_PROJECTID}">
		<input type="hidden" name="name" value="FrontPage">
	</form>
	<table class="styled">
		<caption>[{$VAL_PROJECTID}] {$VAL_NAME|escape}</caption>
		<thead>{include file="ctlProjectOptions.tpl"}</thead>
		<tbody>
			<tr><th>{$smarty.const.STR_PRJ_LEAD}:</th><td>{$VAL_REPORTTO|escape}</td>
				<th>{$smarty.const.STR_PRJ_TOTTASKS}:</th><td>{$VAL_TOTALTASKS|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_DEADLINE}:</th><td>{$VAL_PROJECTDEADLINE|escape}</td>
				<th>{$smarty.const.STR_PRJ_TASKSCOMP}:</th><td>{$VAL_TASKSCLOSED|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_ETC}:</th><td>{$VAL_ETCDATE|escape}</td>
				<th>{$smarty.const.STR_PRJ_HOURSPROJ}:</th><td>{$VAL_ESTHOURS|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_TOTRESINCOMPWO}:</th><td>{$VAL_RESOURCES|escape}</td>
				<th>{$smarty.const.STR_PRJ_HOURSPM}:</th><td>{$VAL_HOURSPM|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_OPENBY}:</th><td>{$VAL_CREATEDBY|escape} ({$VAL_CREATEDON})</td>
				<th>{$smarty.const.STR_PRJ_HOURSAPP}:</th><td>{$VAL_TOTALHOURS|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_STATUS}:</th><td>{$VAL_STATUS|escape}</td>
				<th>{$smarty.const.STR_PRJ_HOURSREM}:</th><td>{$VAL_ETCHOURS|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_LASTACT}:</th><td>{$VAL_LASTACTIVITY|escape}</td>
				<th>{$smarty.const.STR_PRJ_PCTCOMP}:</th><td>{$VAL_PCTCOMP|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_PRJ_CLOSEDON}:</th><td>{$VAL_FINALCLOSE|escape}</td>
			</tr>
{if $VAL_PROJECTS && @count($VAL_PROJECTS) > 0}
			<tr><th>{$smarty.const.STR_PRJ_PARENTPRJ}:</th>
				<td colspan="3">/&nbsp;{section name=project loop=$VAL_PROJECTS}
&nbsp;<a href="{$VAL_MENULINK}?menuAction=boProjects.viewproject&project={$VAL_PROJECTS[project].project_id}">{$VAL_PROJECTS[project].name|escape}</a>{if !$smarty.section.project.last}&nbsp;/{/if}
{/section}
				</td>
			</tr>
{/if}
			<tr><th>{$smarty.const.STR_PRJ_DESCRIPTION}:</th>
				<td colspan="3">{$VAL_DESCRIPTION|escape:"link"}</td>
			</tr>
		</tbody>
	</table>
</div>
{include file="ctlAttachmentsProjects.tpl"}
{include file="ctlProjectChildren.tpl"}
<p>&nbsp;</p>
{include file="ctlProjectTasks.tpl"}
<script type="text/javascript" src="{$DIR_JS}fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script type="text/javascript">
	//<![CDATA[{literal}
	$(document).ready(function() {
		$("a.dcl-lightbox").fancybox({
			type: "iframe"

		});
	});
	//]]>{/literal}
</script>