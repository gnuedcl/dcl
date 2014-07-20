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

class boView
{
	var $title;
	var $table;
	var $columns;
	var $columnhdrs;
	var $groups;
	var $order;
	var $filter;
	var $filternot;
	var $filterdate;
	var $filterlike;
	var $filterstart;
	var $logicdate;
	var $logiclike;
	var $joins;
	var $urlpieces;
	var $style;
	var $startrow;
	var $numrows;
	var $m_oDB;

	function boView()
	{
		$this->Clear();
		$this->startrow = 0;
		$this->numrows = 0;
		$this->urlpieces = array(
				'vt',
				'vti',
				'vc',
				'vch',
				'vg',
				'vo',
				'vf',
				'vfn',
				'vfd',
				'vfl',
				'vs'
			);

		$this->m_oDB = null;
	}

	function ClearDef($def)
	{
		$this->$def = array();
	}

	function Clear()
	{
		// Set some defaults
		$this->title = '';
		$this->table = 'workorders';
		$this->columns = array();
		$this->columnhdrs = array();
		$this->groups = array();
		$this->order = array();
		$this->filter = array();
		$this->filternot = array();
		$this->filterdate = array();
		$this->filterlike = array();
		$this->filterstart = array();
		$this->joins = array();
		$this->logicdate = 'OR';
		$this->logiclike = 'OR';
		$this->style = 'spreadsheet';
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
		$retVal .= '&vs=' . $this->style;
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
		if (count($this->order) > 0)
			$retVal .= '&';
		$retVal .= $this->GetURLArray('vo', $this->order);
		if (count($this->filter) > 0)
			$retVal .= '&';
		$retVal .= $this->GetURLArray('vf', $this->filter);
		if (count($this->filternot) > 0)
			$retVal .= '&';
		$retVal .= $this->GetURLArray('vfn', $this->filternot);
		if (count($this->filterdate) > 0)
			$retVal .= '&';
		$retVal .= $this->GetURLArray('vfd', $this->filterdate);
		if (count($this->filterlike) > 0)
			$retVal .= '&';
		$retVal .= $this->GetURLArray('vfl', $this->filterlike);
		if (count($this->filterstart) > 0)
			$retVal .= '&';
		$retVal .= $this->GetURLArray('vfs', $this->filterstart);

		return $retVal;
	}

	function GetFormElement($var, $val)
	{
		return sprintf('<input type="hidden" name="%s" value="%s">', htmlspecialchars($var, ENT_QUOTES, 'UTF-8'), htmlspecialchars($val, ENT_QUOTES, 'UTF-8')) . phpCrLf;
	}

	function GetForm()
	{
		$retVal = $this->GetFormElement('vt', $this->table);
		$retVal .= $this->GetFormElement('vs', $this->style);
		if ($this->title != '')
			$retVal .= $this->GetFormElement('vti', $this->title);

		$arrItems = array('vc' => 'columns', 'vch' => 'columnhdrs', 'vg' => 'groups', 'vo' => 'order',
				'vf' => 'filter', 'vfn' => 'filternot', 'vfd' => 'filterdate', 'vfl' => 'filterlike', 'vfs' => 'filterstart');
		while (list($attr, $arr) = each($arrItems))
		{
			if (count($this->$arr) > 0)
			{
				list($var, $val) = explode('=', $this->GetURLArray($attr, $this->$arr, false));
				$retVal .= $this->GetFormElement($var, $val);
			}
		}

		return $retVal;
	}
	
	function FixName($sName)
	{
		$aFix = array('accounts' => 'dcl_org', 'revision' => 'reported_version_id.product_version_text');
		if (mb_strpos($sName, '.') > -1)
		{
			// Field
			list($sTable, $sField) = explode('.', $sName);
			foreach ($aFix as $sOldName => $sNewName)
			{
				if ($sTable == $sOldName)
				{
					return $sNewName . '.' . $sField;
				}
			}
		}
		else
		{
			// Table
			foreach ($aFix as $sOldName => $sNewName)
			{
				if ($sName == $sOldName)
				{
					return $sNewName;
				}
			}
		}
		
		return $sName;
	}
	
	function FixArray($aArray)
	{
		$aNewArray = array();
		
		foreach ($aArray as $iIndex => $sName)
		{
			$aNewArray[$iIndex] = $this->FixName($sName);
		}
		
		return $aNewArray;
	}

	function SetFromURL()
	{
		$this->Clear();

		if (IsSet($_REQUEST['vt']))
			$this->table = $this->FixName($_REQUEST['vt']);

		if (IsSet($_REQUEST['vs']))
			$this->style = $_REQUEST['vs'];

		if (IsSet($_REQUEST['vti']))
		{
			$o = new PersonnelModel();
			$this->title = $o->GPCStripSlashes($_REQUEST['vti']);
		}

		if (IsSet($_REQUEST['vc']))
			$this->columns = $this->FixArray(explode(',', $_REQUEST['vc']));

		if (IsSet($_REQUEST['vch']))
			$this->columnhdrs = explode(',', $_REQUEST['vch']);

		if (IsSet($_REQUEST['vg']))
			$this->groups = $this->FixArray(explode(',', $_REQUEST['vg']));

		if (IsSet($_REQUEST['vo']))
			$this->order = $this->FixArray(explode(',', $_REQUEST['vo']));

		$filterSet = array(
				'vf' => 'filter',
				'vfn' => 'filternot',
				'vfd' => 'filterdate',
				'vfl' => 'filterlike',
				'vfs' => 'filterstart');

		foreach ($filterSet as $urlName => $filterName)
		{
			if (IsSet($_REQUEST[$urlName]))
			{
				$allFilters = explode(',', $_REQUEST[$urlName]);

				// Get the field
				while (list($key, $field) = each($allFilters))
				{
					$field = $this->FixName($field);
					
					// Get how many are in there
					list($key, $numValues) = each($allFilters);
					for ($i = 0; $i < $numValues; $i++)
					{
						// Get that many values and store for that field
						list($key, $value) = each($allFilters);
						$this->AddDef($filterName, $field, $value);
					}
				}
			}
		}
	}

