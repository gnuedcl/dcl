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

LoadStringResource('bo');

class ContactAddressController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();

		$this->model = new ContactAddressModel();
		$this->sKeyField = 'contact_addr_id';
		$this->Entity = DCL_ENTITY_CONTACT;
		$this->PermAdd = DCL_PERM_MODIFY;
		$this->PermDelete = DCL_PERM_MODIFY;

		$this->sCreatedDateField = 'created_on';
		$this->sCreatedByField = 'created_by';
		$this->sModifiedDateField = 'modified_on';
		$this->sModifiedByField = 'modified_by';
		
		$this->aIgnoreFieldsOnUpdate = array('created_on', 'created_by', 'contact_id');
	}

	public function Create()
	{
		if (($contactId = Filter::ToInt($_REQUEST['contact_id'])) === null)
			throw new InvalidDataException();

		$presenter = new ContactAddressPresenter();
		$presenter->Create($contactId);
	}

	public function Insert()
	{
		global $dcl_info, $g_oSec;

		if (($id = Filter::ToInt($_POST['contact_id'])) === null ||
			($addr_type_id = Filter::ToInt($_POST['addr_type_id'])) === null
			)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		CleanArray($_POST);

		parent::Insert(array(
						'contact_id' => $id,
						'addr_type_id' => $addr_type_id,
						'add1' => $_POST['add1'],
						'add2' => $_POST['add2'],
						'city' => $_POST['city'],
						'state' => $_POST['state'],
						'zip' => $_POST['zip'],
						'country' => $_POST['country'],
						'preferred' => isset($_POST['preferred']) ? 'Y' : 'N',
						'created_on' => DCL_NOW,
						'created_by' => $GLOBALS['DCLID']
						)
					);

		SetRedirectMessage('Success', 'New address added successfully.');
		RedirectToAction('htmlContactDetail', 'show', 'contact_id=' . $id);
	}

	public function Edit()
	{
		if (($contactAddrId = Filter::ToInt($_REQUEST['contact_addr_id'])) === null)
			throw new InvalidDataException();

		if (($contactId = Filter::ToInt($_REQUEST['contact_id'])) === null)
			throw new InvalidDataException();

		$model = new ContactAddressModel();
		if ($model->Load($contactAddrId) == -1)
			throw new InvalidEntityException();

		$presenter = new ContactAddressPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $g_oSec;

		if (($contactAddrId = Filter::ToInt($_POST['contact_addr_id'])) === null)
			throw new InvalidDataException();

		if (($contactId = Filter::ToInt($_POST['contact_id'])) === null)
			throw new InvalidDataException();

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		CleanArray($_POST);
		$_POST['preferred'] = @Filter::ToYN($_POST['preferred']);
		parent::Update($_POST);

		SetRedirectMessage('Success', 'Address updated successfully.');
		RedirectToAction('htmlContactDetail', 'show', 'contact_id=' . $contactId);
	}

	public function Destroy()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($contactId = Filter::ToInt($_POST['contact_id'])) === null)
			throw new InvalidDataException();

		if (($id = Filter::ToInt($_POST['contact_addr_id'])) === null)
			throw new InvalidDataException();

		$aKey = array('contact_addr_id' => $id);
		parent::Destroy($aKey);
	}
}
