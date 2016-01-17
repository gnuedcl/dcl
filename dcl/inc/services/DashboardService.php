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

class DashboardService
{
    public function GetData()
    {
        $retVal = new stdClass();
        $retVal->workorders = 0;
        $retVal->myWorkorders = 0;
        $retVal->submittedWorkorders = 0;
        $retVal->tickets = 0;
        $retVal->myTickets = 0;
        $retVal->submittedTickets = 0;
        $retVal->projects = 0;
        $retVal->myProjects = 0;

        $db = new DbProvider();
        if (HasAnyPermission(DCL_ENTITY_WORKORDER, array(DCL_PERM_VIEW, DCL_PERM_VIEWACCOUNT, DCL_PERM_VIEWSUBMITTED)))
        {
            $workOrderSqlHelper = new WorkOrderSqlQueryHelper();
            $workOrderSqlHelper->AddDef('filternot', 'statuses.dcl_status_type', '2');
            if ($db->Query($workOrderSqlHelper->GetSQL(true)) !== -1 && $db->next_record())
                $retVal->workorders = (int)$db->f(0);

            $workOrderSqlHelper->AddDef('filter', 'responsible', DCLID);
            if ($db->Query($workOrderSqlHelper->GetSQL(true)) !== -1 && $db->next_record())
                $retVal->myWorkorders = (int)$db->f(0);

            $workOrderSqlHelper->ClearDef('filter');
            $workOrderSqlHelper->AddDef('filter', 'createby', DCLID);
            if ($db->Query($workOrderSqlHelper->GetSQL(true)) !== -1 && $db->next_record())
                $retVal->submittedWorkorders = (int)$db->f(0);
        }

        if (HasAnyPermission(DCL_ENTITY_TICKET, array(DCL_PERM_VIEW, DCL_PERM_VIEWACCOUNT, DCL_PERM_VIEWSUBMITTED)))
        {
            $ticketSqlHelper = new TicketSqlQueryHelper();
            $ticketSqlHelper->AddDef('filternot', 'statuses.dcl_status_type', '2');
            if ($db->Query($ticketSqlHelper->GetSQL(true)) !== -1 && $db->next_record())
                $retVal->tickets = (int)$db->f(0);

            $ticketSqlHelper->AddDef('filter', 'responsible', DCLID);
            if ($db->Query($ticketSqlHelper->GetSQL(true)) !== -1 && $db->next_record())
                $retVal->myTickets = (int)$db->f(0);

            $ticketSqlHelper->ClearDef('filter');
            $ticketSqlHelper->AddDef('filter', 'createdby', DCLID);
            $ticketSqlHelper->AddDef('filternot', 'responsible', DCLID);
            if ($db->Query($ticketSqlHelper->GetSQL(true)) !== -1 && $db->next_record())
                $retVal->submittedTickets = (int)$db->f(0);
        }

        if (HasPermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
        {
            $projectSqlHelper = new ProjectSqlQueryHelper();
            $projectSqlHelper->AddDef('filternot', 'statuses.dcl_status_type', '2');
            if ($db->Query($projectSqlHelper->GetSQL(true)) !== -1 && $db->next_record())
                $retVal->projects = (int)$db->f(0);

            $projectSqlHelper->AddDef('filter', 'reportto', DCLID);
            if ($db->Query($projectSqlHelper->GetSQL(true)) !== -1 && $db->next_record())
                $retVal->myProjects = (int)$db->f(0);
        }

        header('Content-Type: application/json');
        echo json_encode($retVal);
        exit;
    }
}