	function SetFromURLString($strURL)
	{
		$this->Clear();

		parse_str($strURL);

		if (IsSet($vt))
			$this->table = $this->FixName($vt);

		if (IsSet($vs))
			$this->style = $vs;

		if (IsSet($vti))
			$this->title = $vti;

		if (IsSet($vc) && trim($vc) != '')
			$this->columns = $this->FixArray(explode(',', $vc));

		if (IsSet($vch) && trim($vch) != '')
			$this->columnhdrs = explode(',', $vch);

		if (IsSet($vg) && trim($vg) != '')
			$this->groups = $this->FixArray(explode(',', $vg));

		if (IsSet($vo) && trim($vo) != '')
			$this->order = $this->FixArray(explode(',', $vo));

		$filterSet = array(
				'vf' => 'filter',
				'vfn' => 'filternot',
				'vfd' => 'filterdate',
				'vfl' => 'filterlike',
				'vfs' => 'filterstart');

		foreach ($filterSet as $urlName => $filterName)
		{
			if (IsSet($$urlName) && trim($$urlName) != '')
			{
				$allFilters = explode(',', $$urlName);

				// Get the field
				while (list($key, $field) = each($allFilters))
				{
					$field = $this->FixName($field);

					// Get how many are in there
					list($key, $numValues) = each($allFilters);
					for ($i = 0; $i < $numValues; $i++)
					{
						// Get that many values and store for that field
						list($key, $value) = each($allFilters);
						$this->AddDef($filterName, $field, $value);
					}
				}
			}
		}
	}

	function AddDef($which, $field, $value = '')
	{
		if ($this->table == 'workorders')
		{
			if ($field == 'account')
				$field = 'dcl_wo_account.account_id';
			else if ($field == 'revision')
				$field = 'dcl_product_version.product_version_text';
		}
		
		$aMember =& $this->$which;
		if (is_array($value))
		{
			foreach ($value as $qvalue)
			{
				if (mb_substr($which, 0, 6) == 'filter')
				{
					if (!isset($aMember[$field]))
						$aMember[$field] = array();
					
					$aMember[$field][] = $qvalue;
				}
				else
					$aMember[] = $qvalue;
			}
		}
		else
		{
			if (mb_substr($which, 0, 6) == 'filter')
			{
				if (!isset($aMember[$field]))
					$aMember[$field] = array();
					
				$aMember[$field][] = $value;
			}
			else
				$aMember[] = $value;
		}
	}

	function RemoveDef($which, $field)
	{
		if ($this->table == 'workorders')
		{
			if ($field == 'account')
				$field = 'dcl_wo_account.account_id';
			else if ($field == 'revision')
				$field = 'dcl_product_version.product_version_text';
		}
		
		if (mb_substr($which, 0, 6) == 'filter')
		{
			$aFilter =& $this->$which;
			unset($aFilter[$field]);
		}
		else
			$this->$which = array();
	}

	function ReplaceDef($which, $field, $value = '')
	{
		$this->RemoveDef($which, $field);
		$this->AddDef($which, $field, $value);
	}

	// That's Comma Separated List :)
	function GetCSLFromArray(&$arr, $appendTableForJoin = false, $bProcessDates = false, $bOrderBy = false)
	{
		global $phpgw_baseline;

		if (!isset($phpgw_baseline[$this->table]))
			LoadSchema($this->table);
		
		reset($arr);
		$retVal = '';

		if (count($arr) > 0)
		{
			if ($appendTableForJoin == true && count($this->joins) > 0)
			{
				while (list($key, $field) = each($arr))
				{
					if ($retVal != '')
						$retVal .= ',';

					if ($bProcessDates)
					{
						$iColonIdx = mb_strpos($field, ':');
						if ($iColonIdx > 0)
						{
							$func = mb_substr($field, 0, $iColonIdx);
							$agg = mb_substr($field, $iColonIdx + 1);
							if (isset($phpgw_baseline[$this->table]) && isset($phpgw_baseline[$this->table]['aggregates']) && isset($phpgw_baseline[$this->table]['aggregates'][$func]) && isset($phpgw_baseline[$this->table]['aggregates'][$func][$agg]))
							{
								$retVal .= '(' . $phpgw_baseline[$this->table]['aggregates'][$func][$agg] . ') AS _count_' . $agg . '_';
							}
							
							continue;
						}
						else if (mb_strpos($field, '.') > 0)
												{
							list($sTable, $sField) = explode('.', $field);
							$sRealTable = $sTable;
						}
						else
						{
							$sTable = $this->table;
							$sRealTable = $sTable;
							$sField = $field;
						}

						if (mb_strlen($sTable) == 1)
						{
							if ($sTable == 'a' || $sTable == 'b' || $sTable == 'c' || $sTable == 'g')
								$sRealTable = 'personnel';
							else if ($sTable == 'd' || $sTable == 'e' || $sTable == 'f')
								$sRealTable = 'dcl_product_version';
						}
							
						if (!$this->m_oDB)
							$this->m_oDB = new DbProvider;

						LoadSchema($sRealTable);
						if ($phpgw_baseline[$sRealTable]['fd'][$sField]['type'] == 'timestamp')
							$retVal .= $this->m_oDB->ConvertTimestamp($sTable . '.' . $sField, $sField);
						else if ($phpgw_baseline[$sRealTable]['fd'][$sField]['type'] == 'date')
							$retVal .= $this->m_oDB->ConvertDate($sTable . '.' . $sField, $sField);
						else
							$retVal .= $sTable . '.' . $sField;
					}
					else
					{
						$iColonIdx = mb_strpos($field, ':');
						if ($iColonIdx > 0)
						{
							$func = mb_substr($field, 0, $iColonIdx);
							$agg = mb_substr($field, $iColonIdx + 1);
							if (isset($phpgw_baseline[$this->table]) && isset($phpgw_baseline[$this->table]['aggregates']) && isset($phpgw_baseline[$this->table]['aggregates'][$func]) && isset($phpgw_baseline[$this->table]['aggregates'][$func][$agg]))
							{
								$retVal .= ($bOrderBy ? '_count_' . $agg . '_ DESC' : '(' . $phpgw_baseline[$this->table]['aggregates'][$func][$agg] . ') AS _count_' . $agg . '_');
							}
						}
						else if (mb_strpos($field, '.') > 0)
							$retVal .= $field; // He said they've already got one!
						else
							$retVal .= $this->table . '.' . $field;
					}
				}
			}
			else
			{
				foreach ($arr as $field)
				{
					if ($retVal != '')
						$retVal .= ',';
					
					$iColonIdx = mb_strpos($field, ':');
					if ($iColonIdx > 0)
					{
						$func = mb_substr($field, 0, $iColonIdx);
						$agg = mb_substr($field, $iColonIdx + 1);
						if (isset($phpgw_baseline[$this->table]) && isset($phpgw_baseline[$this->table]['aggregates']) && isset($phpgw_baseline[$this->table]['aggregates'][$func]) && isset($phpgw_baseline[$this->table]['aggregates'][$func][$agg]))
						{
							$retVal .= '(' . $phpgw_baseline[$this->table]['aggregates'][$func][$agg] . ') AS _count_' . $agg . '_';
						}
					}
					else
					{
						$retVal .= $field;
					}
				}
			}
		}

		return $retVal;
	}

