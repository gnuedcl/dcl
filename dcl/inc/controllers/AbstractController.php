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

LoadStringResource('bo');

abstract class AbstractController
{
	protected $model;
	protected $Entity;
	protected $PermAdd;
	protected $PermModify;
	protected $PermDelete;
	protected $sKeyField;
	protected $sDescField;
	protected $sActiveField;
	protected $sPublicField;
	protected $sCreatedDateField;
	protected $sModifiedDateField;
	protected $sCreatedByField;
	protected $sModifiedByField;
	protected $aIgnoreFieldsOnUpdate;

	public function __construct()
	{
		// Override these
		$this->Entity = DCL_ENTITY_ADMIN;
		$this->sKeyField = 'id';

		// Change these as necessary
		$this->PermAdd = DCL_PERM_ADD;
		$this->PermModify = DCL_PERM_MODIFY;
		$this->PermDelete = DCL_PERM_DELETE;
		$this->sDescField = 'name';
		$this->sActiveField = 'active';
		$this->sPublicField = '';
		$this->sCreatedDateField = '';
		$this->sModifiedDateField = '';
		$this->sCreatedByField = '';
		$this->sModifiedByField = '';
		$this->aIgnoreFIeldsOnUpdate = array();
	}

	protected function InsertFromArray(array $aSource)
	{
		RequirePermission($this->Entity, $this->PermAdd);

		$this->model->InitFromArray($aSource);

		if ($this->sCreatedDateField != '')
			$this->model->{$this->sCreatedDateField} = DCL_NOW;

		if ($this->sCreatedByField != '')
			$this->model->{$this->sCreatedByField} = DCLID;

		if ($this->sModifiedDateField != '')
			$this->model->{$this->sModifiedDateField} = DCL_NOW;

		if ($this->sModifiedByField != '')
			$this->model->{$this->sModifiedByField} = DCLID;

		if ($this->model->Add() == -1)
		{
			throw new Exception('Add new item failed.');;
		}

		if (isset($this->sKeyField) && $this->sKeyField != '')
			return $this->model->{$this->sKeyField};

		return 1;
	}

	protected function UpdateFromArray(array $aSource)
	{
		RequirePermission($this->Entity, $this->PermModify);

		$this->model->InitFromArray($aSource);
		if ($this->sModifiedDateField != '')
			$this->model->{$this->sModifiedDateField} = DCL_NOW;

		if ($this->sModifiedByField != '')
			$this->model->{$this->sModifiedByField} = DCLID;

		if ($this->model->Edit($this->aIgnoreFieldsOnUpdate) == -1)
		{
			throw new Exception('Update item failed.');
		}
	}

	protected function DestroyFromArray(array $aSource)
	{
		RequirePermission($this->Entity, $this->PermDelete);

		if ($this->model->HasFKRef($aSource[$this->sKeyField]))
			return $this->model->SetActive($aSource, false);

		return $this->model->Delete($aSource);
	}

	public function Exists($aSource)
	{
		return $this->model->Exists($aSource);
	}

	public function GetOptions($bActiveOnly = true)
	{
		return $this->model->GetOptions($this->sKeyField, $this->sDescField, $this->sActiveField, $bActiveOnly, $this->sPublicField);
	}
}