<?php
/*
 * $Id: class.htmlProjectsform.inc.php,v 1.1.1.1 2006/11/27 05:30:46 mdean Exp $
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

LoadStringResource('prj');
LoadStringResource('wo');

class htmlProjectsform
{
	function Show($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			return PrintPermissionDenied();

		$t = CreateSmarty();

		$objPrj = CreateObject('dcl.htmlProjects');
		$objHTMLPersonnel = CreateObject('dcl.htmlPersonnel');
		$oStatus = CreateObject('dcl.htmlStatuses');
		if ($isEdit)
		{
			$t->assign('VAL_PROJECTID', $obj->projectid);
			$t->assign('VAL_NAME', $obj->name);
			$t->assign('CMB_PARENTPRJ', $objPrj->GetCombo($obj->parentprojectid, 'parentprojectid', 0, 0, $obj->projectid));
			$t->assign('CMB_REPORTTO', $objHTMLPersonnel->GetCombo($obj->reportto, 'reportto'));
			$t->assign('CMB_STATUS', $oStatus->GetCombo($obj->status));
			$t->assign('VAL_PROJECTDEADLINE', $obj->projectdeadline);
			$t->assign('VAL_DESCRIPTION', $obj->description);
		}
		else
		{
			$t->assign('VAL_NAME', '');
			$t->assign('CMB_PARENTPRJ', $objPrj->GetCombo(0, 'parentprojectid'));
			$t->assign('CMB_REPORTTO', $objHTMLPersonnel->GetCombo($GLOBALS['DCLID'], 'reportto'));
			$t->assign('CMB_STATUS', $oStatus->GetCombo(1));
			$t->assign('VAL_PROJECTDEADLINE', '');
			$t->assign('VAL_DESCRIPTION', '');
		}

		$t->assign('IS_EDIT', $isEdit);
		
		if ($dcl_info['DCL_PROJECT_XML_TEMPLATES'] == 'Y' && !$isEdit)
		{
			$objXMLProject = CreateObject('dcl.xmlProjects');
			$objXMLProject->createCombo();
			
			$t->assign('CMB_XMLPROJECTS', $objXMLProject->comboHTML);
			$t->assign('XML_TEMPLATES', true);
			$t->assign('JS_TEMPLATE', $objXMLProject->comboJS);
		}

		SmartyDisplay($t, 'htmlProjectsForm.tpl');
	}
}
?>
