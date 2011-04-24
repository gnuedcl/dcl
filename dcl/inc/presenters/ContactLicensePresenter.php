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

class ContactLicensePresenter
{
	public function Create($contactId)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$oSmarty = new DCL_Smarty();

		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlContactDetail.show&contact_id=' . $contactId));

		$oContact = new ContactModel();
		if ($oContact->Load($contactId) == -1)
		    return;

		$oSmarty->assign('VAL_FIRSTNAME', $oContact->first_name);
		$oSmarty->assign('VAL_LASTNAME', $oContact->last_name);
		$oSmarty->assign('VAL_CONTACTID', $oContact->contact_id);

		$oSmarty->assign('TXT_FUNCTION', 'Add New Product License');
		$oSmarty->assign('VAL_MENUACTION', 'ContactLicense.Insert');

		$oSmarty->Render('htmlLicenseForm.tpl');
	}

	public function Edit(ContactLicenseModel $model)
	{
		global $g_oSec;
		
		commonHeader();
		    
		$oSmarty = new DCL_Smarty();

		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlContactDetail.show&contact_id=' . $model->contact_id));

		$oContact = new ContactModel();
		if ($oContact->Load($model->contact_id) == -1)
		    return;

		$oSmarty->assign('VAL_FIRSTNAME', $oContact->first_name);
		$oSmarty->assign('VAL_LASTNAME', $oContact->last_name);
		$oSmarty->assign('VAL_CONTACTID', $oContact->contact_id);

		$oSmarty->assign('VAL_MENUACTION', 'ContactLicense.Update');
		$oSmarty->assign('VAL_CONTACTLICENSEID', $model->contact_license_id);
		$oSmarty->assign('VAL_PRODUCTID', $model->product_id);
		$oSmarty->assign('VAL_VERSION', $model->product_version);
		$oSmarty->assign('VAL_LICENSEID', $model->license_id);
		$oSmarty->assign('VAL_REGISTEREDON', $model->registered_on);
		$oSmarty->assign('VAL_NOTES', $model->license_notes);
		$oSmarty->assign('VAL_EXPIRESON', $model->expires_on);
		$oSmarty->assign('TXT_FUNCTION', 'Edit Product License');

		$oSmarty->Render('htmlLicenseForm.tpl');
	}
}
