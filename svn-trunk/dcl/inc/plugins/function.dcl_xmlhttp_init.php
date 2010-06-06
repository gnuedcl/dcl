<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
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

function smarty_function_dcl_xmlhttp_init($params, &$smarty)
{
?>
<script language="JavaScript">
if (typeof XMLHttpRequest == "undefined")
{
	XMLHttpRequest = function()
	{
		try { return new ActiveXObject("Msxml2.XMLHTTP.6.0") } catch(e) {}
		try { return new ActiveXObject("Msxml2.XMLHTTP.3.0") } catch(e) {}
		try { return new ActiveXObject("Msxml2.XMLHTTP") }     catch(e) {}
		try { return new ActiveXObject("Microsoft.XMLHTTP") }  catch(e) {}
		throw new Error( "This browser does not support XMLHttpRequest or XMLHTTP." )
	};
}

function Request(sURL, sVars, fnCallback)
{
	var oHTTP = new XMLHttpRequest();
	oHTTP.open("POST", sURL, true);
	oHTTP.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
	
	oHTTP.onreadystatechange = function()
	{
		if (oHTTP.readyState == 4 && oHTTP.status == 200)
		{
			if (oHTTP.responseText)
				fnCallback(oHTTP.responseText);
		}
	};
	
	oHTTP.send(sVars);
}

function ParseJSON(sJSON)
{
	try
	{
		//if (/^("(\\.|[^"\\\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/.test(sJSON))
		{
			var j = eval('(' + sJSON + ')');
			return j;
		}
	}
	catch(e)
	{
	}
	
	//throw new SyntaxError("ParseJSON");
}

function RequestJSON(sURL, sVars, fnCallback)
{
	var oHTTP = new XMLHttpRequest();
	oHTTP.open("POST", sURL, true);
	oHTTP.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
	
	oHTTP.onreadystatechange = function()
	{
		if (oHTTP.readyState == 4 && oHTTP.status == 200)
		{
			if (oHTTP.responseText)
				fnCallback(ParseJSON(oHTTP.responseText));
		}
	};
	
	oHTTP.send(sVars);	
}
</script>
<?php
}
?>