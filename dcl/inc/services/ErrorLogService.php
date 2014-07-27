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

class ErrorLogService
{
	public function GetData()
	{
		RequirePermission(DCL_ENTITY_ERRORLOG, DCL_PERM_VIEW);

		$page = @Filter::ToInt($_REQUEST['page']);
		$limit = @Filter::ToInt($_REQUEST['rows']);
		$sidx = @Filter::ToSqlName($_REQUEST['sidx']);
		$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

		if ($sord != 'asc' && $sord != 'desc')
			$sord = 'desc';

		if ($sidx === null)
			$sidx = 'error_log_id';

		if ($page === null)
			$page = 1;

		if ($limit === null)
			$limit = 1;

		$validColumns = array('error_log_id');
		if (!in_array($sidx, $validColumns))
			$sidx = 'error_log_id';

		$idFilter = @Filter::ToInt($_REQUEST['id']);

		$model = new ErrorLogModel();
		$queryHelper = new ErrorLogSqlQueryHelper();
		$queryHelper->AddDef('columns', '', array('error_log_id', 'error_timestamp', 'log_level', 'personnel.short', 'server_name', 'request_uri', 'error_file', 'error_line', 'error_description'));

		if ($idFilter !== null)
			$queryHelper->AddDef('filter', 'error_log_id', $idFilter);

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
				$retVal->rows[] = (object)array(
					'id' => $record[0],
					'ts' => $model->FormatTimeStampForDisplay($record[1]),
					'lvl' => $record[2],
					'user' => $record[3],
					'srv' => $record[4],
					'uri' => $record[5],
					'file' => $record[6],
					'line' => $record[7],
					'desc' => $record[8]
				);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}

	public function Item()
	{
		RequirePermission(DCL_ENTITY_ERRORLOG, DCL_PERM_VIEW);
		$id = Filter::RequireInt(@$_REQUEST['id']);

		$model = new ErrorLogModel();
		$model->Load($id);

		$retVal = new stdClass();
		$retVal->error_log_id = $model->error_log_id;
		$retVal->error_timestamp = $model->error_timestamp;
		$retVal->user_id = $model->user_id;
		$retVal->server_name = $model->server_name;
		$retVal->script_name = $model->script_name;
		$retVal->request_uri = $model->request_uri;
		$retVal->query_string = $model->query_string;
		$retVal->error_file = $model->error_file;
		$retVal->error_line = $model->error_line;
		$retVal->error_description = $model->error_description;
		$retVal->stack_trace = json_decode($model->stack_trace);
		$retVal->log_level = $model->log_level;

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}
}
