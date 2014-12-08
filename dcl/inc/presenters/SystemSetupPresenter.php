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

LoadStringResource('admin');
class SystemSetupPresenter
{
	public function Index()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$oSmarty = new SmartyHelper();
		$oSmarty->assign('TXT_SETUPDESC', STR_ADMIN_SETUPDESC);
		$oSmarty->assign('TXT_SETUPTITLE', STR_ADMIN_SETUPTITLE);
		$oSmarty->assign('VAL_OPTIONS', $this->GetOptions());

		$oSmarty->Render('AdminMain.tpl');
	}

	private function GetOptions()
	{
		return array(
			'Role.Index' => array(
				'action' => STR_ADMIN_SECURITY,
				'description' => STR_ADMIN_SECURITYDESC,
				'note' => STR_ADMIN_SECURITYNOTE
			),
			'Priority.Index' => array(
				'action' => STR_ADMIN_PRIORITIES,
				'description' =>  STR_ADMIN_PRIORITIESDESC,
				'note' =>  STR_ADMIN_PRIORITIESNOTE
			),
			'Severity.Index' => array(
				'action' =>  STR_ADMIN_SEVERITIES,
				'description' =>  STR_ADMIN_SEVERITIESDESC,
				'note' =>  STR_ADMIN_SEVERITIESNOTE
			),
			'Status.Index' => array(
				'action' =>  STR_ADMIN_STATUSES,
				'description' =>  STR_ADMIN_STATUSESDESC,
				'note' =>  STR_ADMIN_STATUSESNOTE
			),
			'Configuration.Edit' => array(
				'action' =>  STR_ADMIN_CONFIG,
				'description' =>  STR_ADMIN_CONFIGDESC,
				'note' =>  STR_ADMIN_CONFIGNOTE
			),
			'Department.Index' => array(
				'action' =>  STR_ADMIN_DEPARTMENTS,
				'description' =>  STR_ADMIN_DEPARTMENTSDESC,
				'note' =>  STR_ADMIN_DEPARTMENTSNOTE
			),
			'OrganizationType.Index' => array(
				'action' =>  STR_ADMIN_ORGTYPES,
				'description' =>  STR_ADMIN_ORGTYPESDESC,
				'note' =>  STR_ADMIN_ORGTYPESNOTE
			),
			'ContactType.Index' => array(
				'action' =>  STR_ADMIN_CONTACTTYPES,
				'description' =>  STR_ADMIN_CONTACTTYPESDESC,
				'note' =>  STR_ADMIN_CONTACTTYPESNOTE
			),
			'Personnel.Index&filterActive=Y' => array(
				'action' =>  STR_ADMIN_USERS,
				'description' =>  STR_ADMIN_USERSDESC,
				'note' =>  STR_ADMIN_USERSNOTE
			),
			'Action.Index' => array(
				'action' =>  STR_ADMIN_ACTIONS,
				'description' =>  STR_ADMIN_ACTIONSDESC,
				'note' =>  STR_ADMIN_ACTIONSNOTE
			),
			'AttributeSet.Index' => array(
				'action' =>  STR_ADMIN_ATTRIBUTESETS,
				'description' =>  STR_ADMIN_ATTRIBUTESETSDESC,
				'note' =>  STR_ADMIN_ATTRIBUTESETSNOTE
			),
			'Product.Index' => array(
				'action' =>  STR_ADMIN_PRODUCTS,
				'description' =>  STR_ADMIN_PRODUCTSDESC,
				'note' =>  STR_ADMIN_PRODUCTSNOTE
			),
			'WorkOrderType.Index' => array(
				'action' =>  STR_ADMIN_WORKORDERTYPES,
				'description' =>  STR_ADMIN_WORKORDERTYPESDESC,
				'note' =>  STR_ADMIN_WORKORDERTYPESNOTE
			),
			'EntitySource.Index' => array(
				'action' =>  STR_ADMIN_ENTITYSOURCES,
				'description' =>  STR_ADMIN_ENTITYSOURCESDESC,
				'note' =>  STR_ADMIN_ENTITYSOURCESNOTE
			),
			'AddressType.Index' => array(
				'action' =>  STR_ADMIN_ADDRESSTYPES,
				'description' =>  STR_ADMIN_ADDRESSTYPESDESC,
				'note' =>  STR_ADMIN_ADDRESSTYPESNOTE
			),
			'EmailType.Index' => array(
				'action' =>  STR_ADMIN_EMAILTYPES,
				'description' =>  STR_ADMIN_EMAILTYPESDESC,
				'note' =>  STR_ADMIN_EMAILTYPESNOTE
			),
			'PhoneType.Index' => array(
				'action' =>  STR_ADMIN_PHONETYPES,
				'description' =>  STR_ADMIN_PHONETYPESDESC,
				'note' =>  STR_ADMIN_PHONETYPESNOTE
			),
			'NoteType.Index' => array(
				'action' =>  STR_ADMIN_NOTETYPES,
				'description' =>  STR_ADMIN_NOTETYPESDESC,
				'note' =>  STR_ADMIN_NOTETYPESNOTE
			),
			'UrlType.Index' => array(
				'action' =>  STR_ADMIN_URLTYPES,
				'description' =>  STR_ADMIN_URLTYPESDESC,
				'note' =>  STR_ADMIN_URLTYPESNOTE
			),
			'Environment.Index' => array(
				'action' => 'Environments',
				'description' => 'Add environments for tracking deployments and outages.'
			),
			'OutageType.Index' => array(
				'action' => 'Outage Types',
				'description' => 'Manage types of outages that can occur in your organization.'
			),
			'MeasurementUnit.Index' => array(
				'action' => 'Measurement Units',
				'description' => 'Maintain a list of units that are used for measurements.',
				'note' => 'Used by measurement types.'
			),
			'MeasurementType.Index' => array(
				'action' => 'Measurement Types',
				'description' => 'Manage types of measurements that can be recorded.',
				'note' => 'This entity relies on measurement units.'
			)
		);
	}
}