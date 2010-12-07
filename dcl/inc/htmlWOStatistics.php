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

LoadStringResource('wost');
LoadStringResource('wo');

class htmlWOStatistics
{
	function ShowUserVsProductStatusForm()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		$objPersonnel = new htmlPersonnel();
		$objProducts = new htmlProducts();

		$t = new DCL_Smarty();

		$t->assign('CMB_PEOPLE', $objPersonnel->GetCombo(0, 'people', 'lastfirst', 8, false));
		$t->assign('CMB_PRODUCTS', $objProducts->GetCombo(0, 'products', 'name', 0, 8, false));

		$t->Render('htmlWOStatisticsForm.tpl');
	}

	function ShowUserVsProductStatus()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$objProduct = new dbProducts();
		$objPersonnel = new dbPersonnel();
		$objStatuses = new dbStatuses();
		$objWorkorders = new dbWorkorders();
		
		$products = @DCL_Sanitize::ToIntArray($_REQUEST['products']);
		$people = @DCL_Sanitize::ToIntArray($_REQUEST['people']);
		$begindate = @DCL_Sanitize::ToDate($_REQUEST['begindate']);
		$enddate = @DCL_Sanitize::ToDate($_REQUEST['enddate']);

		if (count($products) < 1)
		{
			$query  = 'SELECT id FROM products ORDER BY name';
			$objProduct->Query($query);

			$products = array();
			while ($objProduct->next_record())
			{
				$products[count($products)] = $objProduct->f(0);
			}
			
			$objProduct->FreeResult();
		}

		$doingClosed = FALSE;
		if ($begindate !== null)
			$doingClosed = TRUE;
		if ($enddate !== null)
			$doingClosed = TRUE;

		$query = 'SELECT id FROM statuses WHERE dcl_status_type ';
		if ($doingClosed)
			$query .= '= 2';
		else
			$query .= '!= 2';

		$objStatuses->Query($query);
		$statii = array();
		while ($objStatuses->next_record())
		{
			$statii[count($statii)] = $objStatuses->f(0);
		}
		
		$objStatuses->FreeResult();

		if (count($people) < 1)
			$idWhere = 'id > 1';
		else
		{
			$idWhere = 'id in (';
			for ($i = 0; $i < count($people); $i++)
			{
				if ($i > 0)
					$idWhere .= ',';
				$idWhere .= $people[$i];
			}
			$idWhere .= ')';
		}
		
		$query = 'select distinct p.id, p.short from personnel p join dcl_user_role ur on p.id = ur.personnel_id ';
		$query .= 'join dcl_role_perm rp on ur.role_id = rp.role_id where ((entity_id = ';
		$query .= DCL_ENTITY_WORKORDER . ' and perm_id = ' . DCL_PERM_ACTION . ') or (entity_id = ';
		$query .= DCL_ENTITY_GLOBAL . ' and perm_id = ' . DCL_PERM_ADMIN . ')) ORDER BY short';

		$objPersonnel->Query($query);
		$person = array();
		while ($objPersonnel->next_record())
		{
			$person[count($person)] = $objPersonnel->f(0);
		}
		
		$objPersonnel->FreeResult();

		$query = 'SELECT product,status,responsible,';
		if ($doingClosed)
			$query .= 'totalhours';
		else
			$query .= 'esthours';
		$query .= ' FROM workorders, statuses WHERE workorders.status = statuses.id AND statuses.dcl_status_type ';
		if ($doingClosed)
		{
			$query .= '= 2';
			if ($begindate != '' && $enddate != '')
				$query .= ' AND closedon between ' . $objWorkorders->DisplayToSQL($begindate . ' 00:00:00') . ' AND ' . $objWorkorders->DisplayToSQL($enddate . ' 23:59:59');
			else if ($begindate != '')
				$query .= ' AND closedon >=' . $objWorkorders->DisplayToSQL($begindate . ' 00:00:00');
			else if ($enddate != '')
				$query .= ' AND closedon <=' . $objWorkorders->DisplayToSQL($enddate . ' 23:59:59');
		}
		else
			$query .= '!= 2';

		$objWorkorders->Query($query);

		for ($i = 0; $i < count($products) * count($statii) + count($statii) + 1; $i++)
		{
			for ($j = 0; $j < count($person) + 2; $j++)
			{
				$myArrayHours[$i][$j] = 0.0;
				$myArrayUnits[$i][$j] = 0;
			}
		}

		while ($objWorkorders->next_record())
		{
			$thisProduct = -1;
			$thisStatus = -1;
			$thisPerson = -1;

			$bFound = false;
			for ($j = 0; $j < count($products); $j++)
				if ($products[$j] == $objWorkorders->f(0))
				{
					$bFound = true;
					break;
				}
			if ($bFound)
				$thisProduct = $j;

			$bFound = false;
			for ($j = 0; $j < count($statii); $j++)
				if ($statii[$j] == $objWorkorders->f(1))
				{
					$bFound = true;
					break;
				}
			if ($bFound)
				$thisStatus = $j;

			$bFound = false;
			for ($j = 0; $j < count($person); $j++)
				if ($person[$j] == $objWorkorders->f(2))
				{
					$bFound = true;
					break;
				}
			if ($bFound)
				$thisPerson = $j;

			if ($thisProduct > -1 && $thisStatus > -1 && $thisPerson > -1)
			{
				$hours = $objWorkorders->f(3);

				$myArrayHours[$thisProduct * count($statii) + $thisStatus][$thisPerson] += $hours;
				$myArrayHours[count($products) * count($statii) + $thisStatus][$thisPerson] += $hours;
				$myArrayHours[count($products) * count($statii) + count($statii)][$thisPerson] += $hours;
				$myArrayHours[$thisProduct * count($statii) + $thisStatus][count($person)] += $hours;
				$myArrayHours[$thisProduct * count($statii)][count($person) + 1] += $hours;
				$myArrayHours[count($products) * count($statii) + $thisStatus][count($person)] += $hours;
				$myArrayHours[count($products) * count($statii) + count($statii)][count($person)] += $hours;

				$myArrayUnits[$thisProduct * count($statii) + $thisStatus][$thisPerson]++;
				$myArrayUnits[count($products) * count($statii) + $thisStatus][$thisPerson]++;
				$myArrayUnits[count($products) * count($statii) + count($statii)][$thisPerson]++;
				$myArrayUnits[$thisProduct * count($statii) + $thisStatus][count($person)]++;
				$myArrayUnits[$thisProduct * count($statii)][count($person) + 1]++;
				$myArrayUnits[count($products) * count($statii) + $thisStatus][count($person)]++;
				$myArrayUnits[count($products) * count($statii) + count($statii)][count($person)]++;
			}
		}
		
		$objWorkorders->FreeResult();

		print('<table border="0" cellspacing="0" cellpadding="1">');
		print('<tr><td>');
		print('<table border="0" cellspacing="2" cellpadding="1">');
		print('<tr>');
		print('<th rowspan="2">');
		print(STR_CMMN_LOGIN);
		print('</th>');
		for ($i = 0; $i < count($products); $i++)
		{
			print('<th colspan=' . count($statii));
			print('>');
			$objProduct->Load($products[$i]);
			print($objProduct->name);
			print('</th>');
		}
		print('<th colspan=' . (count($statii) + 1));
		print('>');
		print(STR_CMMN_TOTALS);
		print('</th></tr><tr>');
		$statusCol = '';
		for ($j = 0; $j < count($statii); $j++)
		{
			$statusCol .= '<th>';
			$objStatuses->Load($statii[$j]);
			$statusCol .= $objStatuses->short;
			$statusCol .= '</th>';
		}
		for ($i = 0; $i < count($products); $i++)
			print($statusCol);

       // For the totals, dontcha know?
		print($statusCol);
		printf('<th>%s</th>', STR_WOST_ALL);
		print('</tr>');
		for ($i = 0; $i < count($person) + 2; $i++)
		{
			if ($i < count($person) && $myArrayUnits[count($products) * count($statii) + count($statii)][$i] == 0)
				continue;

			if ($i < count($person))
			{
				$objPersonnel->Load($person[$i]);
				print('<tr><td>' . $objPersonnel->short . '</td>');
			}
			else if ($i == count($person))
					print('<tr><td>' . STR_WO_STATUS . '</td>');
				else
					print('<tr><td>' . STR_WO_PRODUCT . '</td>');

			for ($j = 0; $j < count($products) + 1; $j++)
			{
				for ($k = 0; $k < count($statii) + 1; $k++)
				{
					if (($i < (count($person) + 1) && 
								(($j < count($products) && $k < count($statii)) || 
									$j == count($products))) || 
							($i == (count($person) + 1) && $k == 0 && $j < count($products)))
					{
						$units = $myArrayUnits[$j * count($statii) + $k][$i];
						print('<td');
						if ($i == count($person) + 1)
							print(' align="center" colspan=' . count($statii));
						print('>');
						if ($units > 0)
						{
							$menuAction = 'menuAction=htmlWOStatistics.SearchFromStat';
							if ($k < count($statii) && $i < count($person) + 1)
								$menuAction .= '&status=' . $statii[$k];
							if ($j < count($products))
								$menuAction .= '&product=' . $products[$j];
							if ($i < count($person))
								$menuAction .= '&responsible=' . $person[$i];
							if ($begindate != '')
								$menuAction .= '&begindate=' . $begindate;
							if ($enddate != '')
								$menuAction .= '&enddate=' . $enddate;
							printf('<a class="adark" href="%s">', menuLink('', $menuAction));
							print($units . '(' . $myArrayHours[$j * count($statii) + $k][$i] . STR_WOST_HOURSABBREV . ')</a>');
						}
						else
							print('&nbsp;');
						print('</td>');
					}
				}
			}

			print('</tr>');
		}

		print('</table></td></tr></table>');
	}

	function SearchFromStat()
	{
		global $dcl_domain_info, $dcl_domain, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$responsible = DCL_Sanitize::ToInt($_REQUEST['responsible']);
		$product = DCL_Sanitize::ToInt($_REQUEST['product']);
		$status = DCL_Sanitize::ToInt($_REQUEST['status']);
		$begindate = DCL_Sanitize::ToDate($_REQUEST['begindate']);
		$enddate = DCL_Sanitize::ToDate($_REQUEST['enddate']);
		
		$obj = new dclDB;

		$objView = new boView();
		$objView->style = 'report';
		$objView->title = STR_WOST_SEARCHRESULTS;

		$objView->AddDef('columns', '', 
			array('jcn', 'seq', 'responsible.short', 'products.name', 'statuses.name', 'eststarton', 'deadlineon',
				'etchours', 'totalhours', 'summary'));

		$objView->AddDef('columnhdrs', '',
			array(STR_WO_JCN, STR_WO_SEQ, STR_WO_RESPONSIBLE, STR_WO_PRODUCT,
				STR_WO_STATUS, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_WO_SUMMARY));

		$objView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'jcn', 'seq'));

		if ($begindate !== null || $enddate !== null)
		{
			$objView->AddDef('filter', 'statuses.dcl_status_type', '2');
			$objView->AddDef('filterdate', 'closedon', array($obj->DisplayToSQL($begindate), $obj->DisplayToSQL($enddate)));
		}
		else if ($status !== null)
			$objView->AddDef('filter', 'status', $status);
		else
			$objView->AddDef('filternot', 'statuses.dcl_status_type', '2');

		if ($responsible !== null)
			$objView->AddDef('filter', 'responsible', $responsible);

		if ($product !== null)
			$objView->AddDef('filter', 'product', $product);

		$obj = CreateViewObject($objView->table);
		$obj->Render($objView);
	}
}
