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

// Generate JavaScript for attribute sets
class AttributeSetJsHelper
{
	public $bActiveOnly;
	public $bActions;
	public $bPriorities;
	public $bSeverities;
	public $bStatuses;
	public $bModules;
	public $bStatusTypes;
	public $bDepartments;
	public $forWhat;
	public $db;
	public $arrSets;
	public $bIsPublicUser;

	public function __construct()
	{
		global $g_oSec;

		$this->bActiveOnly = false;
		$this->bActions = false;
		$this->bPriorities = false;
		$this->bSeverities = false;
		$this->bStatuses = false;
		$this->bModules = false;
		$this->bStatusTypes = false;
		$this->bDepartments = false;
		$this->forWhat = DCL_ENTITY_WORKORDER;
		$this->arrSets = array();

		$this->bIsPublicUser = $g_oSec->IsPublicUser();
	}

	private function BuildSelectArray($table)
	{
		switch($table)
		{
			case 'actions':
				$typeid = 1;
				break;
			case 'priorities':
				$typeid = 2;
				break;
			case 'severities':
				$typeid = 3;
				break;
			case 'statuses':
				$typeid = 4;
				break;
		}

		printf("var a%d=new Array();\n", $typeid);
		$sql = "SELECT id, name FROM $table";
		if ($this->bActiveOnly)
			$sql .= " WHERE active = 'Y'";

		$sql .= ' ORDER BY name';

		$this->db->Query($sql);
		while ($this->db->next_record())
		{
			printf("a%d[%d]='%s';\n", $typeid, $this->db->f(0), $this->db->f(1));
		}
	}

	private function BuildModuleArray()
	{
		$sql = 'SELECT m.product_module_id, m.product_id, m.module_name, m.active From dcl_product_module m';
		if ($this->bIsPublicUser || $this->bActiveOnly)
		{
			$sql .= ", products p WHERE m.product_id = p.id";

			if ($this->bIsPublicUser)
				$sql .= " AND p.is_public = 'Y'";

			if ($this->bActiveOnly)
				$sql .= " AND m.active = 'Y' AND p.active = 'Y'";
		}

		$sql .= ' ORDER BY m.product_id, m.module_name';

		$this->db->Query($sql);
		print("var pm=new Array();\n");
		if ($this->db->next_record())
		{
			$iCurrIdx = -1;
			do
			{
				if ($iCurrIdx != $this->db->f(1))
				{
					$iCurrIdx = $this->db->f(1);
					printf("pm[%d]=new Array();\n", $iCurrIdx);
				}

				printf("pm[%d].push(new Array(%d, '%s', '%s'));\n", $iCurrIdx, $this->db->f(0), $this->db->f(2), $this->db->f(3));
			}
			while ($this->db->next_record());
		}
	}

	private function BuildStatusTypeArray()
	{
		$sql = 'SELECT dcl_status_type_id, dcl_status_type_name, id, name From dcl_status_type, statuses Where dcl_status_type_id = dcl_status_type';
		if ($this->bActiveOnly)
			$sql .= " WHERE statuses.active = 'Y'";

		$sql .= ' ORDER BY dcl_status_type_id, name';

		$this->db->Query($sql);
		print("var st=new Array();\n");
		if ($this->db->next_record())
		{
			$iCurrIdx = -1;
			do
			{
				if ($iCurrIdx != $this->db->f(0))
				{
					$iCurrIdx = $this->db->f(0);
					printf("st[%d]=new Array();\n", $iCurrIdx);
				}

				printf("st[%d].push(new Array(%d, '%s', '%s'));\n", $iCurrIdx, $this->db->f(2), $this->db->f(3), $this->db->f(1));
			}
			while ($this->db->next_record());
		}
	}

	private function BuildDepartmentArray()
	{
		$sql = 'SELECT a.id, a.name, b.id, b.short From departments a, personnel b Where a.id = b.department';
		if ($this->bActiveOnly)
			$sql .= " WHERE personnel.active = 'Y'";

		$sql .= ' ORDER BY a.name, b.short';

		$this->db->Query($sql);
		print("var dpt=new Array();\n");
		if ($this->db->next_record())
		{
			$iCurrIdx = -1;
			do
			{
				if ($iCurrIdx != $this->db->f(0))
				{
					$iCurrIdx = $this->db->f(0);
					printf("dpt[%d]=new Array();\n", $iCurrIdx);
				}

				printf("dpt[%d].push(new Array(%d, '%s', '%s'));\n", $iCurrIdx, $this->db->f(2), $this->db->f(3), $this->db->f(1));
			}
			while ($this->db->next_record());
		}
	}

