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


LoadStringResource('sec');
LoadStringResource('usr');

class htmlSecAudit
{

	function show()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
		
		$objPersonnel = new PersonnelHtmlHelper();
		
		$oDBPersonnel = new PersonnelModel();
		if ($oDBPersonnel->Load($GLOBALS['DCLID']) == -1)
			return;
			
		$t = new DCL_Smarty();
		$oSelect = new htmlSelect();
		
		$t->assign('CMB_USERS', $objPersonnel->Select(0, 'responsible', 'lastfirst', 0, false));
		
		$begindate = @DCL_Sanitize::ToDate($_REQUEST['begindate']);
		if ($begindate !== null)
			$t->assign('VAL_BEGINDATE', $begindate);
		else
			$t->assign('VAL_BEGINDATE', '');

		$enddate = @DCL_Sanitize::ToDate($_REQUEST['enddate']);
		if ($enddate !== null)
			$t->assign('VAL_ENDDATE', $enddate);
		else
			$t->assign('VAL_ENDDATE', '');

		$t->Render('htmlSecAuditBrowse.tpl');
	}
	
	function Render($reportArray = NULL, $begindate, $enddate, $respname)
	{
	
		if (!isset($reportArray) || !is_array($reportArray))
			return false;
			
		$oTable = new htmlTable();
		
		$oTable->addColumn(STR_USR_LOGIN, 'string');
		$oTable->addColumn(STR_SEC_ACTIONON, 'numeric');
		$oTable->addColumn(STR_SEC_ACTIONTXT, 'string');
		$oTable->addColumn(STR_SEC_ACTIONPARAM, 'string');
		$oTable->setData($reportArray);
		
		$oTable->setCaption(sprintf(STR_SEC_SECLOGTITLE, $begindate, $enddate, $respname));
		
		$oTable->setShowRownum(true);
		$oTable->render();
			
		return true;
	}
}
