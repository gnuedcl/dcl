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

class OrganizationService
{
	public function GetData()
	{
		global $g_oSec, $g_oSession;

		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW);

		$page = @Filter::ToInt($_REQUEST['page']);
		$limit = @Filter::ToSignedInt($_REQUEST['rows']);
		$sidx = @Filter::ToSqlName($_REQUEST['sidx']);
		$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

		if ($sord != 'asc' && $sord != 'desc')
			$sord = 'asc';

		if ($sidx === null)
			$sidx = 'name';
		else if ($sidx === 'phone')
			$sidx = 'dcl_org_phone.phone_number';
		else if ($sidx === 'email')
			$sidx = 'dcl_org_email.email_addr';
		else if ($sidx === 'url')
			$sidx = 'dcl_org_url.url_addr';

		$validColumns = array('id', 'name', 'dcl_org_phone.phone_number', 'dcl_org_email.email_addr', 'dcl_org_url.url_addr');
		if (!in_array($sidx, $validColumns))
			$sidx = 'name';

		if ($page === null)
			$page = 1;

		if ($limit === null)
			$limit = 1;

		$idFilter = @Filter::ToInt($_REQUEST['id']);
		$nameFilter = @$_REQUEST['name'];
		$phoneFilter = @$_REQUEST['phone'];
		$emailFilter = @$_REQUEST['email'];
		$urlFilter = @$_REQUEST['url'];

		$model = new OrganizationModel();
		$queryHelper = new OrganizationSqlQueryHelper();
		$queryHelper->AddDef('columns', '', array('org_id', 'name', 'dcl_org_phone.phone_number', 'dcl_org_email.email_addr', 'dcl_org_url.url_addr'));

		if ($idFilter !== null)
			$queryHelper->AddDef('filter', 'org_id', $idFilter);

		if (isset($nameFilter))
			$queryHelper->AddDef('filterlike', 'name', $nameFilter);

		if (isset($phoneFilter))
			$queryHelper->AddDef('filterlike', 'dcl_org_phone.phone_number', $phoneFilter);

		if (isset($emailFilter))
			$queryHelper->AddDef('filterlike', 'dcl_org_email.email_addr', $emailFilter);

		if (isset($urlFilter))
			$queryHelper->AddDef('filterlike', 'dcl_org_url.url_addr', $urlFilter);

		if ($g_oSec->IsOrgUser())
			$queryHelper->AddDef('filter', 'org_id', explode(',', $g_oSession->Value('member_of_orgs')));

		$queryHelper->AddDef('order', '', array($sidx . ' ' . $sord));

		$retVal = new stdClass();
		$retVal->records = (int)$model->ExecuteScalar($queryHelper->GetSQL(true));
		$retVal->page = $page;
		$retVal->total = (int)($limit > 0 ? ceil($retVal->records / $limit) : $retVal->records);

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
				$retVal->rows[] = (object)array(
					'id' => (int)$record[0],
					'name' => $record[1],
					'phone' => $record[2],
					'email' => $record[3],
					'url' => $record[4]
				);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}
}