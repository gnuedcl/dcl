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
			var bInitComplete = false;
			
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
					oLastButton.className = 'dcl_startsWith';
		
				oLastButton = oButton;
				oButton.className = 'dcl_startsWithSelected';
				
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
				var sURL = '{$smarty.const.DCL_WWW_ROOT}main.php?menuAction=htmlProjectSelector.showBrowseFrame';
				sURL += '&filterStartsWith=' + sStartsWith;
				sURL += '&multiple={$VAL_MULTIPLE}';
{literal}
				var oStatus = document.getElementById('filterStatus');
				if (oStatus)
				{
					sURL += '&filterStatus=' + oStatus.options[oStatus.selectedIndex].value;
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
				
				bInitComplete = true;
			}
{/literal}
		</script>
	</head>
	<body onload="init();">
		<table style="width: 100%;" cellspacing="0">
			<tr>
				<th class="formTitle">Select Project</th>
				<th class="formLinks"><a href="#" onclick="doSave();">{$smarty.const.STR_CMMN_SAVE}</a>&nbsp;|&nbsp;<a href="#" onclick="doCancel();">{$smarty.const.STR_CMMN_CANCEL}</a></th>
			</tr>
			<tr>
				<td class="formContainer" colspan="2">
					<table style="width: 100%;">
						<tr>
							<td>
								{dcl_select_status default=$VAL_FILTERSTATUS name=filterStatus allowHideOrOnlyClosed=Y}&nbsp;|&nbsp;
								<input type="text" name="filterName" id="filterName" value="" size="10">&nbsp;|&nbsp;
								<input type="button" name="filter" id="filter" value="Filter" onclick="applyFilter();">
								<br />
{strip}
								<div class="dcl_filter_selectstart" style="margin-top:6px;">
									{foreach from=$VAL_LETTERS item=letter}
										<div class="dcl_startsWith{if (($VAL_FILTERSTART == "" && $letter == "All") || ($VAL_FILTERSTART == $letter))}Selected{/if}" id="btnStartsWith{$letter}" onmouseover="if(this.className!='dcl_startsWithSelected')this.className='dcl_startsWithHover';" onmouseout="if(this.className!='dcl_startsWithSelected')this.className='dcl_startsWith';" onclick="selectStartsWith(this, '{$letter}');">{$letter}</div>
									{/foreach}
								</div>
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
