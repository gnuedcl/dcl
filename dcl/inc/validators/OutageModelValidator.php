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

class OutageModelValidator
{
	private $validator;

	public function __construct(OutageModel $model)
	{
		$v = ModelValidatorHelper::GetValidatorForModel($model);
		$v->rule('required', array('outage_title', 'outage_description', 'outage_type_id'));
		$v->rule('lengthMax', 'outage_title', 100);

		$outageType = new OutageTypeModel();
		$found = $outageType->Load($model->outage_type_id);

		$v->addRule('validOutageType', function($field, $value, array $params) {
			$params = $params[0];

			return $params['found'] != -1;
		});

		$v->rule('validOutageType', 'outage_type_id', array('model' => $outageType, 'found' => $found))->message('Selected {field} does not exist.');

		if ($outageType->is_planned == 'Y')
		{
			$v->rule('required', array('outage_sched_start', 'outage_sched_end'));
			$v->rule('dateBefore', 'outage_sched_start', new DateTime($model->outage_sched_end))->message('{field} must be before {field1}.');
		}
		else
		{
			$v->rule('required', array('sev_level'));
			$v->rule('min', 'sev_level', 1);
			$v->rule('max', 'sev_level', 5);
		}

		if ($outageType->is_planned != 'Y' || $model->outage_end != '')
			$v->rule('required', array('outage_start'));

		if ($model->outage_start != '')
		{
			$v->rule('dateBefore', 'outage_start', new DateTime())->message('{field} cannot be in the future.');

			if ($model->outage_end != '')
			{
				$v->rule('dateBefore', 'outage_end', new DateTime())->message('{field} cannot be in the future.');
				$v->rule('dateBefore', 'outage_start', $model->outage_end)->message('{field} must be before {field1}.');
			}
		}

		$v->labels(array(
			'outage_title' => 'Title',
			'outage_description' => 'Description',
			'outage_type_id' => 'Outage Type',
			'outage_sched_start' => 'Scheduled Start Time',
			'outage_sched_end' => 'Scheduled End Time',
			'outage_start' => 'Start Time',
			'outage_end' => 'End Time',
			'sev_level' => 'Severity Level'
		));

		$this->validator = $v;
	}

	public function Validate()
	{
		return $this->validator->validate();
	}

	public function Errors($field = null)
	{
		return $this->validator->errors($field);
	}
} 