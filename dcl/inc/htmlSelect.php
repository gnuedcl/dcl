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

class htmlSelect
{
	var $vDefault;
	var $sName;
	var $iSize;
	var $sOnChange;
	var $sZeroOption;
	var $aOptions;
	var $oDB;
	var $bCastToInt;
	var $bHidden;

	function htmlSelect()
	{
		$this->vDefault = 0;
		$this->sName = '';
		$this->iSize = 0;
		$this->sOnChange = '';
		$this->sZeroOption = '';
		$this->aOptions = array();
		$this->bCastToInt = false;
		$this->bHidden = false;
		$this->oDB = NULL;
	}

	function GetOption($sValue, $sDisplay)
	{
		$sValue = trim($sValue);
		$sDisplay = trim($sDisplay);
		$sSelected = ((is_array($this->vDefault) && in_array($sValue, $this->vDefault)) || (!is_array($this->vDefault) && $this->vDefault == $sValue)) ? ' selected' : '';
		return sprintf('<option value="%s"%s>%s</option>', $this->bCastToInt ? (int)$sValue : htmlspecialchars($sValue), $sSelected, htmlspecialchars($sDisplay));
	}

	function AddOption($sValue, $sDisplay)
	{
		$i = count($this->aOptions);
		$this->aOptions[$i] = array();
		$this->aOptions[$i][0] = $this->bCastToInt ? (int)$sValue : $sValue;
		$this->aOptions[$i][1] = $sDisplay;
	}

	function GetHTML()
	{
		$sHtml = '<select id="' . $this->sName . '" name="' . $this->sName;
		if ($this->iSize > 1)
			$sHtml .= '[]" multiple size="' . strval($this->iSize);

		$sHtml .= '"';

		if ($this->sOnChange != '')
			$sHtml .= ' onchange="' . $this->sOnChange . '"';

		if ($this->bHidden)
			$sHtml .= ' style="display: none;"';

		$sHtml .= '>';

		if ($this->iSize < 2 && $this->sZeroOption != '')
			$sHtml .= $this->GetOption(0, $this->sZeroOption);

		// $this->aOptions should be created w/$db->FetchAllRows
		for ($i = 0; $i < count($this->aOptions); $i++)
			$sHtml .= $this->GetOption($this->aOptions[$i][0], $this->aOptions[$i][1]);

		$sHtml .= '</select>';

		return $sHtml;
	}

	function Render()
	{
		echo $this->GetHTML();
	}

	function SetOptionsFromDb($table, $keyField, $valField, $filter = '', $order = '')
	{
		$sql = "SELECT $keyField, $valField FROM $table";
		if ($filter != '')
			$sql .= ' WHERE ' . $filter;
		$sql .= ' ORDER BY ';
		if ($order != '')
			$sql .= $order;
		else
			$sql .= $valField;

		$this->SetFromQuery($sql);
	}

	function SetFromQuery($sql)
	{
		if ($this->oDB == NULL)
		{
			$this->oDB = new DbProvider;
		}

		if ($this->oDB->Query($sql))
		{
			$this->aOptions = $this->oDB->FetchAllRows();
			$this->oDB->FreeResult();
		}
	}
}
?>
