<?php
/*
 * $Id: class.htmlTicketStatistics.inc.php,v 1.1.1.1 2006/11/27 05:30:43 mdean Exp $
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
LoadStringResource('tck');

class htmlTicketStatistics
{
	function ShowUserVsProductStatusForm()
	{
		global $dcl_info;

		commonHeader();

		$objPersonnel = CreateObject('dcl.htmlPersonnel');
		$objProducts = CreateObject('dcl.htmlProducts');
		
		$t = CreateSmarty();
		$t->assign('CMB_PEOPLE', $objPersonnel->GetCombo(0, 'people', 'lastfirst', 8, false));
		$t->assign('CMB_PRODUCTS', $objProducts->GetCombo(0, 'products', 'name', 0, 8, false));
		
		SmartyDisplay($t, 'htmlTicketStatisticsForm.tpl');
	}

	function ShowUserVsProductStatus()
	{
		global $dcl_info;

		commonHeader();

		global $products, $people, $begindate, $enddate, $activity, $byaccount;

		$objProduct = CreateObject('dcl.dbProducts');
		$objStatuses = CreateObject('dcl.dbStatuses');
		$objTickets = CreateObject('dcl.dbTickets');

		$doingActivity = (IsSet($activity) && ($activity == '1'));
		$doingAccounts = (IsSet($byaccount) && ($byaccount == '1'));
		if ($doingAccounts)
		{
			$vsTable = 'dcl_org';
			$vsDesc = 'name';
			$vsField = 'account';
			$objPersonnel = CreateObject('dcl.dbOrg');
		}
		else
		{
			$vsTable = 'personnel';
			$vsDesc = 'short';
			$vsField = 'responsible';
			$objPersonnel = CreateObject('dcl.dbPersonnel');
		}

		if (count($products) < 1)
		{
			$query  = 'SELECT id FROM products ORDER BY name';
			$objProduct->Query($query);
			$products = $objProduct->FetchAllRows();
		}

		$doingClosed = FALSE;
		if ($begindate != '')
			$doingClosed = TRUE;
		if ($enddate != '')
			$doingClosed = TRUE;

		if ($doingActivity)
		{
			$statii = array(1 => 'PR', 2 => 'OP', 3 => 'CL', 4 => 'FW');
		}
		else
		{
			$query = 'SELECT id FROM statuses';
			$query .= ' WHERE dcl_status_type ';
			if ($doingClosed)
				$query .= '= 2';
			else
				$query .= '!= 2';

			$objStatuses->Query($query);
			$statii = $objStatuses->FetchAllRows();
		}


		if ($doingAccounts)
		{
			$query = 'SELECT org_id FROM dcl_org ORDER BY name';
		}
		else
		{
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

			$query = "SELECT id FROM personnel WHERE $idWhere ORDER BY short";
		}

		$objPersonnel->Query($query);
		$person = $objPersonnel->FetchAllRows();

		$query = "SELECT product,status,$vsField";

		if ($doingActivity)
		{
			$query .= ',createdon,closedon FROM tickets, statuses WHERE tickets.status = statuses.id AND (statuses.dcl_status_type != 2 OR (statuses.dcl_status_type = 2 AND ';

			if ($begindate != '' && $enddate != '')
				$query .= 'closedon between ' . $objTickets->DisplayToSQL($begindate . ' 00:00:00') . ' AND ' . $objTickets->DisplayToSQL($enddate . ' 23:59:59');
			else if ($begindate != '')
				$query .= 'closedon >=' . $objTickets->DisplayToSQL($begindate . ' 00:00:00');
			else if ($enddate != '')
				$query .= 'closedon <=' . $objTickets->DisplayToSQL($enddate . ' 23:59:59');

			$query .= '))';
		}
		else
		{
			$query .= ' FROM tickets, statuses WHERE tickets.status = statuses.id AND statuses.dcl_status_type ';
			if ($doingClosed)
			{
				$query .= '= 2';
				if ($begindate != '' && $enddate != '')
					$query .= ' AND closedon between ' . $objTickets->DisplayToSQL($begindate . ' 00:00:00') . ' AND ' . $objTickets->DisplayToSQL($enddate . ' 23:59:59');
				else if ($begindate != '')
					$query .= ' AND closedon >=' . $objTickets->DisplayToSQL($begindate . ' 00:00:00');
				else if ($enddate != '')
					$query .= ' AND closedon <=' . $objTickets->DisplayToSQL($enddate . ' 23:59:59');
			}
			else
				$query .= '!= 2';
		}

		$objTimestamp = new DCLTimestamp;
		$objNow = new DCLTimestamp;

		$objNow->SetFromDisplay($begindate . ' 00:00:00');

		$myArrayUnits = array();
		for ($i = 0; $i <= count($person) + 1; $i++)
		{
			for ($j = 0; $j <= (count($products) * count($statii) + count($statii)); $j++)
				$myArrayUnits[$j][$i] = 0;
		}

		$objTickets->Query($query);
		while ($objTickets->next_record())
		{
			$thisProduct = -1;
			$thisStatus = -1;
			$thisPerson = -1;
			$thisOpened = '';
			$thisClosed = '';

			for ($j = 0; $j < count($products); $j++)
			{
				if ($products[$j][0] == $objTickets->f(0))
				{
					$thisProduct = $j;
					break;
				}
			}

			for ($j = 0; $j < count($person); $j++)
			{
				if ($person[$j][0] == $objTickets->f(2))
				{
					$thisPerson = $j;
					break;
				}
			}

			if ($doingActivity)
			{
				if ($thisProduct > -1 && $thisPerson > -1)
				{
					$objTimestamp->SetFromDB($objTickets->f(3));

					// Opened before begin date?
					if ($objTimestamp->time < $objNow->time)
					{
						$myArrayUnits[$thisProduct * count($statii)][$thisPerson]++;
						$myArrayUnits[count($products) * count($statii)][$thisPerson]++;
						$myArrayUnits[$thisProduct * count($statii)][count($person)]++;
						$myArrayUnits[count($products) * count($statii)][count($person)]++;
					}
					else
					{
						$myArrayUnits[$thisProduct * count($statii) + 1][$thisPerson]++;
						$myArrayUnits[count($products) * count($statii) + 1][$thisPerson]++;
						$myArrayUnits[$thisProduct * count($statii) + 1][count($person)]++;
						$myArrayUnits[count($products) * count($statii) + 1][count($person)]++;
					}

					// Closed or forward?
					if ($objStatuses->GetStatusType($objTickets->f(1)) == 2)
					{
						$myArrayUnits[$thisProduct * count($statii) + 2][$thisPerson]++;
						$myArrayUnits[count($products) * count($statii) + 2][$thisPerson]++;
						$myArrayUnits[$thisProduct * count($statii) + 2][count($person)]++;
						$myArrayUnits[count($products) * count($statii) + 2][count($person)]++;
					}
					else
					{
						$myArrayUnits[$thisProduct * count($statii) + 3][$thisPerson]++;
						$myArrayUnits[count($products) * count($statii) + 3][$thisPerson]++;
						$myArrayUnits[$thisProduct * count($statii) + 3][count($person)]++;
						$myArrayUnits[count($products) * count($statii) + 3][count($person)]++;
					}

					$myArrayUnits[count($products) * count($statii) + count($statii)][$thisPerson]++;
					$myArrayUnits[$thisProduct * count($statii)][count($person) + 1]++;
					$myArrayUnits[count($products) * count($statii) + count($statii)][count($person)]++;
				}
			}
			else
			{
				for ($j = 0; $j < count($statii); $j++)
				{
					if ($statii[$j][0] == $objTickets->f(1))
					{
						$thisStatus = $j;
						break;
					}
				}

				if ($thisProduct > -1 && $thisStatus > -1 && $thisPerson > -1)
				{
					$myArrayUnits[$thisProduct * count($statii) + $thisStatus][$thisPerson]++;
					$myArrayUnits[count($products) * count($statii) + $thisStatus][$thisPerson]++;
					$myArrayUnits[count($products) * count($statii) + count($statii)][$thisPerson]++;
					$myArrayUnits[$thisProduct * count($statii) + $thisStatus][count($person)]++;
					$myArrayUnits[$thisProduct * count($statii)][count($person) + 1]++;
					$myArrayUnits[count($products) * count($statii) + $thisStatus][count($person)]++;
					$myArrayUnits[count($products) * count($statii) + count($statii)][count($person)]++;
				}
			}
		}

		print('<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=1>');
		print('<TR><TD>');
		print('<TABLE BORDER=0 CELLSPACING=2 CELLPADDING=1>');
		print('<TR>');
		print('<TH ROWSPAN=2>');
		print(STR_CMMN_LOGIN);
		print('</TH>');
		$ii = 0;
		for ($i = 0; $i < count($products); $i++)
		{
			if ($myArrayUnits[$i * count($statii)][count($person) + 1] == 0)
				continue;

			print('<TH COLSPAN=' . count($statii));
			print('>');
			$objProduct->Load($products[$i][0]);
			print($objProduct->name);
			print('</TH>');

			$ii++;
		}
		print('<TH COLSPAN=' . (count($statii) + 1));
		print('>');
		print(STR_CMMN_TOTALS);
		print('</TH></TR><TR>');
		$statusCol = '';
		for ($j = 0; $j < count($statii); $j++)
		{
			$statusCol .= '<TH>';
			if ($doingActivity)
			{
				$statusCol .= $statii[$j + 1];
			}
			else
			{
				$objStatuses->Load($statii[$j][0]);
				$statusCol .= $objStatuses->short;
			}
			$statusCol .= '</TH>';
		}
		for ($i = 0; $i < count($products); $i++)
		{
			if ($myArrayUnits[$i * count($statii)][count($person) + 1] == 0)
				continue;

			print($statusCol);
		}

       // For the totals, dontcha know?
		print($statusCol);
		printf('<TH>%s</TH>', STR_WOST_ALL);
		print('</TR>');
		for ($i = 0; $i < count($person) + 2; $i++)
		{
			if ($i < count($person) && $myArrayUnits[count($products) * count($statii) + count($statii)][$i] == 0)
				continue;

			if ($i < count($person))
			{
				$objPersonnel->Load($person[$i][0]);
				print('<TR><TD>' . $objPersonnel->short . '</TD>');
			}
			elseif ($i == count($person))
				print('<TR><TD>' . STR_TCK_STATUS . '</TD>');
			else
				print('<TR><TD>' . STR_TCK_PRODUCT . '</TD>');

			for ($j = 0; $j < count($products) + 1; $j++)
			{
				if ($j < count($products) && $myArrayUnits[$j * count($statii)][count($person) + 1] == 0)
					continue;

				for ($k = 0; $k < count($statii) + 1; $k++)
				{
					if (($i < (count($person) + 1) &&
								(($j < count($products) && $k < count($statii)) ||
									$j == count($products))) ||
							($i == (count($person) + 1) && $k == 0 && $j < count($products)))
					{
						$units = $myArrayUnits[$j * count($statii) + $k][$i];
						print('<TD');
						if ($i == count($person) + 1)
							print(' ALIGN=CENTER COLSPAN=' . count($statii));
						print('>');
						if ($units > 0)
						{
							$sMenuAction = 'menuAction=htmlTicketStatistics.SearchFromStat';
							if ($k < count($statii) && $i < count($person) + 1)
								$sMenuAction .= '&status=' . $statii[$k][0];
							if ($j < count($products))
								$sMenuAction .= '&product=' . $products[$j][0];
							if ($i < count($person))
								$sMenuAction .= '&responsible=' . $person[$i][0];
							if ($begindate != '')
								$sMenuAction .= '&begindate=' . $begindate;
							if ($enddate != '')
								$sMenuAction .= '&enddate=' . $enddate;
							printf('<a href="%s">%s</a>', menuLink('', $sMenuAction), $units);
						}
						else
							print('&nbsp;');
						print('</TD>');
					}
				}
			}

			print('</TR>');
		}

		print('</TABLE>');
		print('</TD>');
		print('</TR>');
		print('</TABLE>');
	}

	function SearchFromStat()
	{
		global $dcl_domain_info, $dcl_domain;

		commonHeader();

		global $responsible, $product, $status, $begindate, $enddate;
		$obj = new dclDB;

		$objView = CreateObject('dcl.boView');
		$objView->table = 'tickets';
		$objView->style = 'report';
		$objView->title = STR_TCK_STATSEARCHRESULTS;
		$objView->AddDef('columns', '', array('ticketid', 'responsible.short', 'products.name', 'dcl_org.name', 'statuses.name', 'contact', 'contactphone'));
		$objView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'ticketid'));

		if ($begindate != '' || $enddate != '')
		{
			$objView->AddDef('filter', 'statuses.dcl_status_type', '2');
			$objView->AddDef('filterdate', 'closedon', array($begindate, $enddate));
		}
		else if ($status > 0)
				$objView->AddDef('filter', 'status', $status);
			else
				$objView->AddDef('filternot', 'statuses.dcl_status_type', '2');

		if ($responsible > 0)
			$objView->AddDef('filter', 'responsible', $responsible);

		if ($product > 0)
			$objView->AddDef('filter', 'product', $product);

		$obj = CreateViewObject($objView->table);
		$obj->Render($objView);
	}
}
?>
