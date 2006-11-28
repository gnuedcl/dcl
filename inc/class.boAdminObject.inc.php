<?php
/*
 * $Id: class.boAdminObject.inc.php,v 1.1.1.1 2006/11/27 05:30:50 mdean Exp $
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

LoadStringResource('bo');

class boAdminObject
{
	var $oDB;
	var $Entity;
	var $PermAdd;
	var $PermModify;
	var $PermDelete;
	var $sKeyField;
	var $sDescField;
	var $sActiveField;
	var $sPublicField;
	var $aIgnoreFieldsOnUpdate;

	function boAdminObject()
	{
		// Should construct $this->oDB in derived class and set $this->sKeyField and $this->Entity for security
		$this->Entity = DCL_ENTITY_ADMIN;
		$this->PermAdd = DCL_PERM_ADD;
		$this->PermModify = DCL_PERM_MODIFY;
		$this->PermDelete = DCL_PERM_DELETE;
		$this->sKeyField = 'id';
		$this->sDescField = 'name';
		$this->sActiveField = 'active';
		$this->sPublicField = '';
		$this->aIgnoreFIeldsOnUpdate = array();
	}

	function add($aSource)
	{
		global $g_oSec;
		if (!$g_oSec->HasPerm($this->Entity, $this->PermAdd))
			return PrintPermissionDenied();

		$this->oDB->InitFromArray($aSource);
		if ($this->oDB->Add() == -1)
			return -1;

		if (isset($this->sKeyField) && $this->sKeyField != '')
			return $this->oDB->{$this->sKeyField};

		return 1;
	}

	function modify($aSource)
	{
		global $g_oSec;
		if (!$g_oSec->HasPerm($this->Entity, $this->PermModify))
			return PrintPermissionDenied();

		$this->oDB->InitFromArray($aSource);
		if ($this->oDB->Edit($this->aIgnoreFieldsOnUpdate) == -1)
			return -1;

		return 1;
	}

	function delete($aSource)
	{
		global $g_oSec;
		if (!$g_oSec->HasPerm($this->Entity, $this->PermDelete))
			return PrintPermissionDenied();

		if ($this->oDB->HasFKRef($aSource[$this->sKeyField]))
			return $this->oDB->SetActive(array($this->sKeyField => $aSource[$this->sKeyField]), false);

		return $this->oDB->Delete(array($this->sKeyField => $aSource[$this->sKeyField]));
	}
	
	function exists($aSource)
	{
		return $this->oDB->Exists($aSource);
	}

	function GetOptions($bActiveOnly = true)
	{
		return $this->oDB->GetOptions($this->sKeyField, $this->sDescField, $this->sActiveField, $bActiveOnly, $this->sPublicField);
	}
}
?>
