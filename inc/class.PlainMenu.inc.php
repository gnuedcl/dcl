<?php
/*
 * $Id: class.PlainMenu.inc.php,v 1.1.1.1 2006/11/27 05:30:46 mdean Exp $
 *
 * This file is part of Double Choco Latte.
 *
 * PHP Layers Menu 3.1.1 (C) 2001-2003 Marco Pratesi (marco at telug dot it)
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

import('LayersMenuCommon');
class PlainMenu extends LayersMenuCommon
{
	var $plainMenuTpl;
	var $_plainMenu;
	var $horizontalPlainMenuTpl;
	var $_horizontalPlainMenu;
	
	function PlainMenu()
	{
		$this->LayersMenuCommon();
	
		$this->plainMenuTpl = $this->tpldir . "layersmenu-plain_menu.ihtml";
		$this->_plainMenu = array();
	
		$this->horizontalPlainMenuTpl = $this->tpldir . "layersmenu-horizontal_plain_menu.ihtml";
		$this->_horizontalPlainMenu = array();
	}
	
	function setDirroot($dirroot) {
		$oldtpldir = $this->tpldir;
		if ($foobar = $this->setDirrootCommon($dirroot)) {
			$this->updateTpldir($oldtpldir);
		}
		return $foobar;
	}
	
	function setTpldir($tpldir) {
		$oldtpldir = $this->tpldir;
		if ($foobar = $this->setTpldirCommon($tpldir)) {
			$this->updateTpldir($oldtpldir);
		}
		return $foobar;
	}
	
	function updateTpldir($oldtpldir) {
		$oldlength = strlen($oldtpldir);
		$foobar = strpos($this->plainMenuTpl, $oldtpldir);
		if (!($foobar === false || $foobar != 0)) {
			$this->plainMenuTpl = $this->tpldir . substr($this->plainMenuTpl, $oldlength);
		}
		$foobar = strpos($this->horizontalPlainMenuTpl, $oldtpldir);
		if (!($foobar === false || $foobar != 0)) {
			$this->horizontalPlainMenuTpl = $this->tpldir . substr($this->horizontalPlainMenuTpl, $oldlength);
		}
	}
	
	function setPlainMenuTpl($plainMenuTpl) {
		if (str_replace("/", "", $plainMenuTpl) == $plainMenuTpl) {
			$plainMenuTpl = $this->tpldir . $plainMenuTpl;
		}
		if (!file_exists($plainMenuTpl)) {
			$this->error("setPlainMenuTpl: file $plainMenuTpl does not exist.");
			return false;
		}
		$this->plainMenuTpl = $plainMenuTpl;
		return true;
	}
	
	function newPlainMenu(
		$menu_name = ""	// non consistent default...
		) {
		$plain_menu_blck = "";
		$t = CreateTemplate(array('tplfile' => $this->plainMenuTpl));
		$t->set_block("tplfile", "template", "template_blck");
		$t->set_block("template", "plain_menu_cell", "plain_menu_cell_blck");
		$t->set_var("plain_menu_cell_blck", "");
		for ($cnt=$this->_firstItem[$menu_name]; $cnt<=$this->_lastItem[$menu_name]; $cnt++) {
			$nbsp = "";
			for ($i=1; $i<$this->tree[$cnt]["level"]; $i++) {
				$nbsp .= "&nbsp;&nbsp;&nbsp;";
			}
			$t->set_var(array(
				"nbsp"		=> $nbsp,
				"href"		=> $this->tree[$cnt]["parsed_href"],
				"title"		=> $this->tree[$cnt]["parsed_title"],
				"target"	=> $this->tree[$cnt]["parsed_target"],
				"text"		=> $this->tree[$cnt]["parsed_text"]
			));
			$plain_menu_blck .= $t->parse("plain_menu_cell_blck", "plain_menu_cell", false);
		}
		$t->set_var("plain_menu_cell_blck", $plain_menu_blck);
		$this->_plainMenu[$menu_name] = $t->parse("template_blck", "template");
	
		return $this->_plainMenu[$menu_name];
	}
	
	function getPlainMenu($menu_name) {
		return $this->_plainMenu[$menu_name];
	}
	
	function printPlainMenu($menu_name) {
		print $this->_plainMenu[$menu_name];
	}
	
	function setHorizontalPlainMenuTpl($horizontalPlainMenuTpl) {
		if (str_replace("/", "", $horizontalPlainMenuTpl) == $horizontalPlainMenuTpl) {
			$horizontalPlainMenuTpl = $this->tpldir . $horizontalPlainMenuTpl;
		}
		if (!file_exists($horizontalPlainMenuTpl)) {
			$this->error("setHorizontalPlainMenuTpl: file $horizontalPlainMenuTpl does not exist.");
			return false;
		}
		$this->horizontalPlainMenuTpl = $horizontalPlainMenuTpl;
		return true;
	}
	
	function newHorizontalPlainMenu(
		$menu_name = ""	// non consistent default...
		) {
		$horizontal_plain_menu_blck = "";
		$t = CreateTemplate(array('tplfile' => $this->horizontalPlainMenuTpl));
		$t->set_block("tplfile", "template", "template_blck");
		$t->set_block("template", "horizontal_plain_menu_cell", "horizontal_plain_menu_cell_blck");
		$t->set_var("horizontal_plain_menu_cell_blck", "");
		$t->set_block("horizontal_plain_menu_cell", "plain_menu_cell", "plain_menu_cell_blck");	
		$t->set_var("plain_menu_cell_blck", "");
		for ($cnt=$this->_firstItem[$menu_name]; $cnt<=$this->_lastItem[$menu_name]; $cnt++) {
			if ($this->tree[$cnt]["level"] == 1 && $cnt > $this->_firstItem[$menu_name]) {
				$t->parse("horizontal_plain_menu_cell_blck", "horizontal_plain_menu_cell", true);
				$t->set_var("plain_menu_cell_blck", "");
			}
			$nbsp = "";
			for ($i=1; $i<$this->tree[$cnt]["level"]; $i++) {
				$nbsp .= "&nbsp;&nbsp;&nbsp;";
			}
			$t->set_var(array(
				"nbsp"		=> $nbsp,
				"href"		=> $this->tree[$cnt]["parsed_href"],
				"title"		=> $this->tree[$cnt]["parsed_title"],
				"target"	=> $this->tree[$cnt]["parsed_target"],
				"text"		=> $this->tree[$cnt]["parsed_text"]
			));
			$t->parse("plain_menu_cell_blck", "plain_menu_cell", true);
		}
		$t->parse("horizontal_plain_menu_cell_blck", "horizontal_plain_menu_cell", true);
		$this->_horizontalPlainMenu[$menu_name] = $t->parse("template_blck", "template");
	
		return $this->_horizontalPlainMenu[$menu_name];
	}
	
	function getHorizontalPlainMenu($menu_name) {
		return $this->_horizontalPlainMenu[$menu_name];
	}
	
	function printHorizontalPlainMenu($menu_name) {
		print $this->_horizontalPlainMenu[$menu_name];
	}
}
?>
