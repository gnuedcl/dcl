<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

function smarty_function_dcl_select_outage_type($params, &$smarty)
{
	global $g_oSec, $g_oSession;

	if (!isset($params['name']))
		$params['name'] = 'outage_type_id';

	if (!isset($params['id']))
		$params['id'] = $params['name'];

	if (!isset($params['default']))
		$params['default'] = '';

	if (!isset($params['size']))
		$params['size'] = 1;

	$oDB = new OutageTypeModel();
	$oDB->cacheEnabled = false;

	$orderBy = 'outage_type_name';

	$query = "SELECT outage_type_id, outage_type_name, is_planned FROM dcl_outage_type ORDER BY $orderBy";

	$oDB->Query($query);

	$oSelect = new SelectHtmlHelper();
	$oSelect->DefaultValue = $params['default'];
	$oSelect->Id = $params['id'];
	$oSelect->Size = $params['size'];
	$oSelect->FirstOption = STR_CMMN_SELECTONE;
	$oSelect->CastToInt = true;

	while ($oDB->next_record())
		$oSelect->AddOption($oDB->f(0), $oDB->f(1), array('data-is-scheduled' => $oDB->f(2)));

	return $oSelect->GetHTML();
}