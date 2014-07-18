<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

class PersonnelService
{
	public function GetData()
	{
		$page = @Filter::ToInt($_REQUEST['page']);
		$limit = @Filter::ToInt($_REQUEST['rows']);
		$sidx = @Filter::ToSqlName($_REQUEST['sidx']);
		$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

		if ($sord != 'asc' && $sord != 'desc')
			$sord = 'asc';

		if ($sidx === null)
			$sidx = 'short';

		$validColumns = array('id', 'active', 'short', 'last_name', 'first_name', 'dept', 'phone', 'email', 'url');
		if (!in_array($sidx, $validColumns))
			$sidx = 'short';

		$filterToSqlColumn = array(
			'id' => 'id',
			'active' => 'active',
			'short' => 'short',
			'last_name' => 'dcl_contact.last_name',
			'first_name' => 'dcl_contact.first_name',
			'dept' => 'departments.name',
			'phone' => 'dcl_contact_phone.phone_number',
			'email' => 'dcl_contact_email.email_addr',
			'url' => 'dcl_contact_url.url_addr'
		);

		$sidx = $filterToSqlColumn[$sidx];

		if ($page === null)
			$page = 1;

		if ($limit === null)
			$limit = 1;

		$idFilter = @Filter::ToInt($_REQUEST['id']);
		$shortFilter = @$_REQUEST['short'];
		$activeFilter = isset($_REQUEST['active']) ? @Filter::ToYN($_REQUEST['active']) : null;
		$lastNameFilter = @$_REQUEST['last_name'];
		$firstNameFilter = @$_REQUEST['first_name'];
		$departmentFilter = @Filter::ToInt($_REQUEST['dept']);
		$phoneFilter = @$_REQUEST['phone'];
		$emailFilter = @$_REQUEST['email'];
		$urlFilter = @$_REQUEST['url'];

		$model = new PersonnelModel();
		$queryHelper = new PersonnelSqlQueryHelper();
		$aColumns = array('id', 'active', 'short', 'dcl_contact.last_name', 'dcl_contact.first_name', 'departments.name', 'dcl_contact_phone.phone_number', 'dcl_contact_email.email_addr', 'dcl_contact_url.url_addr');
		$queryHelper->AddDef('columns', '', $aColumns);

		if ($idFilter !== null)
			$queryHelper->AddDef('filter', 'id', $idFilter);

		if (isset($shortFilter))
			$queryHelper->AddDef('filterlike', 'short', $shortFilter);

		if (isset($activeFilter))
			$queryHelper->AddDef('filter', 'active', $model->Quote($activeFilter));

		if ($lastNameFilter != '')
			$queryHelper->AddDef('filterlike', 'dcl_contact.last_name', $lastNameFilter);

		if ($firstNameFilter != '')
			$queryHelper->AddDef('filterlike', 'dcl_contact.first_name', $firstNameFilter);

		if ($departmentFilter !== null)
			$queryHelper->AddDef('filter', 'department', $departmentFilter);

		if ($phoneFilter != '')
			$queryHelper->AddDef('filterlike', 'dcl_contact_phone.phone_number', $phoneFilter);

		if ($emailFilter != '')
			$queryHelper->AddDef('filterlike', 'dcl_contact_email.email_addr', $emailFilter);

		if ($urlFilter != '')
			$queryHelper->AddDef('filterlike', 'dcl_contact_url.url_addr', $urlFilter);

		$queryHelper->AddDef('order', '', array($sidx . ' ' . $sord));

		$retVal = new stdClass();
		$retVal->records = $model->ExecuteScalar($queryHelper->GetSQL(true));
		$retVal->page = $page;
		$retVal->total = ceil($retVal->records / $limit);

		$query = $queryHelper->GetSQL();
		if ($limit > 0)
			$model->LimitQuery($query, ($page - 1) * $limit, $limit);
		else
			$model->Query($query);

		$allRecs = $model->FetchAllRows();

		$retVal->rows = array();
		if ($retVal->records > 0)
		{
			foreach ($allRecs as $record)
			{
				$retVal->rows[] = array('id' => $record[0], 'cell' => $record);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}
}