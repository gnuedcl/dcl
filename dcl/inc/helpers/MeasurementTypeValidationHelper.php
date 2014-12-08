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

class MeasurementTypeValidationHelper
{
	/**
	 * @var Valitron\Validator
	 */
	private $validator;

	public function __construct(array $values)
	{
		$this->validator = new Valitron\Validator($values);
		$this->validator->rule('required', 'measurement_name');
		$this->validator->rule('required', 'measurement_unit_id');

		$this->validator->rule('lengthMax', 'measurement_name', 50);
		$this->validator->rule('integer', 'measurement_unit_id');

		$this->validator->labels(array(
			'measurement_name' => STR_CMMN_NAME,
			'measurement_unit_id' => 'Measurement Unit'
		));
	}

	public function IsValid()
	{
		return $this->validator->validate();
	}

	public function Errors()
	{
		return $this->validator->errors();
	}
}