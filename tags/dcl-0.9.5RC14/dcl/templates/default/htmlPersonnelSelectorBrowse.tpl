<html>
	<!-- $Id$ -->
	<head>
		<link rel="stylesheet" type="text/css" href="{$DIR_CSS}/default.css" />
		<script language="JavaScript">
			var iPage = {$VAL_PAGE};
			var iMaxPages = {$VAL_MAXPAGE};
			var iInitAttempts = 0;
{literal}
			function init()
			{
				if (parent.topFrame && parent.topFrame.updatePageControl)
				{
					parent.topFrame.iPage = iPage;
					parent.topFrame.iMaxPages = iMaxPages;
					parent.topFrame.updatePageControl();

					var oSelect = document.forms.theForm.personnel_select;
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
{/literal}
{if $VAL_MULTISELECT}
				parent.topFrame.aSelectedID[oControl.value] = oControl.checked;
				if (oControl.checked)
					parent.topFrame.aSelectedName[oControl.value] = document.getElementById("personnel_name_" + oControl.value).innerHTML;
{else}
				parent.topFrame.aSelectedID = new Array();
				parent.topFrame.aSelectedID[oControl.value] = true;
				parent.topFrame.aSelectedName[oControl.value] = document.getElementById("personnel_name_" + oControl.value).innerHTML;
{/if}
{literal}
			}
{/literal}
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
			{section name=user loop=$VAL_USERS}
				{cycle values="odd,even" assign="rowClass"}
				<tr class="{$rowClass}" onmouseover="javascript:this.className='{$rowClass}Hover';" onmouseout="javascript:this.className='{$rowClass}';" onclick="document.getElementById('personnel_select_{$VAL_USERS[user].id}').click();">
				<td>
				{if $VAL_MULTISELECT}
				<input type="checkbox" id="personnel_select_{$VAL_USERS[user].id}" name="personnel_select" onclick="javascript: toggle(this);" value="{$VAL_USERS[user].id}">
				{else}
				<input type="radio" id="personnel_select_{$VAL_USERS[user].id}" name="personnel_select" onclick="javascript: toggle(this);" value="{$VAL_USERS[user].id}">
				{/if}
				</td>
				<td>{$VAL_USERS[user].id}</td>
				<td>{if $VAL_USERS[user].active == "Y"}{$smarty.const.STR_CMMN_YES}{else}{$smarty.const.STR_CMMN_NO}{/if}</td>
				<td id="personnel_name_{$VAL_USERS[user].id}">{$VAL_USERS[user].short}</td>
				<td>{$VAL_USERS[user].last_name}</td>
				<td>{$VAL_USERS[user].first_name}</td>
				</tr>
			{sectionelse}
				<tr><td colspan="2">No matches.</td></tr>
			{/section}
			</form>
{/strip}
		</table>
	</body>
</html>