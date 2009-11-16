<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
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
class dbHotlist extends dclDB
{
	function dbHotlist()
	{
		parent::dclDB();
		$this->TableName = 'dcl_hotlist';
		LoadSchema($this->TableName);

		parent::Clear();
	}
	
	function ListActive()
	{
		return $this->Query("SELECT hotlist_id, hotlist_tag FROM dcl_hotlist WHERE active = 'Y' ORDER BY hotlist_tag");
	}
	
	function getIdByName($sTag)
	{
		$sTag = trim(strtolower($sTag));
		$iID = $this->ExecuteScalar('SELECT hotlist_id FROM dcl_hotlist WHERE hotlist_tag = ' . $this->Quote($sTag));
		if ($iID !== null)
			return $iID;
			
		// Not found, so add it and return the new value
		$this->hotlist_tag = $sTag;
		$this->hotlist_desc = $sTag;
		$this->created_by = $GLOBALS['DCLID'];
		$this->created_on = DCL_NOW;
		$this->active = 'Y';
		
		if ($this->Add() != -1)
		{
			return $this->GetLastInsertID($this->TableName);
		}
		
		return null;
	}

	function getExistingIdsByName($sTags)
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
		
		if ($this->Query("SELECT hotlist_id FROM dcl_hotlist WHERE hotlist_tag IN ($sTagValues)") == -1)
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
	
	function listByPopular()
	{
		$sSQL = 'SELECT dcl_hotlist.hotlist_tag, COUNT(*) FROM dcl_hotlist JOIN dcl_entity_hotlist ON dcl_hotlist.hotlist_id = dcl_entity_hotlist.hotlist_id WHERE dcl_entity_hotlist.deleted_on IS NULL GROUP BY dcl_hotlist.hotlist_tag ORDER BY 2 DESC';
		return $this->LimitQuery($sSQL, 0, 50);
	}
}
?>