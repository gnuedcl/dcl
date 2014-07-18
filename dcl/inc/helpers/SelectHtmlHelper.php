<?php
/*
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

class SelectHtmlHelper
{
	public $DefaultValue;
	public $Id;
	public $Size;
	public $OnChange;
	public $FirstOption;
	public $Options;
	public $CastToInt;
	public $IsHidden;
	public $CssClass;

	private $_dbProvider;

	public function __construct()
	{
		$this->DefaultValue = 0;
		$this->Id = '';
		$this->Size = 0;
		$this->OnChange = '';
		$this->FirstOption = '';
		$this->Options = array();
		$this->CastToInt = false;
		$this->IsHidden = false;
		$this->_dbProvider = NULL;
		$this->CssClass = 'form-control';
	}

	private function GetOption($sValue, $sDisplay)
	{
		$sValue = trim($sValue);
		$sDisplay = trim($sDisplay);
		$sSelected = ((is_array($this->DefaultValue) && in_array($sValue, $this->DefaultValue)) || (!is_array($this->DefaultValue) && $this->DefaultValue == $sValue)) ? ' selected' : '';
		return sprintf('<option value="%s"%s>%s</option>', $this->CastToInt ? (int)$sValue : htmlspecialchars($sValue), $sSelected, htmlspecialchars($sDisplay));
	}

	public function AddOption($sValue, $sDisplay)
	{
		$i = count($this->Options);
		$this->Options[$i] = array();
		$this->Options[$i][0] = $this->CastToInt ? (int)$sValue : $sValue;
		$this->Options[$i][1] = $sDisplay;
	}

	public function GetHTML()
	{
		$sHtml = '<select id="' . $this->Id . '" name="' . $this->Id;
		if ($this->Size > 1)
			$sHtml .= '[]" multiple size="' . strval($this->Size);

		$sHtml .= '"';

		if ($this->OnChange != '')
			$sHtml .= ' onchange="' . $this->OnChange . '"';

		if ($this->IsHidden)
			$sHtml .= ' style="display: none;"';

		if ($this->CssClass != '')
			$sHtml .= ' class="' . $this->CssClass . '"';

		$sHtml .= '>';

		if ($this->Size < 2 && $this->FirstOption != '')
			$sHtml .= $this->GetOption(0, $this->FirstOption);

		// $this->aOptions should be created w/$db->FetchAllRows
		for ($i = 0; $i < count($this->Options); $i++)
			$sHtml .= $this->GetOption($this->Options[$i][0], $this->Options[$i][1]);

		$sHtml .= '</select>';

		return $sHtml;
	}

	public function Render()
	{
		echo $this->GetHTML();
	}

	public function SetOptionsFromDb($table, $keyField, $valField, $filter = '', $order = '')
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

	public function SetFromQuery($sql)
	{
		if ($this->_dbProvider == NULL)
		{
			$this->_dbProvider = new DbProvider();
		}

		if ($this->_dbProvider->Query($sql))
		{
			$this->Options = $this->_dbProvider->FetchAllRows();
			$this->_dbProvider->FreeResult();
		}
	}
}
