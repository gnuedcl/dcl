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

function smarty_function_dcl_select_product_version($params, &$smarty)
{
	global $g_oSec, $g_oSession;

	if (!isset($params['name']))
		$params['name'] = 'product_version_id';

	if (!isset($params['id']))
		$params['id'] = $params['name'];

	if (!isset($params['default']))
		$params['default'] = '';

	if (!isset($params['size']))
		$params['size'] = 1;

	if (!isset($params['onchange']))
		$params['onchange'] = '';

	$sFilter = '';
	if ($params['active'] == 'Y')
		$sFilter = "active = 'Y'";
		
	if (isset($params['product']) && $params['product'] != '')
	{
		if ($sFilter != '')
			$sFilter .= ' AND ';
			
		$sFilter .= 'product_id = ' . $params['product'];
	}
	
	$oSelect = new SelectHtmlHelper();
	$oSelect->DefaultValue = $params['default'];
	$oSelect->Id = $params['name'];
	$oSelect->Size = $params['size'];
	$oSelect->OnChange = $params['onchange'];
	$oSelect->FirstOption = STR_CMMN_SELECTONE;
	$oSelect->SetOptionsFromDb('dcl_product_version', 'product_version_id', 'product_version_text', $sFilter, 'product_version_actual_date desc, product_version_text');

	return $oSelect->GetHTML();
}