	function GetJoinForTable($table)
	{
		global $phpgw_baseline;
		
		if ($table == '' || $table == $this->table)
			return '';

		$join = '';
		
		if (!isset($phpgw_baseline[$this->table]))
			LoadSchema($this->table);
		
		if (isset($phpgw_baseline[$this->table]['joins']) && 
			isset($phpgw_baseline[$this->table]['joins'][$table]))
		{
			$join = $phpgw_baseline[$this->table]['joins'][$table];
		}	

		return $join;
	}

	function AppendJoins(&$arr)
	{
		global $dcl_info, $g_oSec;

		if (count($arr) > 0)
		{
			$joinon = '';
			$i = 0;
			foreach ($arr as $key => $field)
			{
				// If field is an array, then it contains values - $key is our real field
				$bIsValues = is_array($field);
				if ($bIsValues)
					$field = $key;

				if (mb_strpos($field, '.') > 0)
				{
					list($table, $tablefield) = explode('.', $field);
					if ($table == $this->table)
						continue;

					$iJoinType = 1; // 1 = normal, 2 = left
					switch ($table)
					{
						case 'a':
							$table = 'personnel a';
							break;
						case 'b':
							$table = 'personnel b';
							break;
						case 'c':
							$table = 'personnel c';
							break;
						case 'd':
							$table = 'dcl_product_version d';
							break;
						case 'e':
							$table = 'dcl_product_version e';
							break;
						case 'f':
							$table = 'dcl_product_version f';
							break;
						case 'g':
							$table = 'personnel g';
							$iJoinType = 2;
							break;
						case 'responsible':
							$table = 'personnel a';
							if ($bIsValues)
							{
								$arr[str_replace('responsible', 'a', $key)] = $arr[$key];
								unset($arr[$key]);
							}
							else
								$arr[$i] = str_replace('responsible', 'a', $arr[$i]);
							break;
						case 'closedby':
							$table = 'personnel b';
							if ($bIsValues)
							{
								$arr[str_replace('closedby', 'b', $key)] = $arr[$key];
								unset($arr[$key]);
							}
							else
								$arr[$i] = str_replace('closedby', 'b', $arr[$i]);
							break;
						case 'createdby':
						case 'createby':
							if ($bIsValues)
							{
								$arr[str_replace($table, 'c', $key)] = $arr[$key];
								unset($arr[$key]);
							}
							else
								$arr[$i] = str_replace($table, 'c', $arr[$i]);
							$table = 'personnel c';
							break;
						case 'actionby':
							if ($bIsValues)
							{
								$arr[str_replace($table, 'g', $key)] = $arr[$key];
								unset($arr[$key]);
							}
							else
								$arr[$i] = str_replace($table, 'g', $arr[$i]);
							
							$table = 'personnel g';
							$iJoinType = 2;
							break;
						case 'reportto':
							$table = 'personnel a';
							if ($bIsValues)
							{
								$arr[str_replace('reportto', 'a', $key)] = $arr[$key];
								unset($arr[$key]);
							}
							else
								$arr[$i] = str_replace('reportto', 'a', $arr[$i]);
							break;
						case 'reported_version_id':
							$table = 'dcl_product_version d';
							$iJoinType = 2;
							if ($bIsValues)
							{
								$arr[str_replace('reported_version_id', 'd', $key)] = $arr[$key];
								unset($arr[$key]);
							}
							else
								$arr[$i] = str_replace('reported_version_id', 'd', $arr[$i]);
							break;
						case 'targeted_version_id':
							$table = 'dcl_product_version e';
							$iJoinType = 2;
							if ($bIsValues)
							{
								$arr[str_replace('targeted_version_id', 'e', $key)] = $arr[$key];
								unset($arr[$key]);
							}
							else
								$arr[$i] = str_replace('targeted_version_id', 'e', $arr[$i]);
							break;
						case 'fixed_version_id':
							$table = 'dcl_product_version f';
							$iJoinType = 2;
							if ($bIsValues)
							{
								$arr[str_replace('fixed_version_id', 'f', $key)] = $arr[$key];
								unset($arr[$key]);
							}
							else
								$arr[$i] = str_replace('fixed_version_id', 'f', $arr[$i]);
							break;
						case 'accounts':
						case 'dcl_projects':
						case 'projectmap':
						case 'dcl_product_module':
						case 'dcl_wo_account':
						case 'dcl_entity_tag':
						case 'dcl_tag':
						case 'dcl_entity_hotlist':
						case 'dcl_hotlist':
						case 'dcl_entity_source':
						case 'dcl_org':
						case 'dcl_org_addr':
						case 'dcl_org_email':
						case 'dcl_org_phone':
						case 'dcl_org_url':
						case 'dcl_org_contact':
						case 'dcl_contact':
						case 'dcl_contact_addr':
						case 'dcl_contact_email':
						case 'dcl_contact_phone':
						case 'dcl_contact_url':
						case 'dcl_contact_license':
						case 'timecards':
							$iJoinType = 2;
							break;
					}

					if (!IsSet($this->joins[$table]))
					{
						if (!($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y' &&
								$this->table == 'workorders' &&
								($table == 'dcl_wo_account' || $table == 'accounts' || $table == 'dcl_org') &&
								!in_array('accounts.name', $this->order) &&
								!in_array('accounts.name', $this->groups) &&
								!in_array('accounts.name', $this->columns) &&
								!in_array('dcl_org.name', $this->order) &&
								!in_array('dcl_org.name', $this->groups) &&
								!in_array('dcl_org.name', $this->columns)) &&
							!(($this->table == 'workorders' || $this->table == 'tickets') &&
								($table == 'dcl_entity_tag' || $table == 'dcl_tag') &&
								!in_array('dcl_tag.tag_desc', $this->order) &&
								!in_array('dcl_tag.tag_desc', $this->groups) &&
								!in_array('dcl_tag.tag_desc', $this->columns)) &&
							!(($this->table == 'workorders' || $this->table == 'tickets') &&
								($table == 'dcl_entity_hotlist' || $table == 'dcl_hotlist') &&
								!in_array('dcl_hotlist.hotlist_tag', $this->order) &&
								!in_array('dcl_hotlist.hotlist_tag', $this->groups) &&
								!in_array('dcl_hotlist.hotlist_tag', $this->columns)))
						{
							// work orders are associated to projects in the projectmap table
							// so append it here - we don't want it in the selected columns
	
							// Ensure we join dcl_wo_account before accounts
							// Join dcl_org_contact before dcl_org for dcl_contact table
							if ($table == 'dcl_projects')
								$this->joins['projectmap'] = 2;
							else if ($this->table == 'workorders' && ($table == 'accounts' || $table == 'dcl_org') && !isset($this->joins['dcl_wo_account']))
								$this->joins['dcl_wo_account'] = 2;
							else if (($this->table == 'workorders' || $this->table == 'tickets') && $table == 'dcl_tag' && !isset($this->joins['dcl_entity_tag']))
								$this->joins['dcl_entity_tag'] = 2;
							else if (($this->table == 'workorders' || $this->table == 'tickets') && $table == 'dcl_hotlist' && !isset($this->joins['dcl_entity_hotlist']))
								$this->joins['dcl_entity_hotlist'] = 2;
							else if ($this->table == 'dcl_contact' && $table == 'dcl_org' && !isset($this->joins['dcl_org_contact']))
								$this->joins['dcl_org_contact'] = 2;
							else if ($this->table == 'personnel' && $table == 'dcl_contact_email' && !isset($this->joins['dcl_contact']))
								$this->joins['dcl_contact'] = 2;
							else if ($this->table == 'dcl_product_build' && $table == 'workorders')
							{
								$this->joins['dcl_product_version'] = 1;
								$this->joins['dcl_product_version_item'] = 1;
							}
							else if ($this->table == 'workorders' && $table == 'personnel g')
								$this->joins['timecards'] = 2;

							$this->joins[$table] = $iJoinType;
						}
					}
				}

				$i++;
			}
		}

		if ($g_oSec->IsPublicUser())
		{
			if (($this->table == 'workorders' || $this->table == 'tickets') && !isset($this->joins['products']))
				$this->joins['products'] = 1;
		}
	}

