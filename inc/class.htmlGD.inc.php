<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
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

class htmlGD
{
	var $filename;
	var $type;
	var $cur_x;
	var $cur_y;
	var $width;
	var $height;
	var $hImage;
	var $colormap;
	var $hColor;
	var $font;

	function htmlGD()
	{
		global $dcl_info;

		$this->cur_x = 0;
		$this->cur_y = 0;
		$this->width = 0;
		$this->height = 0;
		$this->hImage = 0;
		$this->colormap = array();
		$this->hColor = 0;
		$this->font = 0;
		$this->type = $dcl_info['DCL_GD_TYPE'];
	}

	function Init()
	{
		$this->hImage = ImageCreate($this->width, $this->height) or die (STR_CMMN_INITGDERR);
		return true;
	}

	function Done()
	{
		ImageDestroy($this->hImage);
	}

	function MoveTo($x, $y)
	{
		if ($x >= 0 && $x <= $this->width && $y >= 0 && $y <= $this->height)
		{
			$this->cur_x = $x;
			$this->cur_y = $y;

			return true;
		}

		return false;
	}

	function LineTo($x, $y, $linestyle = 'solid')
	{
		if ($x >= 0 && $x <= $this->width && $y >= 0 && $y <= $this->height)
		{
			if ($linestyle == 'dashed')
				ImageDashedLine($this->hImage, $this->cur_x, $this->cur_y, $x, $y, $this->hColor);
			else
				ImageLine($this->hImage, $this->cur_x, $this->cur_y, $x, $y, $this->hColor);

			$this->cur_x = $x;
			$this->cur_y = $y;

			return true;
		}

		return false;
	}

	function Line($x1, $y1, $x2, $y2, $linestyle = 'solid')
	{
		if ($x1 >= 0 && $x1 <= $this->width && $y1 >= 0 && $y1 <= $this->height && $x2 >= 0 && $x2 <= $this->width && $y2 >= 0 && $y2 <= $this->height)
		{
			if ($linestyle == 'solid')
				ImageLine($this->hImage, $x1, $y1, $x2, $y2, $this->hColor);
			else
				ImageDashedLine($this->hImage, $x1, $y1, $x2, $y2, $this->hColor);

			$this->cur_x = $x2;
			$this->cur_y = $y2;

			return true;
		}

		return false;
	}

	function SetColor($r, $g, $b)
	{
		$key = "$r,$g,$b";
		if (!IsSet($this->colormap[$key]))
		{
			$this->hColor = ImageColorAllocate($this->hImage, $r, $g, $b);
			$this->colormap[$key] = $this->hColor;
		}
		else
		{
			$this->hColor = $this->colormap[$key];
		}

		return true;
	}

	function SetColorByName($name)
	{
		$r = 0;
		$g = 0;
		$b = 0;
		switch ($name)
		{
			case 'red':
				$r = 180;
				break;
			case 'green':
				$g = 180;
				break;
			case 'blue':
				$b = 180;
				break;
			case 'bright red':
				$r = 255;
				break;
			case 'bright green':
				$g = 255;
				break;
			case 'bright blue':
				$b = 255;
				break;
			case 'dark red':
				$r = 80;
				break;
			case 'dark green':
				$g = 80;
				break;
			case 'dark blue':
				$b = 80;
				break;
		}

		return $this->SetColor($r, $g, $b);
	}

	function SetFont($font)
	{
		if ($font < 1 || $font > 5)
			return false;

		$this->font = $font;

		return true;
	}

	function GetFontHeight()
	{
		return ImageFontHeight($this->font);
	}

	function GetFontWidth()
	{
		return ImageFontWidth($this->font);
	}

	function DrawText($text, $direction = '', $justification = 'left')
	{
		$textwidth = ImageFontWidth($this->font) * strlen($text);
		if ($justification == 'center')
		{
			if ($direction == 'up')
			{
				$this->cur_y += $textwidth / 2;
				if ($this->cur_y > $this->height)
					$this->cur_y = $this->height;
			}
			else
			{
				$this->cur_x -= $textwidth / 2;
				if ($this->cur_x < 0)
					$this->cur_x = 0;
			}
		}
		else if ($justification == 'right')
			{
				if ($direction == 'up')
				{
					$this->cur_y += $textwidth;
					if ($this->cur_y > $this->height)
						$this->cur_y = $this->height;
				}
				else
				{
					$this->cur_x -= $textwidth;
					if ($this->cur_x < 0)
						$this->cur_x = 0;
				}
			}

		if ($direction == 'up')
			ImageStringUp($this->hImage, $this->font, $this->cur_x, $this->cur_y, $text, $this->hColor);
		else
			ImageString($this->hImage, $this->font, $this->cur_x, $this->cur_y, $text, $this->hColor);

		return true;
	}

	function ToBrowser()
	{
		header('Content-type: image/' . $this->type);
		switch ($this->type)
		{
			case 'png':
				ImagePNG($this->hImage);
				break;
			case 'gif':
				ImageGIF($this->hImage);
				break;
			case 'jpeg':
				ImageJPEG($this->hImage);
				break;
		}

		exit;
	}
}
?>
