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
class ProductController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		$this->model = new ProductModel();
		$this->sKeyField = 'id';
		$this->sDescField = 'name';
		$this->sPublicField = 'is_public';
		$this->Entity = DCL_ENTITY_PRODUCT;
	}

	public function Index()
	{
		$presenter = new ProductPresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new ProductPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new ProductModel();
		$obj->InitFrom_POST();
		$obj->Add();

		SetRedirectMessage('Success', 'New product added successfully.');
		RedirectToAction('Product', 'Index');
	}

	public function Edit()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();

		$model = new ProductModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new ProductPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_MODIFY, (int)$_POST['id']))
			throw new PermissionDeniedException();

		$model = new ProductModel();
		$model->InitFrom_POST();
		$model->Edit();

		SetRedirectMessage('Success', 'Product updated successfully.');
		RedirectToAction('Product', 'Index');
	}

	public function Delete()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();

		$model = new ProductModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new ProductPresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		global $g_oSec;
		
		if (($id = @Filter::ToInt($_POST['id'])) === null)
			throw new InvalidDataException();
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_DELETE, $id))
			throw new PermissionDeniedException();

		$model = new ProductModel();
		if (!$model->HasFKRef($id))
		{
			$model->Delete($id);
			SetRedirectMessage('Success', 'Product deleted successfully.');
		}
		else
		{
			$model->SetActive(array('id' => $id), false);
			SetRedirectMessage('Success', 'Product was deactivated because other items reference it.');
		}

		RedirectToAction('Product', 'Index');
	}
	
	public function Detail()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();

		$model = new ProductModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new ProductPresenter();
		$presenter->Detail($model);
		$presenter->DetailWorkOrderMetrics($model);
	}

	function DetailWorkOrder()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();

		$model = new ProductModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new ProductPresenter();
		$presenter->Detail($model);
		$presenter->DetailWorkOrder($model);
	}

	function DetailTicket()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();

		$model = new ProductModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new ProductPresenter();
		$presenter->Detail($model);
		$presenter->DetailTicket($model);
	}

	function DetailModule()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();

		$model = new ProductModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new ProductPresenter();
		$presenter->Detail($model);
		$presenter->DetailModule($model);
	}

	function DetailRelease()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();

		$model = new ProductModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new ProductPresenter();
		$presenter->Detail($model);
		$presenter->DetailRelease($model);
	}

	function DetailBuild()
	{
		if (($productId = @Filter::ToInt($_REQUEST['product_id'])) === null)
			throw new InvalidDataException();

		if (($versionId = @Filter::ToInt($_REQUEST['product_version_id'])) === null)
			throw new InvalidDataException();

		$model = new ProductModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$buildModel = new ProductBuildModel();
		if ($buildModel->Load($versionId) == -1)
			throw new InvalidEntityException();

		$presenter = new ProductPresenter();
		$presenter->Detail($model);
		$presenter->DetailBuild($model, $buildModel);
	}
	
	public function IsProjectRequired()
	{
		header('Content-Type: application/json');
		$product_id = @Filter::ToInt($_REQUEST['product_id']);
		if ($product_id === null)
			exit;
		
		$model = new ProductModel();
		$model->Load($product_id);

		$bFirst = true;
		echo '{';
		echo '"totalRecords":1,';
		echo '"data":[{"is_project_required":"' . $model->is_project_required . '"}]';
		echo '}';

		exit;
	}
	
	public function ListVersions()
	{
		header('Content-Type: application/json');
		$product_id = @Filter::ToInt($_REQUEST['product_id']);
		if ($product_id === null)
			exit;
		
		$oDB = new ProductVersionModel();
		$aOptions = $oDB->GetOptions('product_version_id', 'product_version_text', 'active', (isset($_REQUEST['active']) && $_REQUEST['active'] == 'Y'), '', "product_id=$product_id");

		$bFirst = true;
		echo '{';
		echo '"totalRecords":', count($aOptions), ',';
		echo '"data":[';
		for ($i = 0; $i < count($aOptions); $i++)
		{
			if ($i > 0)
				echo ',';
				
			
			echo '{';
			echo '"id":', $aOptions[$i]['product_version_id'], ',';
			echo '"text":"', str_replace('"', '\"', str_replace("\\", "\\\\", $aOptions[$i]['product_version_text'])), '"';
			echo '}';
		}
		
		echo ']}';

		exit;
	}
}
