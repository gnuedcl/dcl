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

class SessionController
{
	public function Kill()
	{
		global $g_oSession;

		RequirePost();
		RequirePermission(DCL_ENTITY_SESSION, DCL_PERM_DELETE);
		if ($_REQUEST['session_id'] == $g_oSession->dcl_session_id)
			throw new PermissionDeniedException();

		$model = new SessionModel();
		$model->dcl_session_id = $_REQUEST['session_id'];
		$model->Delete(array('dcl_session_id' => $model->dcl_session_id));

		SetRedirectMessage('Success', 'Session deleted.');
		RedirectToAction('Session', 'Index');
	}

	public function Index()
	{
		RequirePermission(DCL_ENTITY_SESSION, DCL_PERM_VIEW);

		$presenter = new SessionPresenter();
		$presenter->Index();
	}
}
