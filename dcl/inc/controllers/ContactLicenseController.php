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

class ContactLicenseController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->model = new ContactLicenseModel();
		$this->sKeyField = 'contact_license_id';
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
		if (($contactId = @DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null)
			throw new InvalidDataException();

		$presenter = new ContactLicensePresenter();
		$presenter->Create($contactId);
	}

	public function Insert()
	{
		global $dcl_info, $g_oSec;

		if (($contactId = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null ||
			($productId = DCL_Sanitize::ToInt($_REQUEST['product_id'])) === null ||
			($registeredOn = DCL_Sanitize::ToDate($_REQUEST['registered_on'])) === null ||
			($expiresOn = DCL_Sanitize::ToDate($_REQUEST['expires_on'])) === null
			)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		CleanArray($_POST);

		parent::Insert(array(
						'contact_id' => $contactId,
						'product_id' => $productId,
						'product_version' => $_REQUEST['product_version'],
		                'license_id' => $_REQUEST['license_id'],
		                'registered_on' => $registeredOn,
		                'expires_on' => $expiresOn,
						'license_notes' => $_REQUEST['license_notes'],
						'created_on' => DCL_NOW,
						'created_by' => $GLOBALS['DCLID']
						)
					);

		SetRedirectMessage('Success', 'New license added successfully.');
		RedirectToAction('htmlContactDetail', 'show', 'contact_id=' . $contactId);
	}

	public function Edit()
	{
		if (($contactLicenseId = DCL_Sanitize::ToInt($_REQUEST['contact_license_id'])) === null)
			throw new InvalidDataException();

		if (($contactId = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null)
			throw new InvalidDataException();

		$model = new ContactLicenseModel();
		if ($model->Load($contactLicenseId) == -1)
			throw new InvalidEntityException();

		$presenter = new ContactLicensePresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($contactLicenseId = DCL_Sanitize::ToInt($_REQUEST['contact_license_id'])) === null ||
		    ($contactId = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null ||
			($productId = DCL_Sanitize::ToInt($_REQUEST['product_id'])) === null ||
			($registeredOn = DCL_Sanitize::ToDate($_REQUEST['registered_on'])) === null  ||
			($expiresOn = DCL_Sanitize::ToDate($_REQUEST['expires_on'])) === null
			)
		{
			throw new InvalidDataException();
		}

		CleanArray($_POST);
		parent::Update(array(
		                'contact_license_id' => $contactLicenseId,
						'contact_id' => $contactId,
						'product_id' => $productId,
						'product_version' => $_REQUEST['product_version'],
		                'license_id' => $_REQUEST['license_id'],
		                'registered_on' => $registeredOn,
		                'expires_on' => $expiresOn,
						'license_notes' => $_REQUEST['license_notes'],
						'modified_on' => DCL_NOW,
						'modified_by' => $GLOBALS['DCLID']
						));

		SetRedirectMessage('Success', 'License updated successfully.');
		RedirectToAction('htmlContactDetail', 'show', 'contact_id=' . $contactId);
	}

	public function Destroy()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($contactId = DCL_Sanitize::ToInt($_POST['contact_id'])) === null)
			throw new InvalidDataException();

		if (($id = DCL_Sanitize::ToInt($_POST['contact_license_id'])) === null)
			throw new InvalidDataException();

		$aKey = array('contact_license_id' => $id);
		parent::Destroy($aKey);
	}
}
