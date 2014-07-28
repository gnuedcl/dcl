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

		$limit = @Filter::ToInt($_REQUEST['rows']);
		if ($limit === null)
			$limit = 1;

		if ($_REQUEST['dir'] == 'next')
			$dir = 'next';
		else
			$dir = 'previous';

		$lastId = @Filter::ToInt($_REQUEST['lastid']);
		$firstId = @Filter::ToInt($_REQUEST['firstid']);

		$model = new ErrorLogModel();
		$retVal = new stdClass();

		$sql = 'SELECT error_log_id, error_timestamp, log_level, personnel.short, server_name, request_uri, error_file, error_line, error_description FROM dcl_error_log LEFT JOIN personnel ON user_id = id';
		if ($lastId > 0 || $firstId > 0)
		{
			if ($dir == 'next')
			{
				$sql .= ' WHERE error_log_id < ' . $lastId;
				$sql .= ' ORDER BY error_log_id DESC';
			}
			else
			{
				$sql .= ' WHERE error_log_id > ' . $firstId;
				$sql .= ' ORDER BY error_log_id';
			}
		}
		else
		{
			$sql .= ' ORDER BY error_log_id DESC';
		}

		$countSql = 'SELECT MIN(error_log_id), MAX(error_log_id), COUNT(*) FROM dcl_error_log';
		if ($model->Query($countSql) != -1)
		{
			if ($model->next_record())
			{
				$retVal->min = $model->f(0);
				$retVal->max = $model->f(1);
				$retVal->records = $model->f(2);
			}
		}

		$retVal->page = 1;
		$retVal->total = ceil($retVal->records / $limit);

		$model->LimitQuery($sql, 0, $limit);

		if ($dir == 'next')
			$allRecs = $model->FetchAllRows();
		else
			$allRecs = array_reverse($model->FetchAllRows());

		$retVal->rows = array();
		if ($retVal->records > 0)
		{
			foreach ($allRecs as $record)
			{
				$retVal->rows[] = (object)array(
					'id' => (int)$record[0],
					'ts' => $model->FormatTimeStampForDisplay($record[1]),
					'lvl' => (int)$record[2],
					'user' => $record[3],
					'srv' => $record[4],
					'uri' => $record[5],
					'file' => $record[6],
					'line' => (int)$record[7],
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
