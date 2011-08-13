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

function smarty_function_dcl_calendar_init($params, &$smarty)
{
	if (!isset($params['format']))
	{
		if (!function_exists('GetJSDateFormat'))
		{
			$smarty->trigger_error('dcl_calendar_init: format parameter missing and GetJSDateFormat not defined');
			return;
		}

		$params['format'] = GetJSDateFormat();
	}

	$calDateFormat = str_replace('mm', '%m', $params['format']);
	$calDateFormat = str_replace('dd', '%d', $calDateFormat);
	$calDateFormat = str_replace('y', '%Y', $calDateFormat);
?>
<link rel="stylesheet" type="text/css" media="all" href="calendar/calendar-system.css" title="system" />
<script type="text/javascript" src="calendar/calendar.js"></script>
<script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
<script language="JavaScript">
	function _dateSelected(cal, date)
	{
		cal.sel.value = date;

		if (cal.dateClicked)
			cal.callCloseHandler();
	}

	function _closeHandler(cal)
	{
		cal.hide();
	}

	function showCalendar(id)
	{
		var el = document.getElementById(id);
		if (_dynarch_popupCalendar != null)
		{
			_dynarch_popupCalendar.hide();
		}
		else
		{
			var cal = new Calendar(false, null, _dateSelected, _closeHandler);
			cal.weekNumbers = false;
			_dynarch_popupCalendar = cal;
			cal.setRange(1900, 2070);
			cal.create();
		}

		_dynarch_popupCalendar.setDateFormat('<?php echo $calDateFormat; ?>');
		_dynarch_popupCalendar.parseDate(el.value);
		_dynarch_popupCalendar.sel = el;
		_dynarch_popupCalendar.showAtElement(el);

		return false;
	}
</script>
<?php
}
?>