<html>
	<!-- $Id$ -->
	<head>
		<link rel="stylesheet" type="text/css" href="{$DIR_CSS}default.css" />
		<script language="JavaScript">
{literal}
			var oLastButton = null;
			var sStartsWith = '';
			var sActiveFilter = '';
			var iPage = 1;
			var iMaxPages = 1;
			var aSelectedID = new Array();
			var aSelectedName = new Array();

			function doSave()
			{
				var oOpener = parent.window.opener;
				var oValues = oOpener.oSelectorValue;
				var aText = oOpener.aSelectorText;

				oValues.value = "";
				aText.length = 0;

				if (aSelectedID.length > 0)
				{
					var bFirst = true;
					for (var sKey in aSelectedID)
					{
						if (!sKey || !aSelectedID[sKey])
							continue;

						if (!bFirst)
							oValues.value += ",";
						else
							bFirst = false;

						oValues.value += sKey;
						aText[aText.length] = aSelectedName[sKey];
					}
				}

				if (typeof(oOpener.fSelectorCallBack) == "function" || (document.all && typeof(oOpener.fSelectorCallBack) == "object"))
					oOpener.fSelectorCallBack();

				closeWindow();
			}

			function doCancel()
			{
				closeWindow();
			}

			function closeWindow()
			{
				parent.window.opener.oSelectorWindow = null;
				parent.window.close();
			}

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
{/literal}
				var sURL = '{$smarty.const.DCL_WWW_ROOT}main.php?menuAction=htmlPersonnelSelector.showBrowseFrame';
				sURL += '&filterStartsWith=' + sStartsWith;
				sURL += '&multiple={$VAL_MULTIPLE}';
{literal}
				if (sActiveFilter == "S")
				{
					if (aSelectedID.length > 0)
					{
						var sID = '';
						for (var sKey in aSelectedID)
						{
							if (!aSelectedID[sKey])
								continue;

							if (sID.length > 0)
								sID += ',';

							sID += sKey;
						}

						if (sID.length > 0)
							sURL += '&filterID=' + sID;
					}
				}
				else
				{
					sURL += '&filterActive=' + sActiveFilter;
				}

				var oSearch = document.getElementById('filterName');
				if (oSearch.value != '')
					sURL += '&filterSearch=' + escape(oSearch.value);

				return sURL;
			}

			function applyFilter()
			{
				parent.mainFrame.location.href = getFilter();
			}

			function nextPage()
			{
				iPage++;
				if (iPage > iMaxPages)
					iPage = iMaxPages;

				var sURL = getFilter();
				sURL += '&page=' + iPage;

				parent.mainFrame.location.href = sURL;
			}

			function prevPage()
			{
				iPage--;
				if (iPage < 1)
					iPage = 1;

				var sURL = getFilter();
				sURL += '&page=' + iPage;

				parent.mainFrame.location.href = sURL;
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

				parent.mainFrame.location.href = sURL;
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
				var oOpener = parent.window.opener;

				document.getElementById('jumptopage').onkeydown = jumpToPage;
				document.getElementById('filterName').onkeydown = searchName;
				var oValues = oOpener.oSelectorValue;
				var aText = oOpener.aSelectorText;

				if (oValues && oValues.value.length > 0)
				{
					var sValues = oValues.value;
					var aID = sValues.split(",");
					for (var i = 0; i < aID.length; i++)
					{
						aSelectedID[aID[i]] = true;
						aSelectedName[aID[i]] = aText[i];
					}
				}
			}
{/literal}
		</script>
	</head>
	<body onload="init();">
		<table style="width: 100%;" cellspacing="0">
			<tr>
				<th class="formTitle">Select User</th>
				<th class="formLinks"><a class="adark" href="#" onclick="doSave();">{$smarty.const.STR_CMMN_SAVE}</a>&nbsp;|&nbsp;<a class="adark" href="#" onclick="doCancel();">{$smarty.const.STR_CMMN_CANCEL}</a></th>
			</tr>
			<tr>
				<td class="formContainer" colspan="2">
					<table style="width: 100%;">
						<tr>
							<td>
								<input type="radio" name="filterActive" id="filterActiveAll" value="" onclick="selectActive('');"{if $VAL_FILTERACTIVE eq "" && $VAL_FILTERID eq ""} checked{/if}><label for="filterActiveAll"><b>All</b></label>&nbsp;
								<input type="radio" name="filterActive" id="filterActiveYes" value="Y" onclick="selectActive('Y');"{if $VAL_FILTERACTIVE eq "Y" && $VAL_FILTERID eq ""} checked{/if}><label for="filterActiveYes"><b>Active</b></label>&nbsp;
								<input type="radio" name="filterActive" id="filterActiveNo" value="N" onclick="selectActive('N');"{if $VAL_FILTERACTIVE eq "N" && $VAL_FILTERID eq ""} checked{/if}><label for="filterActiveNo"><b>Inactive</b></label>&nbsp;
								<input type="radio" name="filterActive" id="filterActiveSelected" value="S" onclick="selectActive('S');"{if $VAL_FILTERID neq ""} checked{/if}><label for="filterActiveSelected"><b>Selected</b></label>&nbsp;|&nbsp;
								<input type="text" name="filterName" id="filterName" value="" size="10">
								<br />
{strip}
								<table style="border: none 0px; margin-bottom: 0px;" cellpadding="0"><tr>
								{foreach from=$VAL_LETTERS item=letter}
									<td class="startsWith" id="btnStartsWith{$letter}" onmouseover="if(this.className!='startsWithSelected')this.className='startsWithHover';" onmouseout="if(this.className!='startsWithSelected')this.className='startsWith';" onclick="selectStartsWith(this, '{$letter}');">{$letter}</td>
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
	</body>
</html>
