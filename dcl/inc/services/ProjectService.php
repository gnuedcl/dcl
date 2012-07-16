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

class ProjectService
{
	public function Autocomplete()
	{
		$response = array();

		$idFilter = '';
		$term = isset($_REQUEST['term']) ? $_REQUEST['term'] : '';
		if ($term != '')
		{
			$idFilter = Filter::ToInt($term);
			if ($idFilter === null)
				$idFilter = '';
		}

		$db = new DbProvider();
		$sql = 'SELECT projectid, name FROM dcl_projects';
		if ($term != '')
			$sql .= ' WHERE name ' . $db->LikeKeyword . " '%" . $term . "%'";

		if ($idFilter != '')
		{
			if ($term != '')
				$sql .= ' OR ';
			else
				$sql .= ' WHERE ';

			$sql .= 'projectid = ' . $idFilter;
		}

		$sql .= ' ORDER BY name';

		if ($db->Query($sql) !== -1)
		{
			while ($db->next_record())
			{
				$row = new stdClass();
				$row->id = $db->f(0);
				$row->label = '[' . $db->f(0) . '] ' . $db->f(1);
				$row->value = $row->label;

				$response[] = $row;
			}
		}

		header('Content-Type: application/json');
		echo json_encode($response);
		exit;
	}
}
