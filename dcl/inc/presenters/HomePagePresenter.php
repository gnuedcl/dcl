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

class HomePagePresenter
{
    public function Index()
    {
        $t = new SmartyHelper();
        $t->assign('PERM_OUTAGES', HasPermission(DCL_ENTITY_OUTAGE, DCL_PERM_VIEW));
        $t->assign('PERM_TICKETS', HasAnyPermission(DCL_ENTITY_TICKET, array(DCL_PERM_VIEW, DCL_PERM_VIEWACCOUNT, DCL_PERM_VIEWSUBMITTED)));
        $t->assign('PERM_WORKORDERS', HasAnyPermission(DCL_ENTITY_WORKORDER, array(DCL_PERM_VIEW, DCL_PERM_VIEWACCOUNT, DCL_PERM_VIEWSUBMITTED)));
        $t->assign('PERM_FAQ', HasPermission(DCL_ENTITY_FAQ, DCL_PERM_VIEW));
        $t->assign('PERM_PROJECTS', HasPermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW));
        $t->assign('VAL_USERID', DCLID);
        $t->Render('HomePage.tpl');
    }
}