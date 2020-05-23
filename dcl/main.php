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

$dcl_info = [
    'DCL_TIMESTAMP_FORMAT_DB' => 'Y-m-d H:i:s',
    'DCL_TIMESTAMP_FORMAT' => 'Y-m-d H:i:s',
    'DCL_DATE_FORMAT_DB' => 'Y-m-d',
    'DCL_DATE_FORMAT' => 'Y-m-d'
];

require_once('login.php');

if ($g_oSession->Value('ForcePasswordChange') == '1' && (!isset($_REQUEST['menuAction']) || $_REQUEST['menuAction'] != 'Personnel.ForcePasswordChangePost'))
{
	Invoke('Personnel.ForcePasswordChange');
}
else if (IsSet($_REQUEST['menuAction']))
{
	if ($g_oSec->ValidateMenuAction() == true)
	{
		InvokePlugin('UI.PubSub');
		Invoke($_REQUEST['menuAction']);
	}
	else
	{
		throw new InvalidArgumentException();
	}
}
else
{
	throw new InvalidArgumentException();
}
