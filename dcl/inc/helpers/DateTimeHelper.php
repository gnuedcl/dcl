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

class TimestampHelper
{
	var $time;
	var $dbFormat;
	var $dbFormatEx;

	public function __construct()
	{
		global $dcl_info;

		$this->time = 0;
		$this->SetDBFormat($dcl_info['DCL_TIMESTAMP_FORMAT_DB']);
	}

	public function SetDBFormat($fmtString)
	{
		$this->dbFormat = $fmtString;
		$this->dbFormatEx = str_replace('m', 'MM', $this->dbFormat);
		$this->dbFormatEx = str_replace('d', 'DD', $this->dbFormatEx);
		$this->dbFormatEx = str_replace('Y', 'YYYY', $this->dbFormatEx);
		$this->dbFormatEx = str_replace('H', 'HH', $this->dbFormatEx);
		$this->dbFormatEx = str_replace('i', 'II', $this->dbFormatEx);
		$this->dbFormatEx = str_replace('s', 'SS', $this->dbFormatEx);
	}

	// Returns the database representation of the timestamp
	public function ToDB() 
	{
		if ($this->time > 0)
			return date($this->dbFormat, $this->time);
			
		return null;
	}

	// Returns the display representation of the timestamp
	public function ToDisplay() 
	{
		global $dcl_info;

		if ($this->time > 0)
			return date($dcl_info['DCL_TIMESTAMP_FORMAT'], $this->time);
			
		return null;
	}
	
	public function ToTimeOnly()
	{
		if ($this->time > 0)
			return date('H:i', $this->time);
			
		return null;
	}

	// return current timestamp in ANSI format
	public function ToANSI()
	{
		if ($this->time > 0)
			return date('Y-m-d H:i:s', $this->time);
			
		return null;
	}

	// set current timestamp from ANSI formatted string
	public function SetFromANSI($s)
	{
		$sANSI = 'YYYY-MM-DD HH:II:SS';
		$this->time = mktime(
				substr($s, strpos($sANSI, 'H'), 2),	// hour
				substr($s, strpos($sANSI, 'I'), 2),	// minute
				substr($s, strpos($sANSI, 'S'), 2),	// second
				substr($s, strpos($sANSI, 'M'), 2),	// month
				substr($s, strpos($sANSI, 'D'), 2),	// day
				substr($s, strpos($sANSI, 'Y'), 4));	// year
	}

	// Returns the timestamp as UNIX time
	public function ToInt() 
	{
		return $this->time;
	}

	//sets the timestamp from database string
	public function SetFromDB($s) 
	{
		$this->time = mktime(
				substr($s, strpos($this->dbFormatEx, 'H'), 2),	// hour
				substr($s, strpos($this->dbFormatEx, 'I'), 2),	// minute
				substr($s, strpos($this->dbFormatEx, 'S'), 2),	// second
				substr($s, strpos($this->dbFormatEx, 'M'), 2),	// month
				substr($s, strpos($this->dbFormatEx, 'D'), 2),	// day
				substr($s, strpos($this->dbFormatEx, 'Y'), 4));	// year
	}

	// sets the timestamp from display/web string
	// Date order is based on the date format string,
	// single separators (any character goes!) are ignored
	public function SetFromDisplay($s) 
	{
		global $dcl_info;

		// Create regex string for date based on DCL_DATE_FORMAT
		$regexStr = str_replace('m', '([0-9]{2})', $dcl_info['DCL_DATE_FORMAT']);
		$regexStr = str_replace('d', '([0-9]{2})', $regexStr);
		$regexStr = str_replace('Y', '([0-9]{4})', $regexStr);
		// Check for full timestamp
		if(preg_match('#^' . $regexStr . ' ([0-9]{2}).([0-9]{2}).([0-9]{2})\.{0,1}[0-9]*$#', $s, $dateParts))
		{
			// Got full timestamp
			// Processing will be performed
		}
		else if(preg_match('#^' . $regexStr . '$#', $s, $dateParts))
		{
			// Got just a date
			// Initialize time values to zeroes
			$dateParts[6] = $dateParts[5] = $dateParts[4] = 0;
		}
		else 
		{
			// Date will be unchanged if the parsing didn't succeed
			return;
		}

		// Parse input string based on format string
		$configFmt = $dcl_info['DCL_DATE_FORMAT'];
		for($i = 0, $j = 1; $i < strlen($configFmt); $i++) 
		{
			switch($configFmt[$i]) 
			{
				case 'Y':
					$year = $dateParts[$j];
					$j++;
					break;
				case 'm':
					$month = $dateParts[$j];
					$j++;
					break;
				case 'd':
					$day = $dateParts[$j];
					$j++;
					break;
				default:
					break;
			}
		}

		$this->time = mktime($dateParts[4], $dateParts[5], $dateParts[6], $month, $day, $year);
	}

	// sets the timestamp from UNIX time
	public function SetFromInt($timestamp) 
	{
		$this->time = $timestamp;
	}
}

class DateHelper extends TimestampHelper 
{
	public function __construct()
	{
		global $dcl_info;

		$this->time = 0;
		$this->SetDBFormat($dcl_info['DCL_DATE_FORMAT_DB']);
	}

	public function ToDisplay() 
	{
		global $dcl_info;
		
		if ($this->time > 0)
			return date($dcl_info['DCL_DATE_FORMAT'], $this->time);
			
		return null;
	}

	public function ToDB() 
	{
		if ($this->time > 0)
			return date($this->dbFormat, $this->time);
			
		return null;
	}

	// return current timestamp in ANSI format
	public function ToANSI()
	{
		if ($this->time > 0)
			return date('Y-m-d', $this->time);
			
		return null;
	}

	// set current timestamp from ANSI formatted string
	public function SetFromANSI($s)
	{
		$sANSI = 'YYYY-MM-DD';
		$this->time = mktime(
				0,	// hour
				0,	// minute
				0,	// second
				substr($s, strpos($this->dbFormatEx, 'M'), 2),	// month
				substr($s, strpos($this->dbFormatEx, 'D'), 2),	// day
				substr($s, strpos($this->dbFormatEx, 'Y'), 4));	// year
	}

	public function SetFromDB($s) 
	{
		$this->time = mktime(
				0, 
				0, 
				0, 
				substr($s, strpos($this->dbFormatEx, 'M'), 2), 
				substr($s, strpos($this->dbFormatEx, 'D'), 2), 
				substr($s, strpos($this->dbFormatEx, 'Y'), 4));
	}
}
