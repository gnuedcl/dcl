<center>
<table style="border: 0px none;" cellspacing="0">
	<tr><th class="detailTitle">{TXT_TITLE}</th>
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
	<tr>
		<td class="filterContainer" colspan="2">
			<table style="border: 0px none;">
				<form method="post" action="{VAL_FILTERACTION}">
					{VAL_VIEWSETTINGS}
					<input type="hidden" name="menuAction" value="{VAL_FILTERMENUACTION}" />
					<input type="hidden" name="startrow" value="{VAL_FILTERSTARTROW}" />
					<input type="hidden" name="numrows" value="{VAL_FILTERNUMROWS}" />
<!-- BEGIN pager -->
<!-- This template always has a pager -->
<!-- END pager -->
				<tr>
					<td style="width: 5%; white-space: nowrap;"><b>{TXT_ACTIVE}:</b></td>
					<td style="width: 5%; white-space: nowrap;">
						<select name="filterActive">
<!-- BEGIN filterActiveOptions -->
							<option value="{VAL_FILTERACTIVEOPTION}"{VAL_FILTERACTIVESELECTED}>{TXT_FILTERACTIVEOPTION}</option>
<!-- END filterActiveOptions -->
						</select>
						&nbsp;
						<input type="submit" value="{TXT_FILTER}">
					</td>
					<td rowspan="2" style="text-align: right; white-space: nowrap;">
						<b>Page <input type="text" size="4" maxlength="4" name="jumptopage" value="{VAL_PAGE}"{VAL_JUMPDISABLED}> of {VAL_PAGES}</b>
						<br />
						&nbsp;<input type="submit" style="width: 54px; height: 18px;" name="btnNav" value="&lt;&lt;"{VAL_PREVDISABLED}>
						&nbsp;<input type="submit" style="width: 54px; height: 18px;" name="btnNav" value="&gt;&gt;"{VAL_NEXTDISABLED}>
					</td>
				</tr>
				</form>
			</table>
		</td>
	</tr>
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
			<!-- BEGIN detailHeaderCheckbox -->
						<th class="reportHeader"><input type="checkbox" name="group_check" onclick="javascript: toggle(this);"></th>
			<!-- END detailHeaderCheckbox -->
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
						<td style="background-color: #ffffff;">&nbsp;</td>
				<!-- END detailPadding -->
				<!-- BEGIN detailCheckbox -->
						<td><input type="checkbox" name="selected[]" value="{VAL_ROWSELECT}" /></td>
				<!-- END detailCheckbox -->
				<!-- BEGIN detailColumnText -->
						<td>{VAL_COLUMNVALUE}</td>
				<!-- END detailColumnText -->
				<!-- BEGIN detailColumnAccount -->
						<td>{VAL_COLUMNVALUE}<img src="{IMG_DIR}/jump-to-16.png" style="cursor: pointer; cursor: hand;" onclick="showAccounts({VAL_WOID}, {VAL_SEQ});" /></td>
				<!-- END detailColumnAccount -->
				<!-- BEGIN detailColumnLink -->
						<td><a class="adark" href="{LNK_COLUMNVALUE}">{VAL_COLUMNVALUE}</a></td>
				<!-- END detailColumnLink -->
				<!-- BEGIN detailColumnLinkSet -->
						<td>
					<!-- BEGIN detailColumnLinkSetLinks -->
						<!-- BEGIN detailColumnLinkSetLink -->
							<a class="adark" href="{LNK_COLUMNVALUE}"{LNK_COLUMNDISABLED}>{VAL_COLUMNVALUE}</a>
						<!-- END detailColumnLinkSetLink -->
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