	private function RequireValidCriteriaForMember(array &$member)
	{
		foreach ($member as $key => $field)
		{
			// If field is an array, then it contains values - $key is our real field
			$bIsValues = is_array($field);
			if ($bIsValues)
				$field = $key;

			// Valid for aliases or order by clause
			$spaceIndex = mb_strpos($field, ' ');
			if ($spaceIndex > -1)
			{
				$field = trim(mb_substr($field, $spaceIndex));

				// Make sure there are no others, though
				$aliasOrSortOrder = trim(mb_substr($field, $spaceIndex + 1));
				if (mb_strpos($aliasOrSortOrder, ' ') > -1)
					throw new InvalidDataException();
			}

			Filter::RequireSqlName($field);
		}
	}

	private function RequireValidCriteria()
	{
		Filter::RequireSqlName($this->table);

		$this->RequireValidCriteriaForMember($this->columns);
		$this->RequireValidCriteriaForMember($this->order);
		$this->RequireValidCriteriaForMember($this->groups);
		$this->RequireValidCriteriaForMember($this->filter);
		$this->RequireValidCriteriaForMember($this->filternot);
		$this->RequireValidCriteriaForMember($this->filterlike);
		$this->RequireValidCriteriaForMember($this->filterdate);
	}

	function GetSQL($bCount = false)
	{
		global $dcl_info, $g_oSec, $g_oSession;

		$this->RequireValidCriteria();

		$this->joins = array();
		$this->AppendJoins($this->order);
		$this->AppendJoins($this->groups);
		$this->AppendJoins($this->columns);
		$this->AppendJoins($this->filter);
		$this->AppendJoins($this->filternot);
		$this->AppendJoins($this->filterlike);

		$sql = 'SELECT ';

		if ($bCount)
		{
			$sql .= 'COUNT(*)';
		}
		else
		{
			if (count($this->columns) > 0 || count($this->groups) > 0)
			{
				if (count($this->groups) > 0)
				{
					$aGroups = $this->groups;
					foreach ($aGroups as $key => $group)
					{
						// If we group by weight, we want to display the name while ordering by weight
						if ($group == 'priorities.weight')
							$aGroups[$key] = 'priorities.name';
						else if ($group == 'severities.weight')
							$aGroups[$key] = 'severities.name';
					}

					$sql .= $this->GetCSLFromArray($aGroups, true);
				}

				if (count($this->columns) > 0)
				{
					if (count($this->groups) > 0)
						$sql .= ",";

					$sql .= $this->GetCSLFromArray($this->columns, true, true);
				}
			}
			else
				$sql .= '*';

			if ($this->table == 'workorders')
			{
				// If we show account, but don't group by it, then we will want to display an icon to show
				// the extra accounts.  This count will determine if we need to display the marker for more info
				// and will be left out of the final rendering by htmlView
				if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y' && (in_array('dcl_org.name', $this->columns) || in_array('accounts.name', $this->columns)))
				{
					$sql .= ', (select count(*) from dcl_wo_account where wo_id = workorders.jcn And seq = workorders.seq) As _num_accounts_';
				}
				
				// Same for tags, but to determine if we really need to query for more tags
				if (in_array('dcl_tag.tag_desc', $this->columns))
				{
					$sql .= ', (select count(*) from dcl_entity_tag where entity_id = ' . DCL_ENTITY_WORKORDER . ' AND entity_key_id = workorders.jcn And entity_key_id2 = workorders.seq) As _num_tags_';
				}
				
				// One more time for hotlists
				if (in_array('dcl_hotlist.hotlist_tag', $this->columns))
				{
					$sql .= ', (select count(*) from dcl_entity_hotlist where entity_id = ' . DCL_ENTITY_WORKORDER . ' AND entity_key_id = workorders.jcn And entity_key_id2 = workorders.seq) As _num_hotlist_';
				}
			}
			else if ($this->table == 'tickets')
			{
				// Tags will work the same in tickets as work orders
				if (in_array('dcl_tag.tag_desc', $this->columns))
				{
					$sql .= ', (select count(*) from dcl_entity_tag where entity_id = ' . DCL_ENTITY_TICKET . ' AND entity_key_id = tickets.ticketid) As _num_tags_';
				}
				
				// Hotlists, too...
				if (in_array('dcl_hotlist.hotlist_tag', $this->columns))
				{
					$sql .= ', (select count(*) from dcl_entity_hotlist where entity_id = ' . DCL_ENTITY_TICKET . ' AND entity_key_id = tickets.ticketid) As _num_hotlist_';
				}
			}
		}

		$sql .= ' FROM ' . $this->table;

		$bDoneDidWhere = false;

		// Add joins, if any
		if (count($this->joins) > 0)
		{
			$joinsql = '';

			// Ensure these join in the proper order
			if (isset($this->joins['dcl_status_type']))
			{
				if (isset($this->joins['statuses']))
				{
					$statusesSQL = $this->joins['statuses'];
					unset($this->joins['statuses']);
				}
				else
					$statusesSQL = $this->m_oDB->JoinKeyword . ' statuses ON ' . $this->GetJoinForTable('statuses');

				$typesSQL = $this->joins['dcl_status_type'];
				unset($this->joins['dcl_status_type']);

				$this->joins['statuses'] = $statusesSQL;
				$this->joins['dcl_status_type'] = $typesSQL;
			}

			if (!$this->m_oDB)
				$this->m_oDB = new DbProvider;

			foreach ($this->joins as $table => $iJoinType)
			{
				if ($iJoinType == 2)
					$joinsql .= ' LEFT JOIN';
				else
					$joinsql .= ' ' . $this->m_oDB->JoinKeyword;

				$joinsql .= ' ' . $table . ' ON ' . $this->GetJoinForTable($table);
			}

			$sql .= $joinsql . ' ';
		}

		$productSQL = '';
		$moduleSQL = '';
		$deptSQL = '';
		$responsibleSQL = '';
		$createbySQL = '';
		$closedbySQL = '';
		$sTagFilter = '';
		$sHotlistFilter = '';
		$bOrgFilter = false;
		$bProductFilter = false;
		$bFirst = true;
		if (count($this->filter) > 0)
		{
			if ($bDoneDidWhere == false)
			{
				$bDoneDidWhere = true;
				$sql .= ' WHERE ';
			}
			else
				$sql .= ' AND ';

			foreach ($this->filter as $field => $values)
			{
				// prepend table name if not specified to avoid ambiguity
				if (mb_strpos($field, '.') < 1)
					$field = $this->table . '.' . $field;

				if ($this->table == 'workorders' && preg_match('/^.*\.jcn/i', $field))
				{
					if ($bFirst == false)
						$sql .= ' AND ';

					$bFirst = false;

					$sJCNSQL = '';
					$sJCNSEQSQL = '';
					$iNumValues = count($values);
					for ($i = 0; $i < $iNumValues; $i++)
					{
						$sValue = $values[$i];
						if (mb_strpos($sValue, '-') > 0)
						{
							if ($sJCNSEQSQL != '')
								$sJCNSEQSQL .= ' OR ';

							list($jcn, $seq) = explode('-', $sValue);
							$sJCNSEQSQL .= "(workorders.jcn=$jcn AND workorders.seq=$seq)";
						}
						else
						{
							if ($sJCNSQL != '')
								$sJCNSQL .= ',';

							$sJCNSQL .= $sValue;
						}
					}

					$sql .= '(';
					if ($sJCNSQL != '')
						$sql .= "jcn IN ($sJCNSQL)";
					if ($sJCNSQL != '' && $sJCNSEQSQL != '')
						$sql .= ' OR ';
					$sql .= $sJCNSEQSQL;
					$sql .= ')';
				}
				else if (preg_match('/^.*\.product/i', $field))
				{
					if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
					{
						$values = array_intersect($values, $g_oSession->GetProductFilter());
						if (count($values) == 0)
							$values[] = -1;
					}
						
					$bProductFilter = true;
					if (count($values) == 1)
						$productSQL = "($field=" . $this->GetCSLFromArray($values) . ')';
					else
						$productSQL = "($field in (" . $this->GetCSLFromArray($values) . '))';
				}
				else if (preg_match('/^.*\.module_id/i', $field))
				{
					if (count($values) == 1)
						$moduleSQL = "($field=" . $this->GetCSLFromArray($values) . ')';
					else
						$moduleSQL = "($field in (" . $this->GetCSLFromArray($values) . '))';
				}
				else if (preg_match('/^.*\.department/i', $field))
				{
					if (count($values) == 1)
						$deptSQL = "($field=" . $this->GetCSLFromArray($values) . ')';
					else
						$deptSQL = "($field in (" . $this->GetCSLFromArray($values) . '))';
				}
				else if (preg_match('/^.*\.responsible/i', $field))
				{
					if (count($values) == 1)
						$responsibleSQL = "($field=" . $this->GetCSLFromArray($values) . ')';
					else
						$responsibleSQL = "($field in (" . $this->GetCSLFromArray($values) . '))';
				}
				else if (preg_match('/^.*\.create[d]?by/i', $field))
				{
					if (count($values) == 1)
						$createbySQL = "($field=" . $this->GetCSLFromArray($values) . ')';
					else
						$createbySQL = "($field in (" . $this->GetCSLFromArray($values) . '))';
				}
				else if (preg_match('/^.*\.closedby/i', $field))
				{
					if (count($values) == 1)
						$closedbySQL = "($field=" . $this->GetCSLFromArray($values) . ')';
					else
						$closedbySQL = "($field in (" . $this->GetCSLFromArray($values) . '))';
				}
				else if (preg_match('/^.*\.tag_desc/i', $field))
				{
					$iEntity = null;
					if ($this->table == 'workorders')
						$iEntity = DCL_ENTITY_WORKORDER;
					else if ($this->table == 'tickets')
						$iEntity = DCL_ENTITY_TICKET;
					else if ($this->table == 'dcl_projects')
						$iEntity = DCL_ENTITY_PROJECT;
						
					if ($iEntity !== null)
					{
						if ($bFirst == false)
							$sql .= ' AND ';
	
						$bFirst = false;

						$oTag = new TagModel();
						$sTagFilter = $oTag->getExistingIdsByName($this->GetCSLFromArray($values));
						if (!in_array('dcl_tag.tag_desc', $this->order) &&
							!in_array('dcl_tag.tag_desc', $this->groups) &&
							!in_array('dcl_tag.tag_desc', $this->columns)
							)
						{
							if ($this->table == 'workorders')
							{
								$sql .= "((workorders.jcn in (select entity_key_id from dcl_entity_tag where entity_id = $iEntity AND dcl_entity_tag.tag_id in ($sTagFilter)))";
								$sql .= " AND (workorders.seq in (select entity_key_id2 from dcl_entity_tag where entity_id = $iEntity AND workorders.jcn = entity_key_id And dcl_entity_tag.tag_id in ($sTagFilter))";
								$sql .= '))';
							}
							else if ($this->table == 'tickets')
							{
								$sql .= "(tickets.ticketid in (select entity_key_id from dcl_entity_tag where entity_id = $iEntity AND dcl_entity_tag.tag_id in ($sTagFilter)))";
							}
						}
						else
						{
							$sql .= "(dcl_tag.tag_id in ($sTagFilter))";
						}
					}
				}
				else if (preg_match('/^.*\.hotlist_tag/i', $field))
				{
					$iEntity = null;
					if ($this->table == 'workorders')
						$iEntity = DCL_ENTITY_WORKORDER;
					else if ($this->table == 'tickets')
						$iEntity = DCL_ENTITY_TICKET;
					else if ($this->table == 'dcl_projects')
						$iEntity = DCL_ENTITY_PROJECT;
						
					if ($iEntity !== null)
					{
						if ($bFirst == false)
							$sql .= ' AND ';
	
						$bFirst = false;

						$oHotlist = new HotlistModel();
						$sHotlistFilter = $oHotlist->getExistingIdsByName($this->GetCSLFromArray($values));
						if (!in_array('dcl_hotlist.hotlist_tag', $this->order) &&
							!in_array('dcl_hotlist.hotlist_tag', $this->groups) &&
							!in_array('dcl_hotlist.hotlist_tag', $this->columns)
							)
						{
							if ($this->table == 'workorders')
							{
								$sql .= "((workorders.jcn in (select entity_key_id from dcl_entity_hotlist where entity_id = $iEntity AND dcl_entity_hotlist.hotlist_id in ($sHotlistFilter)))";
								$sql .= " AND (workorders.seq in (select entity_key_id2 from dcl_entity_hotlist where entity_id = $iEntity AND workorders.jcn = entity_key_id And dcl_entity_hotlist.hotlist_id in ($sHotlistFilter))";
								$sql .= '))';
							}
							else if ($this->table == 'tickets')
							{
								$sql .= "(tickets.ticketid in (select entity_key_id from dcl_entity_hotlist where entity_id = $iEntity AND dcl_entity_hotlist.hotlist_id in ($sHotlistFilter)))";
							}
						}
						else
						{
							$sql .= "(dcl_hotlist.hotlist_id in ($sHotlistFilter))";
						}
					}
				}
				else if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y' &&
						$this->table == 'workorders' &&
						!in_array('accounts.name', $this->order) &&
						!in_array('accounts.name', $this->groups) &&
						!in_array('accounts.name', $this->columns) &&
						!in_array('dcl_org.name', $this->order) &&
						!in_array('dcl_org.name', $this->groups) &&
						!in_array('dcl_org.name', $this->columns) &&
						preg_match('/^.*\.account/i', $field))
				{
					// Multi-account filter only does subselect - not MySQL compatible yet
					if ($bFirst == false)
						$sql .= ' AND ';

					$bFirst = false;

					if ($g_oSec->IsOrgUser())
					{
						$sOrgs = $g_oSession->Value('member_of_orgs');
						if ($sOrgs != '')
						{
							$aOrgs = explode(',', $sOrgs);
							$values = array_intersect($values, $aOrgs);
							if (count($values) == 0)
								$values = array('-1');
						}
						else
							$values = array('-1');

						$bOrgFilter = true;
					}

					$sql .= "((workorders.jcn in (select wo_id from dcl_wo_account where account_id in (" . $this->GetCSLFromArray($values) . ")))";
					$sql .= " AND (workorders.seq in (select seq from dcl_wo_account where workorders.jcn = wo_id And account_id in (" . $this->GetCSLFromArray($values) . "))";
					$sql .= '))';
				}
				else if (preg_match('/^.*\.account/i', $field) || preg_match('/^.*\.account_id/i', $field) || preg_match('/^.*\.org_id/i', $field))
				{
					if ($bFirst == false)
						$sql .= ' AND ';

					$bFirst = false;

					if ($g_oSec->IsOrgUser())
					{
						$sOrgs = $g_oSession->Value('member_of_orgs');
						if ($sOrgs != '')
						{
							$aOrgs = explode(',', $sOrgs);
							$values = array_intersect($values, $aOrgs);
							if (count($values) == 0)
								$values = array('-1');
						}
						else
							$values = array('-1');

						$bOrgFilter = true;
					}

					if (count($values) == 1)
						$sql .= "($field=" . $this->GetCSLFromArray($values) . ')';
					else
						$sql .= "($field in (" . $this->GetCSLFromArray($values) . '))';
				}
				else
				{
					if ($bFirst == false)
						$sql .= ' AND ';

					$bFirst = false;
					if (count($values) == 1)
						$sql .= "($field=" . $this->GetCSLFromArray($values) . ')';
					else
						$sql .= "($field in (" . $this->GetCSLFromArray($values) . '))';
				}
			}
		}

