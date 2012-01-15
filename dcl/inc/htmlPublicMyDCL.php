<?php
/*
 * Double Choco Latte - Source Configuration Management System
 * Copyright (C) 1999  Michael L. Dean & Tim R. Norman
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
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

class htmlPublicMyDCL
{
	function show()
	{
		global $g_oSec;
		
		commonHeader();

		$t = new SmartyHelper();
		$t->assign('PERM_TICKETS', $g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED)))));
		$t->assign('PERM_WORKORDERS', $g_oSec->HasAnyPerm(array(DCL_ENTITY_WORKORDER => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED)))));
		$t->assign('PERM_FAQ', $g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW));

		$t->Render('PublicMyDCL.tpl');
	}
}
