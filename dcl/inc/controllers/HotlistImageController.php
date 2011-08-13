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

class HotlistImageController
{
	public function StatusChart()
	{
		$hotlistId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new HotlistImagePresenter();
		$presenter->StatusChart($hotlistId);
	}
	
	public function DepartmentChart()
	{
		$hotlistId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new HotlistImagePresenter();
		$presenter->DepartmentChart($hotlistId);
	}
	
	public function SeverityChart()
	{
		$hotlistId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new HotlistImagePresenter();
		$presenter->SeverityChart($hotlistId);
	}
	
	public function PriorityChart()
	{
		$hotlistId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new HotlistImagePresenter();
		$presenter->PriorityChart($hotlistId);
	}
	
	public function ModuleChart()
	{
		$hotlistId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new HotlistImagePresenter();
		$presenter->ModuleChart($hotlistId);
	}
	
	public function TypeChart()
	{
		$hotlistId = @Filter::RequireInt($_REQUEST['id']);
		
		$presenter = new HotlistImagePresenter();
		$presenter->TypeChart($hotlistId);
	}
}
