<!-- $Id: htmlSessionBrowse.tpl,v 1.1.1.1 2006/11/27 05:30:37 mdean Exp $ -->
<script language="JavaScript">
function killSession(ID)
{
	if (confirm('Are you sure you want to end this session?'))
		location.href = '{LNK_KILLSESSION}&session_id=' + ID;
}
</script>
<center>
<table style="border: 0px none;" cellspacing="0">
	<tr>
		<th class="detailTitle">{TXT_TITLE}</th>
<!-- BEGIN noActions -->
		<th class="detailLinks">&nbsp;</th>
<!-- END noActions -->
<!-- BEGIN actions -->
	<!-- BEGIN actionLinkSet -->
		<th class="detailLinks">
		<!-- BEGIN actionLinkSetLinks -->
			<!-- BEGIN actionLinkSetLink -->
				<a class="adark" href="{LNK_ACTIONVALUE}">{VAL_ACTIONVALUE}</a>
			<!-- END actionLinkSetLink -->
			<!-- BEGIN actionLinkSetSep -->
				|
			<!-- END actionLinkSetSep -->
		<!-- END actionLinkSetLinks -->
		</th>
	<!-- END actionLinkSet -->
<!-- END actions -->
	</tr>
<!-- BEGIN pager -->
	<tr>
		<td class="filterContainer" colspan="2">
			<table style="border: 0px none;">
				<form method="post" action="{VAL_FILTERACTION}">
					{VAL_VIEWSETTINGS}
					<input type="hidden" name="menuAction" value="{VAL_FILTERMENUACTION}" />
					<input type="hidden" name="startrow" value="{VAL_FILTERSTARTROW}" />
					<input type="hidden" name="numrows" value="{VAL_FILTERNUMROWS}" />
				<tr>
					<td style="text-align: right; white-space: nowrap;">
						<b>Page <input type="text" size="4" maxlength="4" name="jumptopage" value="{VAL_PAGE}"{VAL_JUMPDISABLED}> of {VAL_PAGES}</b>
						&nbsp;<input type="submit" style="width: 54px; height: 18px;" name="btnNav" value="&lt;&lt;"{VAL_PREVDISABLED}>
						&nbsp;<input type="submit" style="width: 54px; height: 18px;" name="btnNav" value="&gt;&gt;"{VAL_NEXTDISABLED}>
					</td>
				</tr>
				</form>
			</table>
		</td>
	</tr>
<!-- END pager -->
	<tr>
		<td colspan="2">
<!-- BEGIN nomatches -->
			<span class="error">{TXT_NOMATCHES}</span>
<!-- END nomatches -->
<!-- BEGIN matches -->
			<form name="searchAction" method="post" action="{VAL_SEARCHACTION}">
				<input type="hidden" name="menuAction" value="" />
				{VAL_VIEWSETTINGS}
				<table style="border: 0px none;">
<!-- BEGIN section -->
	<!-- BEGIN group -->
					<tr><th class="{VAL_GROUPCLASS}" style="padding-left: {VAL_GROUPPADDING}px;" colspan="{VAL_GROUPCOLSPAN}">{VAL_GROUP}</th></tr>
	<!-- END group -->
	<!-- BEGIN detailHeader -->
					<tr>
		<!-- BEGIN detailHeaderCells -->
			<!-- BEGIN detailHeaderPadding -->
						<td style="padding-left: {VAL_DETAILHEADERPADDING}px;"></td>
			<!-- END detailHeaderPadding -->
			<!-- BEGIN detailHeaderColumnText -->
						<th class="reportHeader">{VAL_COLUMNHEADER}</th>
			<!-- END detailHeaderColumnText -->
			<!-- BEGIN detailHeaderColumnLink -->
						<th class="reportHeader"><a class="adark" href="{LNK_COLUMNHEADER}">{VAL_COLUMNHEADER}</a></th>
			<!-- END detailHeaderColumnLink -->
		<!-- END detailHeaderCells -->
					</tr>
	<!-- END detailHeader -->
	<!-- BEGIN detailRows -->
		<!-- BEGIN detail -->
					<tr class="{VAL_DETAILCLASS}">
			<!-- BEGIN detailCells -->
				<!-- BEGIN detailPadding -->
						<td style="background-color: #ffffff;">{TXT_PADDING}</td>
				<!-- END detailPadding -->
				<!-- BEGIN detailColumnText -->
						<td>{VAL_COLUMNVALUE}</td>
				<!-- END detailColumnText -->
				<!-- BEGIN detailColumnLink -->
						<td><a class="adark" href="{LNK_COLUMNVALUE}">{VAL_COLUMNVALUE}</a></td>
				<!-- END detailColumnLink -->
				<!-- BEGIN detailColumnLinkSet -->
						<td>
					<!-- BEGIN detailColumnLinkSetLinks -->
						<!-- BEGIN detailColumnLinkSetLink -->
							<a class="adark" href="#" onclick="javascript: killSession('{LNK_COLUMNVALUE}');">{VAL_COLUMNVALUE}</a>
						<!-- END detailColumnLinkSetLink -->
						<!-- BEGIN detailColumnLinkSetLinkDisabled -->
							<span class="disabled">{VAL_COLUMNVALUE}</span>
						<!-- END detailColumnLinkSetLinkDisabled -->
						<!-- BEGIN detailColumnLinkSetSep -->
							|
						<!-- END detailColumnLinkSetSep -->
					<!-- END detailColumnLinkSetLinks -->
						</td>
				<!-- END detailColumnLinkSet -->
			<!-- END detailCells -->
					</tr>
		<!-- END detail -->
	<!-- END detailRows -->
<!-- END section -->
				</table>
			</form>
<!-- END matches -->
		</td>
	</tr>
</table>
</center>