		// Now handle product and module specially because module is exclusive to product, but
		// we may still have other products where they don't filter by module
		if ($productSQL != '' && $moduleSQL != '')
		{
			if ($bFirst == false)
				$sql .= ' AND ';

			$bFirst = false;
			$sql .= '(' . $productSQL . ' OR ' . $moduleSQL . ')';
		}
		else if ($productSQL != '')
		{
			if ($bFirst == false)
				$sql .= ' AND ';

			$bFirst = false;
			$sql .= $productSQL;
		}
		else if ($moduleSQL != '')
		{
			if ($bFirst == false)
				$sql .= ' AND ';

			$bFirst = false;
			$sql .= $moduleSQL;
		}

		$arrPersonnel = array();
		if ($deptSQL != '')
			$arrPersonnel[] = $deptSQL;
		if ($responsibleSQL != '')
			$arrPersonnel[] = $responsibleSQL;
		if ($createbySQL != '')
			$arrPersonnel[] = $createbySQL;
		if ($closedbySQL != '')
			$arrPersonnel[] = $closedbySQL;

		if (count($arrPersonnel) > 0)
		{
			if ($bFirst == false)
				$sql .= ' AND ';

			$bFirst = false;

			if (count($arrPersonnel) > 1)
			{
				$sql .= '(';
				for ($i = 0; $i < count($arrPersonnel); $i++)
				{
					if ($i > 0)
						$sql .= ' OR ';

					$sql .= $arrPersonnel[$i];
				}
				$sql .= ')';
			}
			else
				$sql .= $arrPersonnel[0];
		}

