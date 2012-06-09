<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2012 Free Software Foundation
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

class StatusService
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
            $sidx = 'name';
        else if ($sidx === 'type')
            $sidx = 'dcl_status_type_name';

		if ($page === null)
			$page = 1;
		
		if ($limit === null)
			$limit = 1;

        $idFilter = @Filter::ToInt($_REQUEST['id']);
        $shortFilter = @$_REQUEST['short'];
        $activeFilter = isset($_REQUEST['active']) ? @Filter::ToYN($_REQUEST['active']) : null;
        $nameFilter = @$_REQUEST['name'];
        $typeFilter = @Filter::ToInt($_REQUEST['type']);

        $statusModel = new StatusModel();
        $statusQueryHelper = new StatusSqlQueryHelper();
        $statusQueryHelper->AddDef('columns', '', array('id', 'active', 'short', 'name', 'dcl_status_type.dcl_status_type_name'));

        if ($idFilter !== null)
            $statusQueryHelper->AddDef('filter', 'id', $idFilter);

        if (isset($shortFilter))
            $statusQueryHelper->AddDef('filterlike', 'short', $shortFilter);

        if (isset($activeFilter))
            $statusQueryHelper->AddDef('filter', 'active', $statusModel->Quote($activeFilter));

        if (isset($nameFilter))
            $statusQueryHelper->AddDef('filterlike', 'name', $nameFilter);

        if ($typeFilter !== null)
            $statusQueryHelper->AddDef('filter', 'dcl_status_type', $typeFilter);

        $statusQueryHelper->AddDef('order', '', array($sidx . ' ' . $sord));

		$retVal = new stdClass();
		$retVal->records = $statusModel->ExecuteScalar($statusQueryHelper->GetSQL(true));
        $retVal->page = $page;
		$retVal->total = ceil($retVal->records / $limit);

        $query = $statusQueryHelper->GetSQL();
		if ($limit > 0)
			$statusModel->LimitQuery($query, ($page - 1) * $limit, $limit);
		else
			$statusModel->Query($query);

		$allRecs = $statusModel->FetchAllRows();
		
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
