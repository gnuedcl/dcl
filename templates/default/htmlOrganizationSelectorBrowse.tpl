<html>
	<!-- $Id: htmlOrganizationSelectorBrowse.tpl,v 1.1.1.1 2006/11/27 05:30:38 mdean Exp $ -->
	<head>
		<link rel="stylesheet" type="text/css" href="{$DIR_CSS}default.css" />
		<script language="JavaScript">
			var iPage = {$VAL_PAGE};
			var iMaxPages = {$VAL_MAXPAGE};
			var iInitAttempts = 0;
{literal}
			function init()
			{
				if (parent.topFrame && parent.topFrame.updatePageControl && parent.topFrame.bInitComplete)
				{
					parent.topFrame.iPage = iPage;
					parent.topFrame.iMaxPages = iMaxPages;
					parent.topFrame.updatePageControl();

					var oSelect = document.forms.theForm.org_select;
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
			
			function toggleAll()
			{
				for (var i = 0; i < document.forms.theForm.elements.length; i++)
				{
					if (document.forms.theForm.elements[i].type == "checkbox")
					{
						if (document.forms.theForm.elements[i].name == "group_check")
							continue;

						if (document.forms.theForm.elements.group_check.checked != document.forms.theForm.elements[i].checked)
							document.forms.theForm.elements[i].click();
					}
				}
			}

			function toggle(oControl)
			{
{/literal}
{if $VAL_MULTISELECT}
{literal}
				parent.topFrame.aSelectedID[oControl.value] = oControl.checked;
				if (oControl.checked)
					parent.topFrame.aSelectedName[oControl.value] = document.getElementById("org_name_" + oControl.value).innerHTML;
{/literal}
{else}
				parent.topFrame.aSelectedID = new Array();
				parent.topFrame.aSelectedID[oControl.value] = true;
				parent.topFrame.aSelectedName[oControl.value] = document.getElementById("org_name_" + oControl.value).innerHTML;
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
				<input type="checkbox" name="group_check" onclick="javascript: toggleAll();">
				{else}
				&nbsp;
				{/if}
			</th>
			{foreach from=$VAL_HEADERS item=header}
				<th class="reportHeader">{$header}</th>
			{/foreach}
			</tr>
			{section name=org loop=$VAL_ORGS}
				{cycle values="odd,even" assign="rowClass"}
				<tr class="{$rowClass}" onmouseover="javascript:this.className='{$rowClass}Hover';" onmouseout="javascript:this.className='{$rowClass}';" onclick="document.getElementById('org_select_{$VAL_ORGS[org].org_id}').click();">
				<td>
				{if $VAL_MULTISELECT}
				<input type="checkbox" id="org_select_{$VAL_ORGS[org].org_id}" name="org_select" onclick="javascript: toggle(this);" value="{$VAL_ORGS[org].org_id}">
				{else}
				<input type="radio" id="org_select_{$VAL_ORGS[org].org_id}" name="org_select" onclick="javascript: toggle(this);" value="{$VAL_ORGS[org].org_id}">
				{/if}
				</td>
				<td>{$VAL_ORGS[org].org_id}</td>
				<td id="org_name_{$VAL_ORGS[org].org_id}">{$VAL_ORGS[org].name}</td>
				<td>{$VAL_ORGS[org].phone_number}</td>
				<td>{$VAL_ORGS[org].email_addr}</td>
				<td>{$VAL_ORGS[org].url_addr}</td>
				</tr>
			{sectionelse}
				<tr><td colspan="2">No matches.</td></tr>
			{/section}
			</form>
{/strip}
		</table>
	</body>
</html>