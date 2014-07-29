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

class SearchTerm
{
	public $Include;
	public $Exclude;

	public function __construct()
	{
		$this->Include = array();
		$this->MustHave = array();
		$this->Exclude = array();
	}

	public function Parse($term)
	{
		if (!Filter::IsNotNullOrWhitespace($term))
			return '';

		$term = trim($term);

		$token = $this->NextToken(' ', $term);
		while ($token)
		{
			$isNot = mb_substr($token, 0, 1) == '-';
			$mustHave = !$isNot && mb_substr($token, 0, 1) == '+';
			if ($isNot || $mustHave)
				$token = mb_substr($token, 1);

			if (mb_substr($token, 0, 1) == '"')
				$token = mb_substr($token, 1) . ' ' . $this->NextToken('"');

			if ($isNot)
				$this->Exclude[] = $token;
			else if ($mustHave)
				$this->MustHave[] = $token;
			else
				$this->Include[] = $token;

			$token = $this->NextToken(' ');
		}
	}

	private function NextToken($delim, $value = null)
	{
		static $idx = 0;
		static $tokenString = '';

		if ($value != null)
		{
			$idx = 0;
			$tokenString = $value;
		}

		$token = '';
		while ($idx < mb_strlen($tokenString))
		{
			$ch = mb_substr($tokenString, $idx++, 1);
			if (mb_strpos($delim, $ch) === false)
			{
				$token .= $ch;
			}
			else
			{
				if ($token != '')
					return $token;
			}
		}

		if ($token != '')
			return $token;

		return false;
	}
}