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

class ProjectImageController
{
	public function StatusChart()
	{
		$projectId = @Filter::RequireInt($_REQUEST['id']);
		$children = @Filter::ToIntArray($_REQUEST['children']);
		
		$presenter = new ProjectImagePresenter();
		$presenter->StatusChart($projectId, $children);
	}
	
	public function DepartmentChart()
	{
		$projectId = @Filter::RequireInt($_REQUEST['id']);
		$children = @Filter::ToIntArray($_REQUEST['children']);

		$presenter = new ProjectImagePresenter();
		$presenter->DepartmentChart($projectId, $children);
	}
	
	public function SeverityChart()
	{
		$projectId = @Filter::RequireInt($_REQUEST['id']);
		$children = @Filter::ToIntArray($_REQUEST['children']);

		$presenter = new ProjectImagePresenter();
		$presenter->SeverityChart($projectId, $children);
	}
	
	public function PriorityChart()
	{
		$projectId = @Filter::RequireInt($_REQUEST['id']);
		$children = @Filter::ToIntArray($_REQUEST['children']);

		$presenter = new ProjectImagePresenter();
		$presenter->PriorityChart($projectId, $children);
	}
	
	public function ModuleChart()
	{
		$projectId = @Filter::RequireInt($_REQUEST['id']);
		$children = @Filter::ToIntArray($_REQUEST['children']);

		$presenter = new ProjectImagePresenter();
		$presenter->ModuleChart($projectId, $children);
	}
	
	public function TypeChart()
	{
		$projectId = @Filter::RequireInt($_REQUEST['id']);
		$children = @Filter::ToIntArray($_REQUEST['children']);

		$presenter = new ProjectImagePresenter();
		$presenter->TypeChart($projectId, $children);
	}
}
