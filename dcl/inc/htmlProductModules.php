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

LoadStringResource('prod');

class htmlProductModules
{
	var $public;

	function htmlProductModules()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete', 'PrintAll');
	}

	function GetCombo($default = 0, $cbName = 'module', $size = 0, $activeOnly = true, $product_id = 0)
	{
		$filter = '';
		$table = 'dcl_product_module';

		if ($activeOnly)
			$filter = "active='Y'";

		if ($product_id > 0)
		{
			if ($filter != '')
				$filter .= ' And ';

			$filter .= 'product_id = ' . $product_id;
		}

		$order = 'module_name';

		$obj = new SelectHtmlHelper();
		$obj->SetOptionsFromDb($table, 'product_module_id', 'module_name', $filter, $order);
		$obj->DefaultValue = $default;
		$obj->Id = $cbName;
		$obj->Size = $size;
		$obj->FirstOption = STR_CMMN_SELECTONE;

		return $obj->GetHTML();
	}

	function PrintAll($orderBy = 'module_name')
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		if (($id = Filter::ToInt($_REQUEST['product_id'])) === null)
		{
			throw new InvalidDataException();
		}

		$oProduct = new ProductModel();
		if ($oProduct->Load($id) == -1)
			return;

		$o = new boView();
		$o->table = 'dcl_product_module';
		$o->title = 'Modules for Product ' . $oProduct->name;
		$o->AddDef('columns', '', array('product_module_id', 'active', 'module_name'));
		$o->AddDef('columnhdrs', '', array('ID', STR_CMMN_ACTIVE, 'Name'));
		$o->AddDef('order', '', $orderBy);
		$o->AddDef('filter', 'product_id', $id);

		$oDB = new ProductModulesModel();
		if ($oDB->query($o->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption('Modules for Product ' . $oProduct->name);
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CMMN_ACTIVE, 'string');
		$oTable->addColumn(STR_CMMN_NAME, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=htmlProductModules.add&product_id=' . $oProduct->id), STR_CMMN_NEW);
			
		if ($g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=Product.Detail&id=' . $oProduct->id), 'Detail');
			
		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_CONTACTTYPE => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, DCL_PERM_MODIFY))
					$options = '<a href="' . menuLink('', 'menuAction=htmlProductModules.modify&product_module_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=htmlProductModules.delete&product_module_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
				}

				$allRecs[$i][] = $options;
			}
		}
		
		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$this->ShowEntryForm();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		if (($id = Filter::ToInt($_REQUEST['product_module_id'])) === null)
		{
			throw new InvalidDataException();
		}

		$obj = new ProductModulesModel();
		if ($obj->Load($id) == -1)
			return;
			
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, DCL_PERM_DELETE))
			throw new PermissionDeniedException();
			
		if (($id = Filter::ToInt($_REQUEST['product_module_id'])) === null)
		{
			throw new InvalidDataException();
		}

		$obj = new ProductModulesModel();
		if ($obj->Load($id) == -1)
			return;
			
		ShowDeleteYesNo('Product Module', 'htmlProductModules.submitDelete', $obj->product_module_id, $obj->module_name);
	}

	function submitAdd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new boProductModules();
		CleanArray($_REQUEST);
		$obj->add($_REQUEST);
		$this->PrintAll();
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$obj = new boProductModules();
		CleanArray($_REQUEST);
		$obj->modify($_REQUEST);

		$this->PrintAll();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new boProductModules();
		CleanArray($_REQUEST);
		if ($obj->oDB->Load($id) == -1)
			return;
			
		$_REQUEST['product_id'] = $obj->oDB->product_id;
		$obj->delete(array('product_module_id' => $id));

		$this->PrintAll();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCTMODULE, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			throw new PermissionDeniedException();
		
		if ($isEdit)
		{
			if (($product_module_id = Filter::ToInt($_REQUEST['product_module_id'])) === null)
			{
				throw new InvalidDataException();
			}
		}
		else
		{
			if (($product_id = Filter::ToInt($_REQUEST['product_id'])) === null)
			{
				throw new InvalidDataException();
			}
		}

		$oProduct = new ProductModel();
		$iProductID = $isEdit ? $obj->product_id : $product_id;
		if ($oProduct->Load($iProductID) == -1)
		{
			printf('Could not load product by id %d', $product_id);
			return;
		}

		$t = new SmartyHelper();
		$t->assign('product_id', $iProductID);
		
		if ($isEdit)
		{
			$t->assign('TXT_FUNCTION', 'Edit Module for Product ' . $oProduct->name);
			$t->assign('menuAction', 'htmlProductModules.submitModify');
			$t->assign('product_module_id', $product_module_id);
			$t->assign('CMB_ACTIVE', GetYesNoCombo($obj->active, 'active', 0, false));
			$t->assign('VAL_NAME', $obj->module_name);
		}
		else
		{
			$t->assign('TXT_FUNCTION', 'Add Module for Product ' . $oProduct->name);
			$t->assign('menuAction', 'htmlProductModules.submitAdd');
			$t->assign('CMB_ACTIVE', GetYesNoCombo('Y', 'active', 0, false));
		}

		$t->Render('htmlProductModulesForm.tpl');
	}
}