		if (count($this->filternot) > 0)
		{
			if ($bDoneDidWhere == false)
			{
				$bDoneDidWhere = true;
				$sql .= ' WHERE ';
			}
			else
				$sql .= ' AND ';

			$bFirst = true;
			reset($this->filternot);
			while (list($field, $values) = each($this->filternot))
			{
				// prepend table name if not specified to avoid ambiguity
				if (mb_strpos($field, '.') < 1)
					$field = $this->table . '.' . $field;

				if ($bFirst == false)
					$sql .= ' AND ';

				$bFirst = false;
				if (count($values) == 1)
					$sql .= "($field!=" . $this->GetCSLFromArray($values) . ')';
				else
					$sql .= "($field not in (" . $this->GetCSLFromArray($values) . '))';
			}
		}

		$objWO = new WorkOrderModel();

		if (count($this->filterdate) > 0)
		{
			if ($bDoneDidWhere == false)
			{
				$bDoneDidWhere = true;
				$sql .= ' WHERE (';
			}
			else
				$sql .= ' AND (';

			$bFirst = true;
			foreach ($this->filterdate as $field => $values)
			{
				// prepend table name if not specified to avoid ambiguity
				if (mb_strpos($field, '.') < 1)
					$field = $this->table . '.' . $field;

				if ($bFirst == false)
					$sql .= ' ' . $this->logicdate . ' ';

				$bFirst = false;
				if ($values[0] != '' && $values[1] != '')
					$sql .= sprintf('(%s between %s and %s)', $field, $objWO->DisplayToSQL($values[0]), $objWO->DisplayToSQL($values[1]));
				else if ($values[0] != '')
					$sql .= sprintf('(%s >= %s)', $field, $objWO->DisplayToSQL($values[0]));
				else if ($values[1] != '')
					$sql .= sprintf('(%s <= %s)', $field, $objWO->DisplayToSQL($values[1]));
			}

			$sql .= ')';
		}

