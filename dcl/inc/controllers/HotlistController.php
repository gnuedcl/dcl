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

class HotlistController extends AbstractController
{
	public function Autocomplete()
	{
		$model = new HotlistModel();
		$matchArray = $model->filterList($_REQUEST['term']);

		$matches = array();
		foreach ($matchArray as $match)
		{
			$matches[] = array('id' => $match[0], 'value' => $match[1]);
		}

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($matches);
		exit;
	}

	public function Browse()
	{
		if (isset($_REQUEST['tag']) && trim($_REQUEST['tag'] != ''))
		{
			$this->browseByTag();
			return;
		}
		
		$this->cloud();
	}
	
	public function Cloud()
	{
		if (!HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH) && !HasPermission(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
			throw new PermissionDeniedException();
		
		$model = new HotlistModel();
		$model->listByPopular();
		
		$presenter = new HotlistPresenter();
		$presenter->Cloud($model);
	}
	
	public function BrowseByTag()
	{
		if (!HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH) && !HasPermission(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
			throw new PermissionDeniedException();
		
		if (!isset($_REQUEST['tag']) || trim($_REQUEST['tag']) == '')
			return $this->cloud();
			
		$model = new EntityHotlistModel();
		$model->listByTag($_REQUEST['tag']);
		
		$presenter = new HotlistPresenter();
		$presenter->BrowseByTag($model);
	}
	
	public function Prioritize()
	{
		RequirePermission(DCL_ENTITY_HOTLIST, DCL_PERM_MODIFY);
			
		$hotlistId = @Filter::RequireInt($_REQUEST['hotlist_id']);
		if ($hotlistId < 1)
			throw new InvalidEntityException();
			
		$hotlistModel = new HotlistModel();
		if ($hotlistModel->Load($hotlistId) === -1)
			throw new InvalidEntityException();
			
		$entityHotlistModel = new EntityHotlistModel();
		$entityHotlistModel->listById($hotlistId);

		$presenter = new HotlistPresenter();
		$presenter->Prioritize($hotlistModel, $entityHotlistModel);
	}
	
	public function SavePriority()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_HOTLIST, DCL_PERM_MODIFY);

		$hotlistId = @Filter::RequireInt($_POST['hotlist_id']);
		if ($hotlistId < 1)
			throw new InvalidEntityException();
			
		$hotlistModel = new HotlistModel();
		if ($hotlistModel->Load($hotlistId) === -1)
			throw new InvalidEntityException();

		$aEntities = array();
		$aRemoveEntities = array();
		foreach ($_POST['item'] as $entity)
		{
			$aEntity = @Filter::ToIntArray(split('_', $entity));
			if (count($aEntity) === 3)
			{
				if (in_array($entity, $_POST['remove']))
					$aRemoveEntities[] = $aEntity;
				else
					$aEntities[] = $aEntity;
			}
		}
			
		$db = new EntityHotlistModel();
		$db->setPriority($hotlistId, $aEntities);
		$db->RemoveEntities($hotlistId, $aRemoveEntities);
	}
}
