<link rel="stylesheet" type="text/css" href="{$DIR_CSS}default.css" />
<script language="JavaScript">
	var iPage = {$VAL_PAGE};
	var iMaxPages = {$VAL_MAXPAGE};
	var sStartsWith = '{$VAL_FILTERSTART}';
	var sActiveFilter = '{$VAL_FILTERACTIVE}';

	var oLastButton = null;

	function selectStartsWith(oButton, sLetter)
	{
		if (oLastButton != null)
			oLastButton.className = 'startsWith';

		oLastButton = oButton;
		oButton.className = 'startsWithSelected';

		if (sLetter == 'All')
			sLetter = '';

		sStartsWith = sLetter;

		applyFilter();
	}

	function selectActive(sActive)
	{
		sActiveFilter = sActive;
		applyFilter();
	}

	function getFilter()
	{

		var sURL = '{$URL_MAIN_PHP}?menuAction=htmlOrgBrowse.show';

		sURL += '&filterStartsWith=' + sStartsWith;
		sURL += '&filterActive=' + sActiveFilter;

		var oSearch = document.getElementById('filterName');
		if (oSearch.value != '')
			sURL += '&filterSearch=' + escape(oSearch.value);

		return sURL;
	}

	function applyFilter()
	{
		location.href = getFilter();
	}

	function nextPage()
	{
		iPage++;
		if (iPage > iMaxPages)
			iPage = iMaxPages;

		var sURL = getFilter();
		sURL += '&page=' + iPage;

		location.href = sURL;
	}

	function prevPage()
	{
		iPage--;
		if (iPage < 1)
			iPage = 1;

		var sURL = getFilter();
		sURL += '&page=' + iPage;

		location.href = sURL;
	}

	function searchName(e)
	{
		if (e)
		{
			if (e.which != 13)
				return;
		}
		else if (event)
		{
			if (event.keyCode != 13)
				return;
		}
		else
			return;

		applyFilter();
	}

	function jumpToPage(e)
	{
		if (e)
		{
			if (e.which != 13)
				return;
		}
		else if (event)
		{
			if (event.keyCode != 13)
				return;
		}
		else
			return;

		var oRegEx = /^[0-9]+$/
		var sPage = document.getElementById('jumptopage').value;
		if (!oRegEx.test(sPage))
		{
			alert('Please enter a numeric value for the page.');
			document.getElementById('jumptopage').focus();
			return;
		}

		if (parseInt(sPage) > iMaxPages || parseInt(sPage) < 1)
		{
			alert('Page range must be 1 to ' + iMaxPages);
			return;
		}

		var sURL = getFilter();
		sURL += '&page=' + sPage;

		location.href = sURL;
	}

	function updatePageControl()
	{
		document.getElementById('btnNavPrev').disabled = (iPage == 1);
		document.getElementById('btnNavNext').disabled = (iPage >= iMaxPages);
		document.getElementById('jumptopage').value = iPage;
		document.getElementById('jumptopage').disabled = (iPage == 1 && iMaxPages == 1);
		document.getElementById('maxPages').innerHTML = iMaxPages;
	}

	function init()
	{
		document.getElementById('jumptopage').onkeydown = jumpToPage;
		document.getElementById('filterName').onkeydown = searchName;

		var sFilterStart = "{$VAL_FILTERSTART}";

		if (sFilterStart == "")
			sFilterStart = "All";

		oLastButton = document.getElementById("btnStartsWith" + sFilterStart);
		updatePageControl();
	}

	window.onload = init;

</script>
<table style="width: 100%;" cellspacing="0">
	<tr>
		<th class="formTitle">Browse Organizations</th>
		<th class="formLinks">{if $PERM_ADD}<a href="{$URL_MAIN_PHP}?menuAction=Organization.Create">{$smarty.const.STR_CMMN_NEW}</a>{else}&nbsp;{/if}</th>
	</tr>
	<tr>
		<td class="formContainer" colspan="2">
			<table style="width: 100%;">
				<tr>
					<td>
						<input type="radio" name="filterActive" id="filterActiveAll" value="" onclick="selectActive('');"{if $VAL_FILTERACTIVE == ""} checked{/if}><label for="filterActiveAll"><b>All</b></label>&nbsp;
						<input type="radio" name="filterActive" id="filterActiveYes" value="Y" onclick="selectActive('Y');"{if $VAL_FILTERACTIVE == "Y"} checked{/if}><label for="filterActiveYes"><b>Active</b></label>&nbsp;
						<input type="radio" name="filterActive" id="filterActiveNo" value="N" onclick="selectActive('N');"{if $VAL_FILTERACTIVE == "N"} checked{/if}><label for="filterActiveNo"><b>Inactive</b></label>&nbsp;|&nbsp;
						<input type="text" name="filterName" id="filterName" value="{$VAL_FILTERSEARCH}" size="10">
						<br />
{strip}
						<table style="border: none 0px; margin-bottom: 0px;" cellpadding="0"><tr>
						{foreach from=$VAL_LETTERS item=letter}
							<td class="startsWith{if (($VAL_FILTERSTART == "" && $letter == "All") || ($VAL_FILTERSTART == $letter))}Selected{/if}" id="btnStartsWith{$letter}" onmouseover="if(this.className!='startsWithSelected')this.className='startsWithHover';" onmouseout="if(this.className!='startsWithSelected')this.className='startsWith';" onclick="selectStartsWith(this, '{$letter}');">{$letter}</td>
						{/foreach}
						</tr></table>
{/strip}
					</td>
					<td style="text-align: right; white-space: nowrap;">
						<b>Page <input type="text" size="4" name="jumptopage" id="jumptopage" value="{$VAL_PAGE}"{$VAL_JUMPDISABLED}> / <span id="maxPages">{$VAL_PAGES}</span></b>
						<br />
						&nbsp;<input type="button" style="width: 54px; height: 18px;" name="btnNav" id="btnNavPrev" value="&lt;&lt;"{$VAL_PREVDISABLED} onclick="prevPage();">
						&nbsp;<input type="button" style="width: 54px; height: 18px;" name="btnNav" id="btnNavNext" value="&gt;&gt;"{$VAL_NEXTDISABLED} onclick="nextPage();">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table>
{strip}
	<form name="theForm">
	<tr>{foreach from=$VAL_HEADERS item=header}<th class="reportHeader">{$header}</th>{/foreach}</tr>
	{section name=org loop=$VAL_ORGS}
		{cycle values="odd,even" assign="rowClass"}
		<tr class="{$rowClass}">
		<td>{$VAL_ORGS[org].org_id}</td>
		<td>{if $VAL_ORGS[org].active == "Y"}{$smarty.const.STR_CMMN_YES}{else}{$smarty.const.STR_CMMN_NO}{/if}</td>
		<td><a href="{$URL_MAIN_PHP}?menuAction=Organization.Detail&org_id={$VAL_ORGS[org].org_id}">{$VAL_ORGS[org].name|escape}</a></td>
		<td>{$VAL_ORGS[org].phone_number|escape}</td>
		<td>{if $VAL_ORGS[org].email_addr != ""}{mailto address=$VAL_ORGS[org].email_addr}{else}&nbsp;{/if}</td>
		<td>{$VAL_ORGS[org].url_addr|escape|dcl_link}</td>
		</tr>
	{sectionelse}
		<tr><td colspan="2">No matches.</td></tr>
	{/section}
	</form>
{/strip}
</table>
