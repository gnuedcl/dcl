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

class ProductImageController
{
	public function WorkOrderProductChart()
	{
		$presenter = new ProductImagePresenter();
		$presenter->WorkOrderProductChart();
	}
	
	public function TicketProductChart()
	{
		$presenter = new ProductImagePresenter();
		$presenter->TicketProductChart();
	}
	
	public function WorkOrderStatusChart()
	{
		$productId = @Filter::RequireInt($_REQUEST['id']);

		$presenter = new ProductImagePresenter();
		$presenter->WorkOrderStatusChart($productId);
	}
	
	public function WorkOrderDepartmentChart()
	{
		$productId = @Filter::RequireInt($_REQUEST['id']);

		$presenter = new ProductImagePresenter();
		$presenter->WorkOrderDepartmentChart($productId);
	}
	
	public function WorkOrderSeverityChart()
	{
		$productId = @Filter::RequireInt($_REQUEST['id']);

		$presenter = new ProductImagePresenter();
		$presenter->WorkOrderSeverityChart($productId);
	}
	
	public function WorkOrderPriorityChart()
	{
		$productId = @Filter::RequireInt($_REQUEST['id']);

		$presenter = new ProductImagePresenter();
		$presenter->WorkOrderPriorityChart($productId);
	}
	
	public function WorkOrderModuleChart()
	{
		$productId = @Filter::RequireInt($_REQUEST['id']);

		$presenter = new ProductImagePresenter();
		$presenter->WorkOrderModuleChart($productId);
	}
	
	public function WorkOrderTypeChart()
	{
		$productId = @Filter::RequireInt($_REQUEST['id']);

		$presenter = new ProductImagePresenter();
		$presenter->WorkOrderTypeChart($productId);
	}
	
	public function TicketStatusChart()
	{
		$productId = @Filter::RequireInt($_REQUEST['id']);

		$presenter = new ProductImagePresenter();
		$presenter->TicketStatusChart($productId);
	}
	
	public function TicketTypeChart()
	{
		$productId = @Filter::RequireInt($_REQUEST['id']);

		$presenter = new ProductImagePresenter();
		$presenter->TicketTypeChart($productId);
	}
	
	public function TicketPriorityChart()
	{
		$productId = @Filter::RequireInt($_REQUEST['id']);

		$presenter = new ProductImagePresenter();
		$presenter->TicketPriorityChart($productId);
	}
	
	public function TicketModuleChart()
	{
		$productId = @Filter::RequireInt($_REQUEST['id']);

		$presenter = new ProductImagePresenter();
		$presenter->TicketModuleChart($productId);
	}
}
