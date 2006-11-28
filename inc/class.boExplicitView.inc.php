<?php
/*
 * $Id: class.boExplicitView.inc.php,v 1.1.1.1 2006/11/27 05:30:51 mdean Exp $
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

class boExplicitView
{
	var $title;
	var $table;
	var $sql;
	var $columns;
	var $columnhdrs;
	var $groups;
	var $urlpieces;
	var $startrow;
	var $numrows;
	var $m_oDB;

	function boExplicitView()
	{
		$this->Clear();
		$this->startrow = 0;
		$this->numrows = 0;
		$this->urlpieces = array(
				'vt',
				'vti',
				'vsql',
				'vc',
				'vch',
				'vg'
			);

		$this->m_oDB = null;
	}

	function ClearDef($def)
	{
	}

	function Clear()
	{
		// Set some defaults
		$this->title = '';
		$this->table = 'workorders';
		$this->columns = array();
		$this->columnhdrs = array();
		$this->groups = array();
	}

	function GetURLArray($field, &$arr, $encode = true)
	{
		$retVal = '';

		if (count($arr) > 0)
		{
			$retVal = $field . '=';
			$bFirst = true;
			foreach ($arr as $key => $value)
			{
				if (is_array($value))
				{
					if ($bFirst == false)
						$retVal .= ',';

					if (count($value) > 0)
					{
						$retVal .= $key . ',' . count($value);
						foreach ($value as $key => $realVal)
						{
							if ($encode)
								$retVal .= ',' . rawurlencode($realVal);
							else
								$retVal .= ',' . $realVal;
						}
					}
				}
				else
				{
					if ($bFirst == false)
						$retVal .= ',';

					if ($encode)
						$retVal .= rawurlencode($value);
					else
						$retVal .= $value;
				}

				$bFirst = false;
			}
		}

		return $retVal;
	}

	// Gets a URL to regenerate this view in another page
	function GetURL()
	{
		$retVal = 'vt=' . $this->table;
		$retVal .= '&vsql=' . $this->sql;
		if ($this->title != '')
			$retVal .= '&vti=' . rawurlencode($this->title);

		if (count($this->columns) > 0)
			$retVal .= '&';
			
		$retVal .= $this->GetURLArray('vc', $this->columns);

		if (count($this->columnhdrs) > 0)
			$retVal .= '&';
			
		$retVal .= $this->GetURLArray('vch', $this->columnhdrs);
		
		if (count($this->groups) > 0)
			$retVal .= '&';
		
		$retVal .= $this->GetURLArray('vg', $this->groups);

		return $retVal;
	}

	function GetFormElement($var, $val)
	{
		return sprintf('<input type="hidden" name="%s" value="%s">', $var, $val) . phpCrLf;
	}

	function GetForm()
	{
		$retVal = $this->GetFormElement('vt', $this->table);
		$retVal .= $this->GetFormElement('vsql', $this->sql);
		if ($this->title != '')
			$retVal .= $this->GetFormElement('vti', htmlspecialchars($this->title));

		$arrItems = array('vc' => 'columns', 'vch' => 'columnhdrs', 'vg' => 'groups');
		foreach ($arrItems as $attr => $arr)
		{
			if (count($this->$arr) > 0)
			{
				list($var, $val) = explode('=', $this->GetURLArray($attr, $this->$arr, false));
				$retVal .= $this->GetFormElement($var, htmlspecialchars($val));
			}
		}

		return $retVal;
	}

	function SetFromURL()
	{
		$this->Clear();

		if (IsSet($_REQUEST['vt']))
			$this->table = $_REQUEST['vt'];

		if (IsSet($_REQUEST['vsql']))
			$this->sql = $_REQUEST['vsql'];

		if (IsSet($_REQUEST['vti']))
		{
			$o = CreateObject('dcl.dbPersonnel');
			$this->title = $o->GPCStripSlashes($_REQUEST['vti']);
		}

		if (IsSet($_REQUEST['vc']))
			$this->columns = explode(',', $_REQUEST['vc']);

		if (IsSet($_REQUEST['vch']))
			$this->columnhdrs = explode(',', $_REQUEST['vch']);

		if (IsSet($_REQUEST['vg']))
			$this->groups = explode(',', $_REQUEST['vg']);
	}

	function SetFromURLString($strURL)
	{
		$this->Clear();

		parse_str($strURL);

		if (IsSet($vt))
			$this->table = $vt;

		if (IsSet($vssql))
			$this->sql = $vsql;

		if (IsSet($vti))
			$this->title = $vti;

		if (IsSet($vc) && trim($vc) != '')
			$this->columns = explode(',', $vc);

		if (IsSet($vch) && trim($vch) != '')
			$this->columnhdrs = explode(',', $vch);

		if (IsSet($vg) && trim($vg) != '')
			$this->groups = explode(',', $vg);
	}

	function AddDef($which, $field, $value = '')
	{
		if (is_array($value))
		{
			foreach ($value as $key => $qvalue)
			{
				$code = sprintf('$this->%s[] = "%s";', $which, $qvalue);
				eval($code);
			}					
		}
		else
		{
			$code = sprintf('$this->%s[] = "%s";', $which, $field);
			eval($code);
		}
	}
	
	function RemoveDef($which, $field)
	{
		$code = sprintf('$this->%s = array();', $which);

		eval($code);
	}
	
	function ReplaceDef($which, $field, $value = '')
	{
		$this->RemoveDef($which, $field);
		$this->AddDef($which, $field, $value);
	}

	function GetSQL($bCount = false)
	{
		if ($bCount)
		{
			return pregi_replace("/^SELECT (.*) FROM /", "SELECT COUNT(*) FROM ", $this->sql);
		}
		
		return $this->sql;
	}
}
?>
