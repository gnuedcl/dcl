<html>
	<!-- $Id$ -->
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
{/literal}
{if $updateTop && $filterID}
					iPage = 1;
					iMaxPages = 1;
					parent.topFrame.aSelectedID = new Array();
					var oContact = document.getElementById('contact_select_{$filterID}');
					if (oContact)
					{literal}{{/literal}
						toggle(oContact);
						var oControl = parent.topFrame.document.getElementById('filterActiveSelected');
						if (oControl)
							oControl.checked = true;

						oControl = parent.topFrame.document.getElementById('btnStartsWithAll');
						if (oControl && parent.topFrame.oLastButton != oControl)
						{literal}{{/literal}
							if (parent.topFrame.oLastButton)
								parent.topFrame.oLastButton.className = 'dcl_startsWith';
							parent.topFrame.oLastButton = oControl;
							oControl.className = 'dcl_startsWithSelected';
						{literal}}{/literal}
					{literal}}{/literal}
{/if}
{literal}
					parent.topFrame.iPage = iPage;
					parent.topFrame.iMaxPages = iMaxPages;
					parent.topFrame.updatePageControl();

					var oSelect = document.forms.theForm.contact_select;
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
			
			var aData = {{/literal}{section name=contact loop=$VAL_CONTACTS}
"{$VAL_CONTACTS[contact].contact_id|escape:javascript}":{literal}{{/literal}"n":"{$VAL_CONTACTS[contact].last_name|escape:javascript}, {$VAL_CONTACTS[contact].first_name|escape:javascript}","oid":"{$VAL_CONTACTS[contact].org_id|escape:javascript}","on":"{$VAL_CONTACTS[contact].org_name|escape:javascript}"{literal}}{/literal}{if !$smarty.section.contact.last},
{/if}
{/section}{literal}};

			function toggle(oControl)
			{
{/literal}
{if $VAL_MULTISELECT}
				parent.topFrame.aSelectedID[oControl.value] = oControl.checked;
				parent.topFrame.aSelectedOrgID[oControl.value] = aData[oControl.value].oid;
				{literal}if (oControl.checked)
				{
					parent.topFrame.aSelectedName[oControl.value] = aData[oControl.value].n;
					parent.topFrame.aSelectedOrgName[oControl.value] = aData[oControl.value].on;
				}{/literal}
{else}
				parent.topFrame.aSelectedID = new Array();
				parent.topFrame.aSelectedID[oControl.value] = true;
				parent.topFrame.aSelectedName = new Array();
				parent.topFrame.aSelectedName[oControl.value] = aData[oControl.value].n;
				parent.topFrame.aSelectedOrgID = new Array();
				parent.topFrame.aSelectedOrgID[oControl.value] = aData[oControl.value].oid;
				parent.topFrame.aSelectedOrgName = new Array();
				parent.topFrame.aSelectedOrgName[oControl.value] = aData[oControl.value].on;
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
			{section name=contact loop=$VAL_CONTACTS}
				{cycle values="odd,even" assign="rowClass"}
				<tr class="{$rowClass}" onmouseover="javascript:this.className='{$rowClass}Hover';" onmouseout="javascript:this.className='{$rowClass}';" onclick="document.getElementById('contact_select_{$VAL_CONTACTS[contact].contact_id}').click();">
				<td>
				{if $VAL_MULTISELECT}
				<input type="checkbox" id="contact_select_{$VAL_CONTACTS[contact].contact_id}" name="contact_select" onclick="javascript: toggle(this);" value="{$VAL_CONTACTS[contact].contact_id}">
				{else}
				<input type="radio" id="contact_select_{$VAL_CONTACTS[contact].contact_id}" name="contact_select" onclick="javascript: toggle(this);" value="{$VAL_CONTACTS[contact].contact_id}">
				{/if}
				</td>
				<td>{$VAL_CONTACTS[contact].contact_id}</td>
				<td id="contact_name_{$VAL_CONTACTS[contact].contact_id}">{$VAL_CONTACTS[contact].last_name}, {$VAL_CONTACTS[contact].first_name}</td>
				<td id="org_name_{$VAL_CONTACTS[contact].contact_id}"><div style="display:none" id="org_id_{$VAL_CONTACTS[contact].contact_id}">{$VAL_CONTACTS[contact].org_id}</div>{$VAL_CONTACTS[contact].org_name}</td>
				<td>{$VAL_CONTACTS[contact].phone_number}</td>
				<td>{$VAL_CONTACTS[contact].email_addr}</td>
				</tr>
			{sectionelse}
				<tr><td colspan="2">No matches.</td></tr>
			{/section}
			</form>
{/strip}
		</table>
	</body>
</html>
