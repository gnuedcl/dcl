<!-- $Id: htmlAccountDetail.tpl,v 1.1.1.1 2006/11/27 05:30:36 mdean Exp $ -->
<table border="0">
<tr>
	<th class="formTitle" style="white-space: nowrap;">{VAL_NAME}</th>
	<th class="formLinks">
		<form method="post" action="{VAL_FORMACTION}">
			<input type="hidden" name="id" value="{VAL_ID}">
			<input type="hidden" name="whatid1" value="{VAL_ID}">
			<input type="hidden" name="type" value="{VAL_WIKITYPE}">
			<input type="hidden" name="name" value="FrontPage">
			{CMB_ACTION}
			<input type="submit" value="{TXT_GO}">
		</form>
	</th>
</tr>
<tr>
	<td class="formContainer" colspan="2">
		<table style="border: 0px none;">
			<tr><td>
				<table border="0">
					<tr><td><b>{TXT_ACTIVE}:</b></td><td>{VAL_ACTIVE}</td></tr>
					<tr><td><b>{TXT_SHORT}:</b></td><td>{VAL_SHORT}</td></tr>
					<tr><td><b>{TXT_ADDRESS1}:</b></td><td>{VAL_ADD1}</td></tr>
					<tr><td><b>{TXT_ADDRESS2}:</b></td><td>{VAL_ADD2}</td></tr>
					<tr><td><b>{TXT_CITY}:</b></td><td>{VAL_CITY}</td></tr>
					<tr><td><b>{TXT_STATE}:</b></td><td>{VAL_STATE}</td></tr>
					<tr><td><b>{TXT_ZIP}:</b></td><td>{VAL_ZIP}</td></tr>
					<tr><td><b>{TXT_CONTACT}:</b></td><td>{VAL_CONTACT}</td></tr>
					<tr><td><b>{TXT_CONTACTPH}:</b></td><td>{VAL_VOICE}</td></tr>
					<tr><td><b>{TXT_FAX}:</b></td><td>{VAL_FAX}</td></tr>
					<tr><td><b>{TXT_DATA1}:</b></td><td>{VAL_DATA1}</td></tr>
					<tr><td><b>{TXT_DATA2}:</b></td><td>{VAL_DATA2}</td></tr>
				</table>
			</td>
			<td valign="top" class="leftSeparator">
<!-- BEGIN statuses -->
				<b>{VAL_STATUSNAME}:</b> {VAL_STATUSCOUNT} {VAL_STATUSHOURS}<br>
<!-- END statuses -->
<!-- BEGIN nostatuses -->
				{TXT_NOITEMS}
<!-- END nostatuses -->
			</td>
			<td valign="top" class="leftSeparator">
				<b>{TXT_ACTIVITY}</b><br>
<!-- BEGIN activity -->
				<b>{TXT_ACTIVITYTYPE}: {VAL_ACTIVITYCOUNT}<br>
<!-- END activity -->
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr><th colspan="2" class="formTitle">{TXT_NOTES}</th></tr>
<tr><td colspan="2" class="formContainer">{VAL_NOTES}</td></tr>
</table>
