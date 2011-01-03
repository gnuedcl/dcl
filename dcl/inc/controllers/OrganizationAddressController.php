<?php
/*
 * $Id$
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

class OrganizationAddressController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();

		$this->model = new OrganizationAddressModel();
		$this->sKeyField = 'org_addr_id';
		$this->Entity = DCL_ENTITY_ORG;
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
		if (($orgId = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null)
			throw new InvalidDataException();

		$presenter = new OrganizationAddressPresenter();
		$presenter->Create($orgId);
	}

	public function Insert()
	{
		global $dcl_info, $g_oSec;

		if (($id = DCL_Sanitize::ToInt($_POST['org_id'])) === null ||
			($addr_type_id = DCL_Sanitize::ToInt($_POST['addr_type_id'])) === null
			)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		CleanArray($_POST);

		parent::Insert(array(
						'org_id' => $id,
						'addr_type_id' => $addr_type_id,
						'add1' => $_POST['add1'],
						'add2' => $_POST['add2'],
						'city' => $_POST['city'],
						'state' => $_POST['state'],
						'zip' => $_POST['zip'],
						'country' => $_POST['country'],
						'preferred' => @DCL_Sanitize::ToYN($_POST['preferred']),
						'created_on' => DCL_NOW,
						'created_by' => $GLOBALS['DCLID']
						)
					);

		SetRedirectMessage('Success', 'New address added successfully.');
		RedirectToAction('htmlOrgDetail', 'show', 'org_id=' . $id);
	}

	public function Edit()
	{
		if (($orgAddrId = DCL_Sanitize::ToInt($_REQUEST['org_addr_id'])) === null)
			throw new InvalidDataException();

		if (($orgId = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null)
			throw new InvalidDataException();

		$model = new OrganizationAddressModel();
		if ($model->Load($orgAddrId) == -1)
			throw new InvalidEntityException();

		$presenter = new OrganizationAddressPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $g_oSec;

		if (($orgAddrId = DCL_Sanitize::ToInt($_POST['org_addr_id'])) === null)
			throw new InvalidDataException();

		if (($orgId = DCL_Sanitize::ToInt($_POST['org_id'])) === null)
			throw new InvalidDataException();

		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		CleanArray($_POST);
		$_POST['preferred'] = @DCL_Sanitize::ToYN($_POST['preferred']);
		parent::Update($_POST);

		SetRedirectMessage('Success', 'Address updated successfully.');
		RedirectToAction('htmlOrgDetail', 'show', 'org_id=' . $orgId);
	}

	public function Destroy()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($orgId = DCL_Sanitize::ToInt($_POST['org_id'])) === null)
			throw new InvalidDataException();

		if (($id = DCL_Sanitize::ToInt($_POST['org_addr_id'])) === null)
			throw new InvalidDataException();

		$aKey = array('org_addr_id' => $id);
		parent::Destroy($aKey);
	}
}
