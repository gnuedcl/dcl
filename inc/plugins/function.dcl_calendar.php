<?php
/*
 * $Id: function.dcl_calendar.php,v 1.1.1.1 2006/11/27 05:30:52 mdean Exp $
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

function smarty_function_dcl_calendar($params, &$smarty)
{
	if (!isset($params['name']))
	{
		$smarty->trigger_error('dcl_calendar: missing parameter name');
		return;
	}

	if (!isset($params['id']))
		$params['id'] = $params['name'];

	if (!isset($params['value']))
		$params['value'] = '';
?>
<input type="text" name="<?php echo $params['name']; ?>" id="<?php echo $params['id']; ?>" size="10" maxlength="10" value="<?php echo $params['value']; ?>">&nbsp;<a href="javascript:;" onclick="showCalendar('<?php echo $params['id']; ?>');"><img src="img/calendar.gif" border="0"></a>
<?php
}
?>