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

class LineGraphImageHelper
{
	public $title;
	public $caption_x;
	public $caption_y;
	public $lines_x;
	public $lines_y;
	public $line_captions_x;
	public $data;
	public $colors;
	public $color_legend;
	public $graph_width;
	public $graph_height;
	public $margin_top;
	public $margin_left;
	public $margin_bottom;
	public $margin_right;
	
	public function __construct()
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
	}
	
	public function Show()
	{
		$this->FromURL();
		$this->Render();
	}
	
	public function FromURL()
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
			$this->data[] = Filter::ToIntArray($line);
		
		$this->colors = explode(',', $_REQUEST['colors']);
		$this->color_legend = explode(',', $_REQUEST['color_legend']);
		$this->graph_width = $_REQUEST['graph_width'];
		$this->graph_height = $_REQUEST['graph_height'];
		$this->margin_top = $_REQUEST['margin_top'];
		$this->margin_left = $_REQUEST['margin_left'];
		$this->margin_bottom = $_REQUEST['margin_bottom'];
		$this->margin_right = $_REQUEST['margin_right'];
	}
	
	public function ToURL()
	{
		$url = 'title=' . rawurlencode($this->title) . '&';
		$url .= 'caption_x=' . rawurlencode($this->caption_x) . '&';
		$url .= 'caption_y=' . rawurlencode($this->caption_y) . '&';
		$url .= 'num_lines_x=' . $this->num_lines_x . '&';
		$url .= 'num_lines_y=' . $this->num_lines_y . '&';
		$url .= 'line_captions_x=' . rawurlencode(implode(',', $this->line_captions_x)) . '&';

		$dataURL = '';
		foreach ($this->data as $line)
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
	
	public function Render()
	{
		$oChart = new ChartHelper($this->graph_width, $this->graph_height);
		
		$oChart->Data->AddPoint($this->data[0], 'Serie1');
		$oChart->Data->AddPoint($this->data[1], 'Serie2');
		$oChart->Data->AddPoint($this->line_captions_x, 'Serie3');
		$oChart->Data->AddAllSeries();
		$oChart->Data->SetAbsciseLabelSerie('Serie3');
		$oChart->Data->SetSerieName('Opened', 'Serie1');
		$oChart->Data->SetSerieName('Closed', 'Serie2');
		$oChart->Data->SetYAxisName($this->caption_y);
		$oChart->Data->SetXAxisName($this->caption_x);
		
		$oChart->Chart->setGraphArea($this->margin_left, $this->margin_top, $this->graph_width - $this->margin_right, $this->graph_height - $this->margin_bottom);
		$oChart->Chart->drawGraphArea(255, 255, 255, true);

		$data = $oChart->Data->GetData();
		$hasValues = false;
		foreach ($data as $serie)
		{
			if ($serie['Serie1'] > 0 || $serie['Serie2'] > 0)
			{
				$hasValues = true;
				break;
			}
		}

		if (!$hasValues)
			$oChart->Chart->setFixedScale(0, 100);
		
		$oChart->Chart->drawScale($data, $oChart->Data->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, true, 0, 2);
		$oChart->Chart->drawGrid(4, true, 230, 230, 230, 50);
		
		$oChart->Data->removeSerie('Serie3');

		$oChart->Chart->drawLineGraph($data, $oChart->Data->GetDataDescription());
		$oChart->Chart->drawPlotGraph($data, $oChart->Data->GetDataDescription(), 3, 2, 255, 255, 255);
		
		$oChart->Chart->drawLegend(5, 35, $oChart->Data->GetDataDescription(), 255, 255, 255);
		$oChart->Chart->drawTitle($this->margin_left, $this->margin_top - 4, $this->title, 50, 50, 50, $this->graph_width - $this->margin_right);
		$oChart->Chart->Stroke();
		exit;
	}
}
