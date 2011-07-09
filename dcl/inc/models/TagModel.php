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

LoadStringResource('db');
class TagModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_tag';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	public function getIdByName($sTag)
	{
		$sTag = trim(strtolower($sTag));
		$iID = $this->ExecuteScalar('SELECT tag_id FROM dcl_tag WHERE tag_desc = ' . $this->Quote($sTag));
		if ($iID !== null)
			return $iID;
			
		// Not found, so add it and return the new value
		$this->tag_desc = $sTag;
		if ($this->Add() != -1)
		{
			return $this->GetLastInsertID($this->TableName);
		}
		
		return null;
	}

	public function filterList($filter)
	{
		$this->LimitQuery("SELECT tag_id, tag_desc FROM dcl_tag WHERE tag_desc LIKE " . $this->Quote('%' . $filter . '%') . " ORDER BY tag_desc", 0, 20);

		return $this->FetchAllRows();
	}

	public function getExistingIdsByName($sTags)
	{
		if ($sTags === null || trim($sTags) == '')
			return '-1';
			
		$sTags = trim(strtolower($sTags));
		$aTags = split(',', $sTags);
		if (count($aTags) < 1)
			return '-1';
			
		$sTagValues = '';
		foreach($aTags as $sTag)
		{
			$sTag = trim($sTag);
			if ($sTag == '')
				continue;
				
			if ($sTagValues != '')
				$sTagValues .= ',';
				
			$sTagValues .= $this->Quote($sTag);
		}
		
		if ($this->Query("SELECT tag_id FROM dcl_tag WHERE tag_desc IN ($sTagValues)") == -1)
			return '-1';

		$sID = '';
		while ($this->next_record())
		{
			if ($sID != '')
				$sID .= ',';
				
			$sID .= $this->f(0);
		}
		
		if ($sID == '')
			$sID = '-1';
			
		return $sID;
	}
	
	public function listByPopular()
	{
		$sSQL = 'SELECT dcl_tag.tag_desc, COUNT(*) FROM dcl_tag JOIN dcl_entity_tag ON dcl_tag.tag_id = dcl_entity_tag.tag_id GROUP BY dcl_tag.tag_desc ORDER BY 2 DESC';
		return $this->LimitQuery($sSQL, 0, 50);
	}
}
