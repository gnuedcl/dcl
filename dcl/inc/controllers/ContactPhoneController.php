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

class ContactPhoneController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();

		$this->model = new ContactPhoneModel();
		$this->sKeyField = 'contact_phone_id';
		$this->Entity = DCL_ENTITY_CONTACT;
		$this->PermAdd = DCL_PERM_MODIFY;
		$this->PermDelete = DCL_PERM_MODIFY;

		$this->sCreatedDateField = 'created_on';
		$this->sCreatedByField = 'created_by';
		$this->sModifiedDateField = 'modified_on';
		$this->sModifiedByField = 'modified_by';
		
		$this->aIgnoreFieldsOnUpdate = array('created_on', 'created_by');
	}

	public function Create()
	{
		if (($contactId = @Filter::ToInt($_REQUEST['contact_id'])) === null)
			throw new InvalidDataException();

		$presenter = new ContactPhonePresenter();
		$presenter->Create($contactId);
	}

	public function Insert()
	{
		global $dcl_info, $g_oSec;

		if (($id = Filter::ToInt($_POST['contact_id'])) === null ||
			($phoneTypeId = Filter::ToInt($_POST['phone_type_id'])) === null
			)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		CleanArray($_POST);

		parent::Insert(array(
						'contact_id' => $id,
						'phone_type_id' => $phoneTypeId,
						'phone_number' => $_POST['phone_number'],
						'preferred' => @Filter::ToYN($_POST['preferred']),
						'created_on' => DCL_NOW,
						'created_by' => DCLID
						)
					);

		SetRedirectMessage('Success', 'New phone number added successfully.');
		RedirectToAction('htmlContactDetail', 'show', 'contact_id=' . $id);
	}

	public function Edit()
	{
		if (($contactPhoneId = Filter::ToInt($_REQUEST['contact_phone_id'])) === null)
			throw new InvalidDataException();

		if (($contactId = Filter::ToInt($_REQUEST['contact_id'])) === null)
			throw new InvalidDataException();

		$model = new ContactPhoneModel();
		if ($model->Load($contactPhoneId) == -1)
			throw new InvalidEntityException();

		$presenter = new ContactPhonePresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($contactId = Filter::ToInt($_POST['contact_id'])) === null)
			throw new InvalidDataException();

		CleanArray($_POST);
		$_POST['preferred'] = @Filter::ToYN($_POST['preferred']);
		parent::Update($_POST);

		SetRedirectMessage('Success', 'Phone updated successfully.');
		RedirectToAction('htmlContactDetail', 'show', 'contact_id=' . $contactId);
	}

	public function Destroy()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($contactId = Filter::ToInt($_POST['contact_id'])) === null)
			throw new InvalidDataException();

		if (($id = Filter::ToInt($_POST['contact_phone_id'])) === null)
			throw new InvalidDataException();

		$aKey = array('contact_phone_id' => $id);
		parent::Destroy($aKey);
	}
}