		if (count($this->filterlike) > 0)
		{
			if ($bDoneDidWhere == false)
			{
				$bDoneDidWhere = true;
				$sql .= ' WHERE (';
			}
			else
				$sql .= ' AND (';

			$bFirst = true;
			foreach ($this->filterlike as $field => $values)
			{
				// prepend table name if not specified to avoid ambiguity
				if (mb_strpos($field, '.') < 1)
					$field = $this->table . '.' . $field;

				if ($bFirst == false)
					$sql .= ' ' . $this->logiclike . ' ';

				$bFirst = false;
				$sql .= sprintf('(%s like %s)', $objWO->GetUpperSQL($field), mb_strtoupper($objWO->Quote('%' . $values[0] . '%')));
			}

			$sql .= ')';
		}

		if (count($this->filterstart) > 0)
		{
			if ($bDoneDidWhere == false)
			{
				$bDoneDidWhere = true;
				$sql .= ' WHERE (';
			}
			else
				$sql .= ' AND (';

			$bFirst = true;
			foreach ($this->filterstart as $field => $values)
			{
				// prepend table name if not specified to avoid ambiguity
				if (mb_strpos($field, '.') < 1)
					$field = $this->table . '.' . $field;

				if ($bFirst == false)
					$sql .= ' ' . $this->logiclike . ' ';

				$bFirst = false;
				$sql .= sprintf('(%s like %s)', $objWO->GetUpperSQL($field), $objWO->Quote(mb_strtoupper($values[0]) . '%'));
			}

			$sql .= ')';
		}

