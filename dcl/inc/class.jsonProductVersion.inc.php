<?php
/*
 * $Id: schema.dcl_entity_perm.php 12 2006-12-01 01:46:51Z mdean $
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

class jsonProductVersion
{
	function jsonProductVersion()
	{
		
	}
	
	function ListVersions()
	{
		// FIXME: application/x-javascript for Opera
		header('Content-Type: application/json');
		$product_id = @DCL_Sanitize::ToInt($_REQUEST['product_id']);
		if ($product_id === null)
			exit;
		
		$oDB = new dbProductVersion();
		$aOptions = $oDB->GetOptions('product_version_id', 'product_version_text', 'active', (isset($_REQUEST['active']) && $_REQUEST['active'] == 'Y'), '', "product_id=$product_id");

		$bFirst = true;
		echo '{';
		echo '"totalRecords":', count($aOptions), ',';
		echo '"data":[';
		for ($i = 0; $i < count($aOptions); $i++)
		{
			if ($i > 0)
				echo ',';
				
			
			echo '{';
			echo '"id":', $aOptions[$i]['product_version_id'], ',';
			echo '"text":"', str_replace('"', '\"', str_replace("\\", "\\\\", $aOptions[$i]['product_version_text'])), '"';
			echo '}';
		}
		
		echo ']}';

		exit;
	}
}
