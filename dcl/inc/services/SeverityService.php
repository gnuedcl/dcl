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

class SeverityService
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

   		if ($page === null)
   			$page = 1;

   		if ($limit === null)
   			$limit = 1;

        $validColumns = array('id', 'short', 'active', 'name', 'weight');
        if (!in_array($sidx, $validColumns))
            $sidx = 'name';

        $idFilter = @Filter::ToInt($_REQUEST['id']);
        $shortFilter = @$_REQUEST['short'];
        $activeFilter = isset($_REQUEST['active']) ? @Filter::ToYN($_REQUEST['active']) : null;
        $nameFilter = @$_REQUEST['name'];
        $weightFilter = @Filter::ToInt($_REQUEST['weight']);

        $model = new SeverityModel();
        $queryHelper = new SeveritySqlQueryHelper();
        $queryHelper->AddDef('columns', '', array('id', 'active', 'short', 'name', 'weight'));

        if ($idFilter !== null)
            $queryHelper->AddDef('filter', 'id', $idFilter);

        if (isset($shortFilter))
            $queryHelper->AddDef('filterlike', 'short', $shortFilter);

        if (isset($activeFilter))
            $queryHelper->AddDef('filter', 'active', $model->Quote($activeFilter));

        if (isset($nameFilter))
            $queryHelper->AddDef('filterlike', 'name', $nameFilter);

        if ($weightFilter !== null)
            $queryHelper->AddDef('filter', 'weight', $weightFilter);

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
