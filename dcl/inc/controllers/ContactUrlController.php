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

class ContactUrlController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();

		$this->model = new ContactUrlModel();
		$this->sKeyField = 'contact_url_id';
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

		$presenter = new ContactUrlPresenter();
		$presenter->Create($contactId);
	}

	public function Insert()
	{
		global $dcl_info, $g_oSec;

		if (($id = Filter::ToInt($_POST['contact_id'])) === null ||
			($urlTypeId = Filter::ToInt($_POST['url_type_id'])) === null
			)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		CleanArray($_POST);

		parent::InsertFromArray(array(
						'contact_id' => $id,
						'url_type_id' => $urlTypeId,
						'url_addr' => $_POST['url_addr'],
						'preferred' => @Filter::ToYN($_POST['preferred']),
						'created_on' => DCL_NOW,
						'created_by' => DCLID
						)
					);

		SetRedirectMessage('Success', 'New URL added successfully.');
		RedirectToAction('htmlContactDetail', 'show', 'contact_id=' . $id);
	}

	public function Edit()
	{
		if (($contactUrlId = Filter::ToInt($_REQUEST['contact_url_id'])) === null)
			throw new InvalidDataException();

		if (($contactId = Filter::ToInt($_REQUEST['contact_id'])) === null)
			throw new InvalidDataException();

		$model = new ContactUrlModel();
		if ($model->Load($contactUrlId) == -1)
			throw new InvalidEntityException();

		$presenter = new ContactUrlPresenter();
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
		parent::UpdateFromArray($_POST);

		SetRedirectMessage('Success', 'URL updated successfully.');
		RedirectToAction('htmlContactDetail', 'show', 'contact_id=' . $contactId);
	}

	public function Destroy()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($contactId = Filter::ToInt($_POST['contact_id'])) === null)
			throw new InvalidDataException();

		if (($id = Filter::ToInt($_POST['contact_url_id'])) === null)
			throw new InvalidDataException();

		$aKey = array('contact_url_id' => $id);
		parent::DestroyFromArray($aKey);
	}
}