	private function BuildMapArray($table)
	{
		switch($table)
		{
			case 'actions':
				$typeid = 1;
				$order = 'b.name';
				break;
			case 'priorities':
				$typeid = 2;
				$order = 'a.weight';
				break;
			case 'severities':
				$typeid = 3;
				$order = 'a.weight';
				break;
			case 'statuses':
				$typeid = 4;
				$order = 'b.name';
				break;
		}

		if ($this->forWhat == DCL_ENTITY_TICKET)
			$field = 'tcksetid';
		else
			$field = 'wosetid';

		print("\nvar m$typeid=new Array();\n");

		reset($this->arrSets);
		while (list($setid, $junk) = each($this->arrSets))
		{
			$query = "SELECT a.keyid FROM attributesetsmap a, $table b WHERE ";
			$query .= "a.setid=$setid AND a.typeid=$typeid AND b.id=a.keyid ORDER BY $order";

			$this->db->Query($query);
			$prevID = -1;
			while ($this->db->next_record())
			{
				if ($prevID != $setid)
				{
					printf("\nm%d[%d]=new Array();", $typeid, $setid);
					$prevID = $setid;
				}
				printf("m%d[%d].push(%d);", $typeid, $setid, $this->db->f(0));
			}
		}
	}

	private function BuildProductSetArray()
	{
		if ($this->forWhat == DCL_ENTITY_TICKET)
			$field = 'tcksetid';
		else
			$field = 'wosetid';

		$query = "SELECT id,$field FROM products";
		if ($this->bIsPublicUser || $this->bActiveOnly)
		{
			$query .= ' WHERE';

			if ($this->bIsPublicUser)
				$query .= " is_public = 'Y'";

			if ($this->bActiveOnly)
			{
				if ($this->bIsPublicUser)
					$query .= ' AND';

				$query .= " active = 'Y'";
			}
		}

		$query .= ' ORDER BY id';

		$this->db->Query($query);

		print("\nvar p = new Array();\n");
		while ($this->db->next_record())
		{
			$id = $this->db->f(0);
			$setid = $this->db->f(1);

			// Save unique set ids
			if (!IsSet($this->arrSets[$setid]))
				$this->arrSets[$setid] = 1;

			printf("p[%d]=%d;\n", $id, $setid);
		}
	}

	private function BuildChgFunction($table)
	{
		$err = '';
		switch($table)
		{
			case 'actions':
				$typeid = 1;
				$ctrl = 'action';
				break;
			case 'priorities':
				$typeid = 2;
				$ctrl = 'priority';
				break;
			case 'severities':
				$typeid = 3;
				if ($this->forWhat == DCL_ENTITY_WORKORDER)
					$ctrl = 'severity';
				else
					$ctrl = 'type';
				break;
			case 'statuses':
				$typeid = 4;
				$ctrl = 'status';
				break;
		}

		printf("\nfunction chg%s(f){\n", $ctrl);
		print(" var a=f.elements[\"product\"].options[f.elements[\"product\"].selectedIndex].value;\n");
		print(" var c=f.elements[\"$ctrl\"];\n");
		print(" if (!c) return;\n");
		print(" var j=c.options[c.selectedIndex].value;\n"); // Save selected index just in case
		print(" c.length=1;\n"); // Keep the "Select One" option
		printf(" if(m%d[p[a]]){\n", $typeid);
		printf("  for(var i=0;i<m%d[p[a]].length;i++){\n", $typeid);
		print("    var o=new Option();\n");
		printf("    o.value=m%d[p[a]][i];\n", $typeid);
		printf("    o.text=a%d[m%d[p[a]][i]];\n", $typeid, $typeid);
		print("    o.selected=(j==o.value);\n");
		print("    c.options[c.length]=o;\n");
		print("  }\n");
		print(" }\n");
		if ($typeid == 2)
			print(" if (c.length==1) alert('" . STR_CMMN_NOPRIORITIESFORPRODUCT . "');\n");
		else
			print(" if (c.length==1) alert('" . STR_CMMN_NOSEVERITIESFORPRODUCT . "');\n");
		print("}\n");

	}

