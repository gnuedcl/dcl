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

class OutageTypeValidationHelper
{
	/**
	 * @var Valitron\Validator
	 */
	private $validator;

	public function __construct(array $values)
	{
		$this->validator = new Valitron\Validator($values);
		$this->validator->rule('required', 'is_down');
		$this->validator->rule('required', 'is_infrastructure');
		$this->validator->rule('required', 'is_planned');
		$this->validator->rule('required', 'outage_type_name');

		$this->validator->rule('lengthMax', 'is_down', 1);
		$this->validator->rule('lengthMax', 'is_infrastructure', 1);
		$this->validator->rule('lengthMax', 'is_planned', 1);
		$this->validator->rule('lengthMax', 'outage_type_name', 64);
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