<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2015 Free Software Foundation
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

class MenuModel
{
    public $DclId;
    public $DclName;
    public $LinkHome;
    public $LinkPreferences;
    public $LinkLogoff;

    public $TextWorkOrders;
    public $TextTickets;
    public $TextProjects;
    public $TextHome;
    public $TextPreferences;

    public $TextLogoff;
    public $CanViewWorkOrders;
    public $CanViewTickets;
    public $CanViewProjects;
    public $CanModifyPreferences;

    public $CanViewWorkspaces;
    public $CanViewHotlists;
    public $Workspace;

    public function __construct()
    {
        global $g_oSession, $g_oSec;

        $this->LinkLogoff = menuLink('logout.php');
        $this->DclId = DCLID;
        $this->DclName = $g_oSession->Value('DCLNAME');

        $this->LinkHome = menuLink('', 'menuAction=HomePage.Index');

        $this->LinkPreferences = menuLink('', 'menuAction=htmlPreferences.modify');
        $this->TextWorkOrders = DCL_MENU_WORKORDERS;
        $this->TextTickets = DCL_MENU_TICKETS;
        $this->TextProjects = DCL_MENU_PROJECTS;
        $this->TextHome = DCL_MENU_HOME;
        $this->TextPreferences = DCL_MENU_PREFERENCES;
        $this->TextLogoff = DCL_MENU_LOGOFF;

        $this->CanViewWorkOrders = $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH) || $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW);
        $this->CanViewTickets = $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH) || $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEW);
        $this->CanViewProjects = $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_SEARCH) || $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW);
        $this->CanModifyPreferences = $g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_MODIFY);
        $this->CanViewWorkspaces = $g_oSec->HasPerm(DCL_ENTITY_WORKSPACE, DCL_PERM_VIEW);
        $this->CanViewHotlists = $g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_VIEW);

        $this->Workspace = $g_oSession->Value('workspace');
    }
}