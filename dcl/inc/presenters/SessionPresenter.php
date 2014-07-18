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

class SessionPresenter
{
	public function Index()
	{
		global $g_oSec, $g_oSession;
		
		commonHeader();
		
		$queryHelper = new SessionSqlQueryHelper();
		$queryHelper->title = 'DCL Sessions';
		$queryHelper->AddDef('columns', '', array('personnel.short', 'create_date', 'update_date', 'dcl_session_id'));
		$queryHelper->AddDef('order', '', array('personnel.short', 'create_date'));

		$queryHelper->AddDef('columnhdrs', '', array(
				'User',
				'Login Date',
				'Last Access',
				'Session ID'));

		$model = new SessionModel();
		if ($model->query($queryHelper->GetSQL()) == -1)
			throw new SqlQueryException();
		
		$allRecs = $model->FetchAllRows();

		$tableHelper = new TableHtmlHelper();
		$tableHelper->setCaption($queryHelper->title);
		$tableHelper->addColumn('User', 'string');
		$tableHelper->addColumn('Login Date', 'string');
		$tableHelper->addColumn('Last Access', 'string');
		$tableHelper->addColumn('Session ID', 'string');
		
		$tableHelper->addToolbar(menuLink('', 'menuAction=Session.Index'), 'Refresh');

		if (count($allRecs) > 0 && $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_ENTITY_ADMIN))
		{
			$tableHelper->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($allRecs[$i][3] != $g_oSession->dcl_session_id)
					$options = '<a href="' . menuLink('', 'menuAction=Session.Kill&session_id=' . $allRecs[$i][3]) . '">' . 'Kill' . '</a>';

				$allRecs[$i][] = $options;
			}
		}
		
		$tableHelper->setData($allRecs);
		$tableHelper->setShowRownum(true);
		$tableHelper->render();
	}
}