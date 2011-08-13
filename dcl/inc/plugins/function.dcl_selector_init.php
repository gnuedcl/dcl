<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
 *
 * Double Choco Latte is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * Double Choco Latte is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Select License Info from the Help menu to view the terms and conditions of this license.
 */

function smarty_function_dcl_selector_init($params, &$smarty)
{
?>
<script language="JavaScript">
var oSelectorWindow = null;
var oSelectorValue = null;
var oSecondaryValue = null;
var aSelectorText = null;
var aSecondaryText = null;
var fSelectorCallBack = function() {};

function showSelector(oValCtrl, aArrayText, fCallBack, sClass, sMultiple, sWindowName, oSecControl, aSecText)
{
	oSelectorValue = oValCtrl;
	oSecondaryValue = oSecControl;
	aSelectorText = aArrayText;
	aSecondaryText = aSecText;
	fSelectorCallBack = fCallBack;
	if (!sWindowName)
		sWindowName = '_dcl_selector_';

	if (oSelectorWindow != null && !oSelectorWindow.closed)
	{
		oSelectorWindow.focus();
	}
	else
	{
		var sURL = '<?php menuLink(); ?>?menuAction=' + sClass + '.show&multiple=' + sMultiple;
		if (oValCtrl.value != '')
			sURL += '&filterActive=S&filterID=' + escape(oValCtrl.value);
		else
			sURL += '&filterActive=Y';

		oSelectorWindow = window.open(sURL, sWindowName, 'width=600,height=450,scrollbars=yes,resizable=yes');
	}
}

function renderItems(oDiv, aItems)
{
	if (!oDiv)
		return;

	if (aItems.length == 0)
	{
		oDiv.innerHTML = '';
		return;
	}

	aItems.sort();
	var sHTML = '';
	for (var i = 0; i < aItems.length; i++)
	{
		if (i > 0)
			sHTML += "&nbsp;;&nbsp;";

		sHTML += '<span class="selecteditem">' + aItems[i] + '</span>';
	}

	oDiv.innerHTML = sHTML;
}
</script>
<?php
}
?>
