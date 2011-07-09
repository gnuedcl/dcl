<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2010 Free Software Foundation
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
class PersonnelModel extends DbProvider
{
	public function __construct()
	{
		parent::dclDB();
		$this->TableName = 'personnel';
		$this->cacheEnabled = true;

		LoadSchema($this->TableName);

		$this->foreignKeys = array(
				'workorders' => array('responsible', 'createby', 'closedby'),
				'workorders_audit' => array('responsible', 'createby', 'closedby'),
				'timecards' => array('actionby', 'reassign_from_id', 'reassign_to_id'),
				'tickets' => array('responsible', 'createdby', 'closedby'),
				'tickets_audit' => array('responsible', 'createdby', 'closedby'),
				'ticketresolutions' => 'loggedby',
				'watches' => 'whoid',
				'views' => 'whoid',
				'products' => array('reportto', 'ticketsto'),
				'dcl_projects' => 'reportto',
				'dcl_projects_audit' => 'reportto',
				'personnel' => 'reportto');

		parent::Clear();
	}

	public function Edit()
	{
		$query = 'UPDATE personnel SET ';
		$query .= 'short=' . $this->FieldValueToSQL('short', $this->short) . ',';
		$query .= 'reportto=' . $this->FieldValueToSQL('reportto', $this->reportto) . ',';
		$query .= 'department=' . $this->FieldValueToSQL('department', $this->department) . ',';
		$query .= 'active=' . $this->FieldValueToSQL('active', $this->active) . ',';
		$query .= 'contact_id=' . $this->FieldValueToSQL('contact_id', $this->contact_id);
		$query .= ' WHERE id=' . $this->FieldValueToSQL('id', $this->id);

		return $this->Execute($query);
	}

	public function Delete()
	{
		return parent::Delete(array('id' => $this->id));
	}

	public function Load($id)
	{
		return parent::Load(array('id' => $id));
	}
	
	public function LoadByLogin($sLogin)
	{
		if ($this->Query('SELECT ' . $this->SelectAllColumns() . ' FROM ' . $this->TableName . ' WHERE ' . $this->GetUpperSQL('short') . ' = ' . $this->Quote(strtoupper($sLogin))) != -1)
		{
			if ($this->next_record())
				return $this->GetRow();
		}
		
		return -1;
	}

	public function IsPasswordOK($userID, $password)
	{
		$this->Load($userID);
		if (md5($password) == $this->pwd)
			return true;
		else
			return false;
	}

	public function ChangePassword($userID, $oldPassword, $newPassword, $confirmPassword)
	{
		global $g_oSec;

		if (($userID = Filter::ToInt($userID)) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($GLOBALS['DCLID'] > 1 && !$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_PASSWORD))
		{
			if ($this->IsPasswordOK($userID, $oldPassword) == false)
			{
				trigger_error(STR_DB_WRONGPWD, E_USER_ERROR);
				$presenter = new PersonnelPresenter();
				$presenter->EditPassword();
				return;
			}
		}

		$query = 'UPDATE personnel SET pwd=' . $this->Quote(md5($newPassword)) . ' WHERE id=' . $userID;
		$this->Execute($query);
		trigger_error(STR_DB_PWDCHGSUCCESS, E_USER_NOTICE);
	}

	public function Encrypt()
	{
		$this->pwd = md5($this->pwd);
	}
}
