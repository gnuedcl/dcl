<html>
	<head>
		<link rel="stylesheet" type="text/css" href="{$DIR_CSS}/default.css" />
		<style>body{ padding-top:0px; }</style>
		<script language="JavaScript">
			var iPage = {$VAL_PAGE};
			var iMaxPages = {$VAL_MAXPAGE};
			var iInitAttempts = 0;

			function init()
			{
				if (parent.topFrame && parent.topFrame.updatePageControl && parent.topFrame.bInitComplete)
				{
					parent.topFrame.iPage = iPage;
					parent.topFrame.iMaxPages = iMaxPages;
					parent.topFrame.updatePageControl();
					
					var oSelect = document.forms.theForm.project_select;
					if (oSelect)
					{
						if (oSelect.length)
						{
							for (var i = 0; i < oSelect.length; i++)
							{
								if (parent.topFrame.aSelectedID[oSelect[i].value])
									oSelect[i].checked = true;
							}
						}
						else
						{
							if (parent.topFrame.aSelectedID[oSelect.value])
								oSelect.checked = true;
						}
					}
				}
				else
				{
					// topFrame not loaded yet, so wait a little and retry
					if (++iInitAttempts < 21)
						setTimeout('init()', 250);
				}
			}
			
			function toggle(oControl)
			{

{if $VAL_MULTISELECT}
				parent.topFrame.aSelectedID[oControl.value] = oControl.checked;
				if (oControl.checked)
					parent.topFrame.aSelectedName[oControl.value] = document.getElementById("project_name_" + oControl.value).innerHTML;
{else}
				parent.topFrame.aSelectedID = new Array();
				parent.topFrame.aSelectedID[oControl.value] = true;
				parent.topFrame.aSelectedName[oControl.value] = document.getElementById("project_name_" + oControl.value).innerHTML;
{/if}

			}

		</script>
	</head>
	<body onload="init();">
		<table>
{strip}
			<form name="theForm">
			<tr>
			<th class="reportHeader">
				{if $VAL_MULTISELECT}
				<input type="checkbox" name="group_check" onclick="javascript: toggle(this);">
				{else}
				&nbsp;
				{/if}
			</th>
			{foreach from=$VAL_HEADERS item=header}
				<th class="reportHeader">{$header}</th>
			{/foreach}
			</tr>
			{section name=project loop=$VAL_PROJECTS}
				{cycle values="odd,even" assign="rowClass"}
				<tr class="{$rowClass}" onmouseover="javascript:this.className='{$rowClass}Hover';" onmouseout="javascript:this.className='{$rowClass}';" onclick="document.getElementById('project_select_{$VAL_PROJECTS[project].projectid}').click();">
				<td>
				{if $VAL_MULTISELECT}
				<input type="checkbox" name="project_select" id="project_select_{$VAL_PROJECTS[project].projectid}" onclick="javascript: toggle(this);" value="{$VAL_PROJECTS[project].projectid}">
				{else}
				<input type="radio" name="project_select" id="project_select_{$VAL_PROJECTS[project].projectid}" onclick="javascript: toggle(this);" value="{$VAL_PROJECTS[project].projectid}">
				{/if}
				</td>
				<td>{$VAL_PROJECTS[project].projectid|escape}</td>
				<td>{$VAL_PROJECTS[project].reportto|escape}</td>
				<td>{$VAL_PROJECTS[project].status|escape}</td>
				<td id="project_name_{$VAL_PROJECTS[project].projectid|escape}">{$VAL_PROJECTS[project].name|escape}</td>
				</tr>
			{sectionelse}
				<tr><td colspan="2">No matches.</td></tr>
			{/section}
			</form>
{/strip}
		</table>
	</body>
</html>