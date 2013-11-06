<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2012 Free Software Foundation
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

LoadStringResource('db');
class ErrorLogModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_error_log';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	public function Add()
	{
		$this->user_id = DCLID;
		$this->error_timestamp = DCL_NOW;
		return parent::Add();
	}

	public function Edit($aIgnoreFields = '')
	{
		throw new NotImplementedException();
	}

	public function Delete($aID)
	{
		throw new NotImplementedException();
	}

	public function Load($id, $bTriggerErrorIfNotFound = true)
	{
		return parent::Load(array('error_log_id' => $id));
	}
}
