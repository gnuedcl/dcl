<?php
/*
 * $Id: class.boGraph.inc.php,v 1.1.1.1 2006/11/27 05:30:43 mdean Exp $
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

class boGraph
{
	var $title;
	var $caption_x;
	var $caption_y;
	var $lines_x;
	var $lines_y;
	var $line_captions_x;
	var $data;
	var $colors;
	var $color_legend;
	var $graph_width;
	var $graph_height;
	var $margin_top;
	var $margin_left;
	var $margin_bottom;
	var $margin_right;
	var $obj;
	
	function boGraph()
	{
		$this->title = '';
		$this->caption_x = '';
		$this->caption_y = '';
		$this->num_lines_x = 7;
		$this->num_lines_y = 20;
		$this->line_captions_x = array();
		$this->data = array();
		$this->colors = array();
		$this->color_legend = array();
		$this->graph_width = 500;
		$this->graph_height = 400;
		$this->margin_top = 20;
		$this->margin_left = 40;
		$this->margin_bottom = 40;
		$this->margin_right = 20;
		$this->obj = CreateObject('dcl.htmlGD');
	}
	
	function Open()
	{
		print('<script language="JavaScript">');
		print('window.open(\'main.php?menuAction=boGraph.Show&');
		if (ereg('MSIE', $GLOBALS['HTTP_USER_AGENT']))
			print('DCLINFO=' . $GLOBALS['DCLINFO'] . '&');
		print($this->ToURL() . '\', \'graph\', \'width=' . ($this->graph_width + 20) . ',height=' . ($this->graph_height + 20) . ',resizable=yes,scrollbars=yes\');');
		print('</script>');
	}
	
	function Show()
	{
		$this->FromURL();
		$this->Render();
	}
	
	function FromURL()
	{
		$this->title = $_REQUEST['title'];
		$this->caption_x = $_REQUEST['caption_x'];
		$this->caption_y = $_REQUEST['caption_y'];
		$this->num_lines_x = $_REQUEST['num_lines_x'];
		$this->num_lines_y = $_REQUEST['num_lines_y'];
		$this->line_captions_x = explode(',', $_REQUEST['line_captions_x']);
		
		$dataURL = explode('~', $_REQUEST['data']);
		$this->data = array();
		foreach ($dataURL as $line)
			$this->data[] = explode(',', $line);
		
		$this->colors = explode(',', $_REQUEST['colors']);
		$this->color_legend = explode(',', $_REQUEST['color_legend']);
		$this->graph_width = $_REQUEST['graph_width'];
		$this->graph_height = $_REQUEST['graph_height'];
		$this->margin_top = $_REQUEST['margin_top'];
		$this->margin_left = $_REQUEST['margin_left'];
		$this->margin_bottom = $_REQUEST['margin_bottom'];
		$this->margin_right = $_REQUEST['margin_right'];
	}
	
	function ToURL()
	{
		$url = 'title=' . rawurlencode($this->title) . '&';
		$url .= 'caption_x=' . rawurlencode($this->caption_x) . '&';
		$url .= 'caption_y=' . rawurlencode($this->caption_y) . '&';
		$url .= 'num_lines_x=' . $this->num_lines_x . '&';
		$url .= 'num_lines_y=' . $this->num_lines_y . '&';
		$url .= 'line_captions_x=' . rawurlencode(implode(',', $this->line_captions_x)) . '&';
		reset($this->data);
		$dataURL = '';
		while(list($junk, $line) = each($this->data))
		{
			if ($dataURL != '')
				$dataURL .= '~';
			$dataURL .= implode(',', $line);
		}
		$url .= 'data=' . $dataURL . '&';
		$url .= 'colors=' . implode(',', $this->colors) . '&';
		$url .= 'color_legend=' . rawurlencode(implode(',', $this->color_legend)) . '&';
		$url .= 'graph_width=' . $this->graph_width . '&';
		$url .= 'graph_height=' . $this->graph_height . '&';
		$url .= 'margin_top=' . $this->margin_top . '&';
		$url .= 'margin_left=' . $this->margin_left . '&';
		$url .= 'margin_bottom=' . $this->margin_bottom . '&';
		$url .= 'margin_right=' . $this->margin_right;
		
		return $url;
	}
	
	function Render()
	{
		// Initialize image - map white since it's our background
		$this->obj->width = $this->graph_width;
		$this->obj->height = $this->graph_height;
		$this->obj->Init();
		$this->obj->SetColor(255, 255, 255);
		
		// Draw the captions
		$this->obj->SetFont(2);
		$this->obj->SetColor(0, 0, 0);
		$this->obj->MoveTo($this->graph_width / 2, 2);
		$this->obj->DrawText($this->title, '', 'center');
		$this->obj->MoveTo(2, $this->graph_height / 2);
		$this->obj->DrawText($this->caption_y, 'up', 'center');
		$this->obj->MoveTo($this->graph_width / 2, $this->graph_height - $this->obj->GetFontHeight() - 2);
		$this->obj->DrawText($this->caption_x, '', 'center');
		
		// Draw the two axis
		$this->obj->Line($this->margin_left, $this->margin_top, $this->margin_left, $this->graph_height - $this->margin_bottom + 4);
		$this->obj->Line($this->margin_left - 4, $this->graph_height - $this->margin_bottom, $this->graph_width - $this->margin_right, $this->graph_height - $this->margin_bottom);
		
		// Draw dashed lines for x axis
		$linespace = ($this->graph_width - $this->margin_left - $this->margin_right) / ($this->num_lines_x - 1);
		for ($i = 1; $i < $this->num_lines_x; $i++)
		{
			$x = $i * $linespace + $this->margin_left;
			$this->obj->SetColor(0, 0, 0);
			$this->obj->Line($x, $this->graph_height - $this->margin_bottom - 4, $x, $this->graph_height - $this->margin_bottom + 4);
			$this->obj->SetColor(200, 200, 200);
			$this->obj->Line($x, $this->margin_top, $x, $this->graph_height - $this->margin_bottom - 4, 'dashed');
		}
		
		// Draw dashed lines for y axis
		$linespace = ($this->graph_height - $this->margin_top - $this->margin_bottom) / ($this->num_lines_y - 1);
		for ($i = 1; $i < $this->num_lines_y; $i++)
		{
			$y = $this->graph_height - $this->margin_bottom - ($i * $linespace);
			$this->obj->SetColor(0, 0, 0);
			$this->obj->Line($this->margin_left - 4, $y, $this->margin_left + 4, $y);
			$this->obj->SetColor(200, 200, 200);
			$this->obj->Line($this->margin_left + 4, $y, $this->graph_width - $this->margin_right, $y, 'dashed');
		}
		
		// Find the largest numeric value in data (an array of arrays representing data)
		$largest = 0;
		reset($this->data);
		while (list($junk, $line) = each($this->data))
		{
			reset($line);
			while (list($junk2, $value) = each($line))
			{
				if ($value > $largest)
					$largest = $value;
			}
		}
		
		while ($largest < ($this->num_lines_y - 1))
			$largest = ($this->num_lines_y - 1);
		
		$spread = ceil($largest / ($this->num_lines_y - 1));
		$largest = $spread * ($this->num_lines_y - 1);
		
		// Draw the x axis text
		$this->obj->SetColor(0, 0, 0);
		$this->obj->SetFont(1);
		$linespace = ($this->graph_width - $this->margin_left - $this->margin_right) / ($this->num_lines_x - 1);
		reset($this->line_captions_x);
		$i = 0;
		while (list($junk, $text) = each($this->line_captions_x))
		{
			$this->obj->MoveTo($i * $linespace + $this->margin_left, $this->graph_height - $this->margin_bottom + 8);
			$this->obj->DrawText($text, '', 'right');
			$i++;
		}
		
		// Draw the y axis text
		$linespace = ($this->graph_height - $this->margin_top - $this->margin_bottom) / ($this->num_lines_y - 1);
		for ($i = 0; $i < $this->num_lines_y; $i++)
		{
			$y = $this->graph_height - $this->margin_bottom - ($i * $linespace);
			$this->obj->MoveTo($this->margin_left - 6, $y);
			$this->obj->DrawText($i * $spread, '', 'right');
		}
		
		// Draw the lines for the data
		$this->obj->SetColor(255, 0, 0);
		$linespace = ($this->graph_width - $this->margin_left - $this->margin_right) / ($this->num_lines_x - 1);
		reset($this->data);
		$color_index = 0;
		while (list($junk, $line) = each($this->data))
		{
			$this->obj->SetColorByName($this->colors[$color_index]);
			reset($line);
			$i = 0;
			while (list($junk2, $value) = each($line))
			{
				$y = $this->graph_height - $this->margin_bottom - (($value / $largest) * ($this->graph_height - $this->margin_bottom - $this->margin_top));
				if ($i == 0)
					$this->obj->MoveTo($this->margin_left, $y);
				else
					$this->obj->LineTo($i * $linespace + $this->margin_left, $y);
				
				$i++;
			}
			
			$color_index++;
		}
		
		$this->obj->ToBrowser();
		$this->obj->Done();
	}
}
?>