		if ($this->table == 'workorders')
		{
			// If we group by account, we'll join the whole lot together.  This will cause a work order
			// with n accounts to appear in the report n times.  Otherwise, if we only sort or show the account
			// column, we'll get the first account (in order) and display a link to show the other accounts
			// as needed
			if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y' && (in_array('accounts.name', $this->columns) || in_array('dcl_org.name', $this->columns)))
			{
				$aOrgFilter = array();
				if ($g_oSec->IsOrgUser())
					$aOrgFilter = explode(',', $g_oSession->Value('member_of_orgs'));

				if (isset($this->filter['dcl_wo_account.account_id']) && is_array($this->filter['dcl_wo_account.account_id']))
				{
					if (count($aOrgFilter) > 0)
						$aOrgFilter = array_intersect($this->filter['dcl_wo_account.account_id'], $aOrgFilter);
					else
						$aOrgFilter = $this->filter['dcl_wo_account.account_id'];
				}

				$sql .= ' And (dcl_wo_account.account_id is null Or dcl_wo_account.account_id = ';
				$sql .= '(Select min(account_id) From dcl_wo_account where wo_id = workorders.jcn And seq = workorders.seq';
				if (count($aOrgFilter) > 0)
					$sql .= ' AND account_id IN (' . join(',', $aOrgFilter) . ')';

				$sql .= '))';
			}
			
			// Same for Tags
			if (in_array('dcl_tag.tag_desc', $this->columns))
			{
				$sql .= ' And (dcl_entity_tag.tag_id is null Or dcl_entity_tag.tag_id = ';
				$sql .= '(Select min(tag_id) From dcl_entity_tag where entity_id = ' . DCL_ENTITY_WORKORDER . ' AND entity_key_id = workorders.jcn And entity_key_id2 = workorders.seq';
				if ($sTagFilter != '')
					$sql .= ' AND tag_id IN (' . $sTagFilter . ')';

				$sql .= '))';
			}
			
			// And hotlists...
			if (in_array('dcl_hotlist.hotlist_tag', $this->columns))
			{
				$sql .= ' And (dcl_entity_hotlist.hotlist_id is null Or dcl_entity_hotlist.hotlist_id = ';
				$sql .= '(Select min(hotlist_id) From dcl_entity_hotlist where entity_id = ' . DCL_ENTITY_WORKORDER . ' AND entity_key_id = workorders.jcn And entity_key_id2 = workorders.seq';
				if ($sHotlistFilter != '')
					$sql .= ' AND hotlist_id IN (' . $sHotlistFilter . ')';

				$sql .= '))';
			}
		}
		else if ($this->table == 'tickets')
		{
			// Same for ticket tags as work order tags
			if (in_array('dcl_tag.tag_desc', $this->columns))
			{
				$sql .= ' And (dcl_entity_tag.tag_id is null Or dcl_entity_tag.tag_id = ';
				$sql .= '(Select min(tag_id) From dcl_entity_tag where entity_id = ' . DCL_ENTITY_TICKET . ' AND entity_key_id = tickets.ticketid';
				if ($sTagFilter != '')
					$sql .= ' AND tag_id IN (' . $sTagFilter . ')';

				$sql .= '))';
			}

			// Hotlists
			if (in_array('dcl_hotlist.hotlist_tag', $this->columns))
			{
				$sql .= ' And (dcl_entity_hotlist.hotlist_id is null Or dcl_entity_hotlist.hotlist_id = ';
				$sql .= '(Select min(hotlist_id) From dcl_entity_hotlist where entity_id = ' . DCL_ENTITY_TICKET . ' AND entity_key_id = tickets.ticketid';
				if ($sHotlistFilter != '')
					$sql .= ' AND hotlist_id IN (' . $sHotlistFilter . ')';

				$sql .= '))';
			}
		}

		// Add public restriction
		if ($g_oSec->IsPublicUser())
		{
			switch ($this->table)
			{
				case 'workorders':
				case 'tickets':
					$sql .= " AND products.is_public = 'Y'";
					// fall through to add public filter to table
				case 'products':
				case 'timecards':
				case 'ticketresolutions':
					$sql .= ' AND ' . $this->table . ".is_public = 'Y'";
					break;
			}
		}

		$sAccountSQL = '';		
		if ($this->table == 'workorders')
		{
			if ($g_oSec->IsOrgUser())
			{
				if (!$bOrgFilter)
				{
					$sOrgs = $g_oSession->Value('member_of_orgs');
					if ($sOrgs != '')
						$values = explode(',', $sOrgs);
					else
						$values = array('-1');
	
					$sAccountSQL = "((workorders.jcn in (select wo_id from dcl_wo_account where account_id in (" . $this->GetCSLFromArray($values) . ")))";
					$sAccountSQL .= " AND (workorders.seq in (select seq from dcl_wo_account where workorders.jcn = wo_id And account_id in (" . $this->GetCSLFromArray($values) . "))";
					$sAccountSQL .= '))';
				}
			}
			
			if (!$bProductFilter && ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace()))
			{
				if ($bDoneDidWhere == false)
				{
					$bDoneDidWhere = true;
					$sql .= ' WHERE ';
				}
				else
					$sql .= ' AND ';

				$sql .= 'product IN (' . join(',', $g_oSession->GetProductFilter()) . ')';
			}
			
			if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWSUBMITTED))
			{
				if ($bDoneDidWhere == false)
				{
					$bDoneDidWhere = true;
					$sql .= ' WHERE ';
				}
				else
					$sql .= ' AND ';
	
				$sql .= '(' . $this->table . '.createby = ' . DCLID;
				$sql .= ' OR ' . $this->table . '.contact_id = ' . $g_oSession->Value('contact_id');
				if ($sAccountSQL != '')
					$sql .= ' OR ' . $sAccountSQL;
					
				$sql .= ')';
			}
			else if ($sAccountSQL != '')
			{
				if ($bDoneDidWhere == false)
				{
					$bDoneDidWhere = true;
					$sql .= ' WHERE ';
				}
				else
					$sql .= ' AND ';
	
				$sql .= $sAccountSQL;
			}
		}
		else if ($this->table == 'tickets')
		{
			if ($g_oSec->IsOrgUser())
			{
				if (!$bOrgFilter)
					$sAccountSQL = 'account IN (' . $g_oSession->Value('member_of_orgs') . ')';
			}

			if (!$bProductFilter && ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace()))
			{
				if ($bDoneDidWhere == false)
				{
					$bDoneDidWhere = true;
					$sql .= ' WHERE ';
				}
				else
					$sql .= ' AND ';

				$sql .= 'product IN (' . join(',', $g_oSession->GetProductFilter()) . ')';
			}

			if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEWSUBMITTED))
			{
				if ($bDoneDidWhere == false)
				{
					$bDoneDidWhere = true;
					$sql .= ' WHERE ';
				}
				else
					$sql .= ' AND ';
	
				$sql .= '(' . $this->table . '.createdby = ' . DCLID;
				$sql .= ' OR ' . $this->table . '.contact_id = ' . $g_oSession->Value('contact_id');
				if ($sAccountSQL != '')
					$sql .= ' OR ' . $sAccountSQL;
					
				$sql .= ')';
			}
			else if ($sAccountSQL != '')
			{
				if ($bDoneDidWhere == false)
				{
					$bDoneDidWhere = true;
					$sql .= ' WHERE ';
				}
				else
					$sql .= ' AND ';
	
				$sql .= $sAccountSQL;
			}
		}

		if (!$bCount && (count($this->order) > 0 || count($this->groups) > 0))
		{
			$sql .= ' ORDER BY ';
			if (count($this->order) > 0 && count($this->groups) > 0)
				$sql .= $this->GetCSLFromArray(array_merge($this->groups, $this->order), true, false, true);
			else if (count($this->groups) > 0)
				$sql .= $this->GetCSLFromArray($this->groups, true, false, true);
			else
				$sql .= $this->GetCSLFromArray($this->order, true, false, true);
		}

		return $sql;
	}
}
