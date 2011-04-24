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

class ContactAddressPresenter
{
	public function Create($contactId)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$oSmarty = new DCL_Smarty();
		$oAddrType = new AddressTypeHtmlHelper();

		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlContactDetail.show&contact_id=' . $contactId));

		$oContact = new ContactModel();
		$oContact->Load($contactId);
		$oSmarty->assign('VAL_FIRSTNAME', $oContact->first_name);
		$oSmarty->assign('VAL_LASTNAME', $oContact->last_name);
		$oSmarty->assign('VAL_CONTACTID', $oContact->contact_id);

		$oSmarty->assign('TXT_FUNCTION', 'Add New Contact Address');
		$oSmarty->assign('VAL_MENUACTION', 'ContactAddress.Insert');
		$oSmarty->assign('CMB_ADDRTYPE', $oAddrType->Select());

		$oSmarty->Render('htmlAddrForm.tpl');
	}

	public function Edit(ContactAddressModel $model)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
		    
		$oSmarty = new DCL_Smarty();
		$oAddrType = new AddressTypeHtmlHelper();

		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlContactDetail.show&contact_id=' . $model->contact_id));

		$contactModel = new ContactModel();
		$contactModel->Load($model->contact_id);
		$oSmarty->assign('VAL_FIRSTNAME', $contactModel->first_name);
		$oSmarty->assign('VAL_LASTNAME', $contactModel->last_name);
		$oSmarty->assign('VAL_CONTACTID', $contactModel->contact_id);

		$oSmarty->assign('VAL_MENUACTION', 'ContactAddress.Update');
		$oSmarty->assign('VAL_CONTACTADDRID', $model->contact_addr_id);
		$oSmarty->assign('VAL_ADD1', $model->add1);
		$oSmarty->assign('VAL_ADD2', $model->add2);
		$oSmarty->assign('VAL_CITY', $model->city);
		$oSmarty->assign('VAL_STATE', $model->state);
		$oSmarty->assign('VAL_ZIP', $model->zip);
		$oSmarty->assign('VAL_COUNTRY', $model->country);
		$oSmarty->assign('VAL_PREFERRED', $model->preferred);
		$oSmarty->assign('CMB_ADDRTYPE', $oAddrType->Select($model->addr_type_id));
		$oSmarty->assign('TXT_FUNCTION', 'Edit Contact Address');

		$oSmarty->Render('htmlAddrForm.tpl');
	}

	public function Delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
	}
}
