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

class OrganizationImageController
{
	public function WorkOrderOrganizationChart()
	{
		$presenter = new OrganizationImagePresenter();
		$presenter->WorkOrderOrganizationChart();
	}
	
	public function WorkOrderStatusChart()
	{
		$orgId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new OrganizationImagePresenter();
		$presenter->WorkOrderStatusChart($orgId);
	}
	
	public function WorkOrderDepartmentChart()
	{
		$orgId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new OrganizationImagePresenter();
		$presenter->WorkOrderDepartmentChart($orgId);
	}
	
	public function WorkOrderSeverityChart()
	{
		$orgId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new OrganizationImagePresenter();
		$presenter->WorkOrderSeverityChart($orgId);
	}
	
	public function WorkOrderPriorityChart()
	{
		$orgId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new OrganizationImagePresenter();
		$presenter->WorkOrderPriorityChart($orgId);
	}
	
	public function WorkOrderModuleChart()
	{
		$orgId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new OrganizationImagePresenter();
		$presenter->WorkOrderModuleChart($orgId);
	}
	
	public function WorkOrderTypeChart()
	{
		$orgId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new OrganizationImagePresenter();
		$presenter->WorkOrderTypeChart($orgId);
	}
	
	public function TicketOrganizationChart()
	{
		$presenter = new OrganizationImagePresenter();
		$presenter->TicketOrganizationChart();
	}
	
	public function TicketStatusChart()
	{
		$orgId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new OrganizationImagePresenter();
		$presenter->TicketStatusChart($orgId);
	}
	
	public function TicketModuleChart()
	{
		$orgId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new OrganizationImagePresenter();
		$presenter->TicketModuleChart($orgId);
	}
	
	public function TicketTypeChart()
	{
		$orgId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new OrganizationImagePresenter();
		$presenter->TicketTypeChart($orgId);
	}
	
	public function TicketPriorityChart()
	{
		$orgId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new OrganizationImagePresenter();
		$presenter->TicketPriorityChart($orgId);
	}
}