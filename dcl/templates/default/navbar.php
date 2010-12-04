<?php
	/*
	 * $Id$
	 *
	 * Double Choco Latte - Source Configuration Management System
	 * Copyright (C) 1999  Michael L. Dean & Tim R. Norman
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License
	 * as published by the Free Software Foundation; either version 2
	 * of the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
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

LoadStringResource('wo');
LoadStringResource('tck');
LoadStringResource('menu');
class DCLNavBar
{
	var $t;
	var $_class;
	var $_method;

	function __construct()
	{
		global $dcl_info;

		$this->t = new DCL_Smarty();
		if (IsSet($_REQUEST['menuAction']) && $_REQUEST['menuAction'] != 'clearScreen')
			list($this->_class, $this->_method) = explode('.', $_REQUEST['menuAction']);
	}

	function createGlobal()
	{
		global $g_oSec, $dcl_info;
		$aItems = array();

		if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD))
			$aItems[] = array(DCL_MENU_NEWWORKORDER, 'boWorkorders.newjcn', 'new-16.png');

		if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADD))
			$aItems[] = array(DCL_MENU_NEWPROJECT, 'boProjects.newproject', 'new-16.png');

		if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ADD))
			$aItems[] = array(DCL_MENU_NEWTICKET, 'boTickets.add', 'new-16.png');

		if ($dcl_info['DCL_WIKI_ENABLED'] == 'Y' && $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_VIEWWIKI))
			$aItems[] = array(DCL_MENU_MAINWIKI, 'htmlWiki.show&type=0&name=FrontPage', 'book-16.png');

		$aItems[] = array('Print', 'javascript:printer_friendly();', 'print-16.png');

		$this->t->assign('VAL_TITLE', STR_CMMN_OPTIONS);

		return $this->renderItems($aItems);
	}

	function createGroupContext()
	{
		global $g_oSec, $dcl_info;
		$aItems = array();

		if ($this->_isWorkorderGroup())
		{
			if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
				$aItems[] = array(DCL_MENU_MYWOS, 'htmlWorkorders.show&filterReportto=' . $GLOBALS['DCLID'], 'home-16.png');

			if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD))
				$aItems[] = array(DCL_MENU_NEW, 'boWorkorders.newjcn', 'new-16.png');

			if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_IMPORT))
				$aItems[] = array(DCL_MENU_IMPORT, 'boWorkorders.csvupload', 'import-16.png');

			if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REPORT))
			{
				$aItems[] = array(DCL_MENU_ACTIVITY, 'reportPersonnelActivity.getparameters', 'exec-16.png');
				$aItems[] = array(DCL_MENU_GRAPH, 'boWorkorders.graph', 'exec-16.png');
				$aItems[] = array(DCL_MENU_STATISTICS, 'htmlWOStatistics.ShowUserVsProductStatusForm', 'exec-16.png');
				$aItems[] = array(DCL_MENU_SCHEDULE, 'scheduleByPerson.SelectPersonToSchedule', 'exec-16.png');
			}

			if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
				$aItems[] = array(DCL_MENU_SEARCH, 'htmlWOSearches.Show', 'search-16.png');

			if ($g_oSec->HasAnyPerm(array(DCL_ENTITY_WORKORDER => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT)))))
				$aItems[] = array(DCL_MENU_BROWSE, 'htmlWorkorders.show', 'exec-16.png');

			$this->t->assign('VAL_TITLE', DCL_MENU_WORKORDERS);
		}
		else if ($this->_isTicketGroup())
		{
			if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION))
				$aItems[] = array(DCL_MENU_MYTICKETS, 'htmlTickets.show&filterReportto=' . $GLOBALS['DCLID'], 'home-16.png');

			if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ADD))
				$aItems[] = array(DCL_MENU_NEW, 'boTickets.add', 'new-16.png');

			if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REPORT))
			{
				$aItems[] = array(DCL_MENU_ACTIVITY, 'reportTicketActivity.getparameters', 'exec-16.png');
				$aItems[] = array(DCL_MENU_GRAPH, 'boTickets.graph', 'exec-16.png');
				$aItems[] = array(DCL_MENU_STATISTICS, 'htmlTicketStatistics.ShowUserVsProductStatusForm', 'exec-16.png');
			}

			if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
				$aItems[] = array(DCL_MENU_SEARCH, 'htmlTicketSearches.Show', 'search-16.png');

			if ($g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT)))))
				$aItems[] = array(DCL_MENU_BROWSE, 'htmlTickets.show', 'exec-16.png');

			$this->t->assign('VAL_TITLE', DCL_MENU_TICKETS);
		}
		else if ($this->_isProjectGroup())
		{
			if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
				$aItems[] = array(DCL_MENU_MYPROJECTS, 'htmlProjects.show&filterReportto=' . $GLOBALS['DCLID'], 'home-16.png');

			if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADD))
				$aItems[] = array(DCL_MENU_NEW, 'boProjects.newproject', 'new-16.png');

			if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
				$aItems[] = array(DCL_MENU_BROWSE, 'htmlProjects.show', 'exec-16.png');

			$this->t->assign('VAL_TITLE', DCL_MENU_PROJECTS);
		}
		else if ($this->_isAdminGroup())
		{
			if ($g_oSec->HasPerm(DCL_ENTITY_STATUS, DCL_PERM_ADD))
				$aItems[] = array(STR_CMMN_NEW, 'boStatuses.add', '');
	
			if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			{
				$aItems[] = array(DCL_MENU_SYSTEMSETUP, 'boAdmin.ShowSystemConfig', '');
			}
			
			$this->t->assign('VAL_TITLE', DCL_MENU_ADMIN);
		}
		else
			return;

		return $this->renderItems($aItems);
	}

	function renderItems(&$aItems)
	{
		$aLinks = array();
		$i = 0;
		foreach ($aItems as $aItem)
		{
			$aLinks[$i] = array();
			if (substr($aItem[1], 0, 11) == 'javascript:')
				$aLinks[$i]['onclick'] = $aItem[1];
			else
				$aLinks[$i]['onclick'] = menuLink('', 'menuAction=' . $aItem[1]);
			
			$aLinks[$i]['text'] = $aItem[0];
			$aLinks[$i]['image'] = $aItem[2];
			
			$i++;
		}

		$this->t->assign('VAL_NAVBOXITEMS', $aLinks);

		return $this->t->ToString('navbar.tpl');
	}

	function getHtml()
	{
		$retVal = $this->createGroupContext();
		$retVal .= $this->createGlobal();

		return $retVal;
	}

	function _isWorkorderGroup()
	{
		return ($this->_class == 'htmlWorkorders' ||
				$this->_class == 'boWorkorders' ||
				$this->_class == 'reportPersonnelActivity' ||
				$this->_class == 'htmlWOSearches' ||
				$this->_class == 'htmlWOStatistics' ||
				$this->_class == 'scheduleByPerson' ||
				$this->_class == 'boTimecards' ||
				($this->_class == 'htmlSearchBox' && $_REQUEST['which'] == 'workorders')
			);
	}

	function _isWorkorderItem()
	{
		global $menuAction;

		if (!IsSet($_REQUEST['jcn']) || $_REQUEST['jcn'] == '' || !IsSet($_REQUEST['seq']) || $_REQUEST['seq'] == '')
			return false;

		$bSearchBox = ($this->_class == 'htmlSearchBox' &&
				$_REQUEST['which'] == 'workorders' &&
				ereg('^([0-9]+)[-]([0-9]*)$', $_REQUEST['search_text'], $reg)
			);

		if ($bSearchBox)
		{
			$_REQUEST['jcn'] = $reg[1];
			$_REQUEST['seq'] = $reg[2];
		}

		return ($bSearchBox ||
				$menuAction == 'boWorkorders.viewjcn' ||
				$menuAction == 'boTimecards.add' ||
				$menuAction == 'boWorkorders.upload' ||
				$menuAction == 'boWorkorders.reassign' ||
				$menuAction == 'boWorkorders.modifyjcn'
			);
	}

	function _isAdminGroup()
	{
		return false;// (in_array($this->_class, array('boStatuses')));
	}

	function _isTicketGroup()
	{
		return ($this->_class == 'htmlTickets' ||
				$this->_class == 'boTickets' ||
				$this->_class == 'boTicketresolutions' ||
				$this->_class == 'reportTicketActivity' ||
				$this->_class == 'htmlTicketStatistics' ||
				($this->_class == 'htmlSearchBox' && $_REQUEST['which'] == 'tickets')
			);
	}

	function _isTicketItem()
	{
		global $menuAction;

		$bSearchBox = ($this->_class == 'htmlSearchBox' &&
				$_REQUEST['which'] == 'tickets' &&
				ereg('^([0-9]+)$', $_REQUEST['search_text'], $reg)
			);

		if ($bSearchBox)
			$_REQUEST['ticketid'] = $reg[1];

		return ($bSearchBox ||
				$menuAction == 'boTicketresolutions.add' ||
				$menuAction == 'boTickets.reassign' ||
				$menuAction == 'boTickets.modify' ||
				$menuAction == 'boTickets.delete' ||
				$menuAction == 'boTickets.copyToWO' ||
				$menuAction == 'boTickets.upload' ||
				$menuAction == 'boTickets.view'
			);
	}

	function _isProjectGroup()
	{
		return ($this->_class == 'htmlProjects' ||
				$this->_class == 'htmlProjectsform' ||
				$this->_class == 'htmlProjectsdetail' ||
				$this->_class == 'htmlProjectmap' ||
				$this->_class == 'boProjects' ||
				($this->_class == 'htmlSearchBox' && $_REQUEST['which'] == 'dcl_projects')
			);
	}

	function _isProjectItem()
	{
		global $menuAction;

		return ($menuAction == 'boProjects.viewproject');
	}
}
