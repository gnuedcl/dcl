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

class htmlSession
{
	function Kill()
	{
		global $g_oSec, $g_oSession;
		
		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_SESSION, DCL_PERM_DELETE) || $_REQUEST['session_id'] == $g_oSession->dcl_session_id)
			throw new PermissionDeniedException();

		$o = new boSession();
		$o->Kill($_REQUEST);

		$this->Show();
	}

	function Show()
	{
		global $dcl_info, $g_oSec, $g_oSession;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_SESSION, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$oView = new boView();
		$oView->table = 'dcl_session';
		$oView->style = 'report';
		$oView->title = 'DCL Sessions';
		$oView->AddDef('columns', '', array('personnel.short', 'create_date', 'update_date', 'dcl_session_id'));
		$oView->AddDef('order', '', array('personnel.short', 'create_date'));

		$oView->AddDef('columnhdrs', '', array(
				'User',
				'Login Date',
				'Last Access',
				'Session ID'));

		$oDB = new dbSession();
		if ($oDB->query($oView->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption($oView->title);
		$oTable->addColumn('User', 'string');
		$oTable->addColumn('Login Date', 'string');
		$oTable->addColumn('Last Access', 'string');
		$oTable->addColumn('Session ID', 'string');
		
		$oTable->addToolbar(menuLink('', 'menuAction=htmlSession.Show'), 'Refresh');
		$oTable->addToolbar(menuLink('', 'menuAction=htmlSession.Detail'), STR_CMMN_VIEW);
			
		if (count($allRecs) > 0 && $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_ENTITY_ADMIN))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($allRecs[$i][3] != $g_oSession->dcl_session_id)
					$options = '<a href="' . menuLink('', 'menuAction=htmlSession.Kill&session_id=' . $allRecs[$i][3]) . '">' . 'Kill' . '</a>';

				$allRecs[$i][] = $options;
			}
		}
		
		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	function Detail()
	{
		global $g_oSession, $g_oSec, $dcl_info;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_SESSION, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		echo '<pre>';
		print_r($g_oSession->session_data);
		echo '</pre>';
	}
}
