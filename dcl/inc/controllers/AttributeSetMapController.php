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

class AttributeSetMapController
{
	public function Index()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();

		$model = new AttributeSetModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new AttributeSetMapPresenter();
		$presenter->Index($model);
	}

	public function Edit()
	{
		if (($setId = @Filter::ToInt($_REQUEST['setid'])) === null)
			throw new InvalidDataException();

		if (($typeId = @Filter::ToInt($_REQUEST['typeid'])) === null)
			throw new InvalidDataException();

		$presenter = new AttributeSetMapPresenter();
		$presenter->Edit($setId, $typeId);
	}

	public function Update()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($setId = @Filter::ToInt($_REQUEST['setid'])) === null)
			throw new InvalidDataException();

		if (($typeId = @Filter::ToInt($_REQUEST['typeid'])) === null)
			throw new InvalidDataException();

		$model = new AttributeSetMapModel();
		$model->setid = $setId;
		$model->typeid = $typeId;

		$model->BeginTransaction();
		$model->DeleteBySetType($setId, $typeId);

		if (($aKeyID = @Filter::ToIntArray($_REQUEST['keyidset'])) !== null)
		{
			$i = 1;
			foreach ($aKeyID as $id)
			{
				$model->weight = $i;
				$model->keyid = $id;
				$model->Add();
				$i++;
			}
		}

		$model->EndTransaction();

		SetRedirectMessage('Success', 'Attribute set mapping updated successfully.');
		RedirectToAction('AttributeSetMap', 'Index', 'id=' . $setId);
	}
}