	private function BuildDependentList($func, $mainName, $dependentName, $arrayName)
	{
		$err = '';

		print("function $func(f){\n");
		print(" var oMain=f.elements['$mainName'];\n");
		print(" if (!oMain) oMain=f.elements['" . $mainName . "[]'];\n");
		print(" var isMulti=oMain.multiple;\n");
		print(" if (isMulti){\n");
		print("  var c=f.elements['" . $dependentName . "[]'];\n");
		print("  if (!c) return;\n");
		print("  var _arr=new Array();\n");
		print("  if (c.selectedIndex>-1){\n");
		print("   for (var i = c.selectedIndex; i < c.length; i++){\n");
		print("    if (c.options[i].selected){\n");
		print("     _arr[c.options[i].value]=true;\n");
		print("    }\n");
		print("   }\n");
		print("  }\n");
		print("  c.length=0;\n");
		print("  if (oMain.selectedIndex > -1){\n");
		print("   for (i=oMain.selectedIndex;i<oMain.length;i++){\n");
		print("    if (oMain.options[i].selected){\n");
		print("     a=oMain.options[i].value;\n");
		print("     if(" . $arrayName . "[a]){\n");
		print("      for(var j=0;j<" . $arrayName . "[a].length;j++){\n");
		print("       var o=new Option();\n");
		print("       o.value=a+','+" . $arrayName . "[a][j][0];\n");
		print("       o.text=oMain.options[i].text+'::'+" . $arrayName . "[a][j][1];\n");
		print("       o.selected=(_arr[o.value]==true);\n");
		print("       c.options[c.length]=o;\n");
		print("      }\n");
		print("     }\n");
		print("    }\n");
		print("   }\n");
		print("  }\n");
		print("  return;\n");
		print(" }\n");
		print(" var a=f.elements['$mainName'].options[f.elements['$mainName'].selectedIndex].value;\n");
		print(" var c=f.elements['$dependentName'];\n");
		print(" if (!c) return;\n");
		print(" var j=c.options[c.selectedIndex].value;\n"); // Save selected index just in case
		print(" c.length=1;\n"); // Keep the "Select One" option for drop down
		print(" if(" . $arrayName . "[a]){\n");
		print("  for(var i=0;i<" . $arrayName . "[a].length;i++){\n");
		print("    var o=new Option();\n");
		print("    o.value=" . $arrayName . "[a][i][0];\n");
		print("    o.text=" . $arrayName . "[a][i][1];\n");
		print("    o.selected=(j==o.value);\n");
		print("    c.options[c.length]=o;\n");
		print("  }\n");
		print(" }\n");
		print("}\n");
	}

	private function BuildChgModuleFunction()
	{
		$this->BuildDependentList('chgModule', 'product', 'module_id', 'pm');
	}

	private function BuildChgStatusTypeFunction()
	{
		$this->BuildDependentList('chgStatusType', 'dcl_status_type', 'status', 'st');
	}

	private function BuildChgDepartmentFunction()
	{
		$this->BuildDependentList('chgDepartment', 'department', 'personnel', 'dpt');
	}

	public function DisplayAttributeScript()
	{
		if (!$this->bActions && !$this->bPriorities && !$this->bSeverities && !$this->bStatuses && !$this->bModules)
			return;

		$this->db = new ActionModel(); // Just for data access - doesn't matter which

		print("<script language=\"JavaScript1.2\">\n");

		$calls = '';

		// Must be called first to build unique array of sets
		$this->BuildProductSetArray();

		if ($this->bActions)
		{
			$this->BuildSelectArray('actions');
			$this->BuildMapArray('actions');
			$this->BuildChgFunction('actions');
			$calls .= " chgaction(f);\n";
		}

		if ($this->bPriorities)
		{
			$this->BuildSelectArray('priorities');
			$this->BuildMapArray('priorities');
			$this->BuildChgFunction('priorities');
			$calls .= " chgpriority(f);\n";
		}

		if ($this->bSeverities)
		{
			$this->BuildSelectArray('severities');
			$this->BuildMapArray('severities');
			$this->BuildChgFunction('severities');
			if ($this->forWhat == DCL_ENTITY_WORKORDER)
				$calls .= " chgseverity(f);\n";
			else
				$calls .= " chgtype(f);\n";
		}

		if ($this->bStatuses)
		{
			$this->BuildSelectArray('statuses');
			$this->BuildMapArray('statuses');
			$this->BuildChgFunction('statuses');
			$calls .= " chgstatus(f);\n";
		}

		if ($this->bModules)
		{
			$this->BuildModuleArray();
			$this->BuildChgModuleFunction();
			$calls .= " chgModule(f);\n";
		}

		if ($this->bStatusTypes)
		{
			$this->BuildStatusTypeArray();
			$this->BuildChgStatusTypeFunction();
			$calls .= " chgStatusType(f);\n";
		}

		if ($this->bDepartments)
		{
			$this->BuildDepartmentArray();
			$this->BuildChgDepartmentFunction();
			$calls .= " chgDepartment(f);\n";
		}

		print("function productSelChange(f){\n");
		print(" var c=f.elements[\"product\"];\n");
		print(" if(c.options[c.selectedIndex].value<1)return;\n");
		print($calls);
		print("}\n");

		print('</script>');
	}
}
