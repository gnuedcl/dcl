<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

class WoRubricScoreModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_wo_rubric_score';
		LoadSchema($this->TableName);
		parent::Clear();
	}

	public function DeleteInvalidScores(WorkOrderModel $model)
	{
		$woTypeId = $model->wo_type_id;
		$productId = $model->product;

		$sql = 'DELETE FROM ' . $this->TableName . " WHERE rubric_criteria_id NOT IN (SELECT rubric_criteria_id FROM dcl_rubric_criteria WHERE rubric_id IN (SELECT rubric_id FROM dcl_product_rubric WHERE product_id = $productId AND wo_type_id = $woTypeId))";
		return $this->Execute($sql);
	}

	public function ListByWorkOrder(WorkOrderModel $model)
	{
		$id = $model->jcn;
		$seq = $model->seq;
		$productId = $model->product;
		$woTypeId = $model->wo_type_id;

		$sql = "SELECT RC.rubric_criteria_id, WR.score FROM dcl_rubric_criteria AS RC LEFT JOIN dcl_wo_rubric_score AS WR ON RC.rubric_criteria_id = WR.rubric_criteria_id AND WR.wo_id = $id AND WR.seq = $seq WHERE RC.rubric_id = (SELECT rubric_id FROM dcl_product_rubric WHERE product_id = $productId AND wo_type_id = $woTypeId)";

		$retVal = array();
		if ($this->Query($sql) != -1)
		{
			while ($this->next_record())
				$retVal[$this->f(0)] = $this->f(1);
		}

		return $retVal;
	}

	public function DeleteByWorkOrder($id, $seq)
	{
		$sql = 'DELETE FROM ' . $this->TableName . " WHERE wo_id = $id AND seq = $seq";
		return $this->Execute($sql);
	}
}