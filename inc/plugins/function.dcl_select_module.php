<?php
/*
 * $Id: function.dcl_select_module.php,v 1.1.1.1 2006/11/27 05:30:52 mdean Exp $
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

function smarty_function_dcl_select_module($params, &$smarty)
{
	global $g_oSec;
	
	if (!isset($params['name']))
		$params['name'] = 'module_id';

	if (!isset($params['id']))
		$params['id'] = $params['name'];

	if (!isset($params['default']))
		$params['default'] = '';

	$params['active'] = (!isset($params['active']) || $params['active'] == 'Y') ? 'Y' : 'N';

	if (!isset($params['setid']))
		$params['setid'] = 0;

	if (!isset($params['size']))
		$params['size'] = 1;

	if (!isset($params['onchange']))
		$params['onchange'] = '';

	if (!isset($params['product']))
		$params['product'] = 0;

	$sFilter = '';
	if ($params['active'] == 'Y')
		$sFilter = "pm.active = 'Y'";
		
	if ($g_oSec->IsPublicUser())
	{
		$sFilter .= ($sFilter == '' ? '' : ' AND ') . "pm.product_id = p.id AND p.is_public = 'Y'";
		if ($params['active'] == 'Y')
			$sFilter .= " AND p.active = 'Y'";
	}

	if ($params['product'] > 0)
		$sFilter .= ($sFilter == '' ? '' : ' AND ') . "product_id = " . $params['product'];

	$oSelect =& CreateObject('dcl.htmlSelect');
	$oSelect->vDefault = $params['default'];
	$oSelect->sName = $params['name'];
	$oSelect->iSize = $params['size'];
	$oSelect->sOnChange = $params['onchange'];
	$oSelect->sZeroOption = STR_CMMN_SELECTONE;
	
	if ($g_oSec->IsPublicUser())
	{
		$oSelect->SetOptionsFromDb('dcl_product_module pm, products p', 'product_module_id', 'module_name', $sFilter, 'module_name');
	}
	else
	{
		$oSelect->SetOptionsFromDb('dcl_product_module pm', 'product_module_id', 'module_name', $sFilter, 'module_name');
	}

	return $oSelect->GetHTML();
}
?>