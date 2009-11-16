<?php
/*
 * $Id$
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
class LayersMenu extends LayersMenuCommon
{
	var $horizontalMenuTpl;
	var $verticalMenuTpl;
	var $subMenuTpl;
	var $header;
	var $listl;
	var $father_keys;
	var $father_vals;
	var $moveLayers;
	var $_firstLevelMenu;
	var $footer;
	var $forwardArrowImg;
	var $downArrowImg;
	var $transparentIcon;
	var $_hasIcons;
	var $menuTopShift;
	var $menuRightShift;
	var $menuLeftShift;
	var $thresholdY;
	var $abscissaStep;
	
	function LayersMenu($menuTopShift = 6, $menuRightShift = 7, $menuLeftShift = 2, $thresholdY = 5, $abscissaStep = 140)
	{
		$this->LayersMenuCommon();
	
		$this->_packageName = "PHP Layers Menu";
		$this->version = "3.1.1";
		$this->copyright = "(C) 2001-2003";
		$this->author = "Marco Pratesi (marco at telug dot it)";
	
		$this->horizontalMenuTpl = $this->tpldir . "layersmenu-horizontal_menu.ihtml";
		$this->verticalMenuTpl = $this->tpldir . "layersmenu-vertical_menu.ihtml";
		$this->subMenuTpl = $this->tpldir . "layersmenu-sub_menu.ihtml";
	
		$this->header = "";
		$this->listl = "";
		$this->father_keys = "";
		$this->father_vals = "";
		$this->moveLayers = "";
		$this->_firstLevelMenu = array();
		$this->footer = "";
	
		$this->transparentIcon = "transparent.png";
		$this->_hasIcons = array();
		$this->forwardArrowImg["src"] = "forward-arrow.png";
		$this->forwardArrowImg["width"] = 4;
		$this->forwardArrowImg["height"] = 7;
		$this->downArrowImg["src"] = "down-arrow.png";
		$this->downArrowImg["width"] = 9;
		$this->downArrowImg["height"] = 5;
		$this->menuTopShift = $menuTopShift;
		$this->menuRightShift = $menuRightShift;
		$this->menuLeftShift = $menuLeftShift;
		$this->thresholdY = $thresholdY;
		$this->abscissaStep = $abscissaStep;
	}
	
	function setMenuTopShift($menuTopShift) {
		$this->menuTopShift = $menuTopShift;
	}
	
	function setMenuRightShift($menuRightShift) {
		$this->menuRightShift = $menuRightShift;
	}
	
	function setMenuLeftShift($menuLeftShift) {
		$this->menuLeftShift = $menuLeftShift;
	}
	
	function setThresholdY($thresholdY) {
		$this->thresholdY = $thresholdY;
	}
	
	function setAbscissaStep($abscissaStep) {
		$this->abscissaStep = $abscissaStep;
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
		$foobar = strpos($this->horizontalMenuTpl, $oldtpldir);
		if (!($foobar === false || $foobar != 0)) {
			$this->horizontalMenuTpl = $this->tpldir . substr($this->horizontalMenuTpl, $oldlength);
		}
		$foobar = strpos($this->verticalMenuTpl, $oldtpldir);
		if (!($foobar === false || $foobar != 0)) {
			$this->verticalMenuTpl = $this->tpldir . substr($this->verticalMenuTpl, $oldlength);
		}
		$foobar = strpos($this->subMenuTpl, $oldtpldir);
		if (!($foobar === false || $foobar != 0)) {
			$this->subMenuTpl = $this->tpldir . substr($this->subMenuTpl, $oldlength);
		}
	}
	
	function setHorizontalMenuTpl($horizontalMenuTpl) {
		if (str_replace("/", "", $horizontalMenuTpl) == $horizontalMenuTpl) {
			$horizontalMenuTpl = $this->tpldir . $horizontalMenuTpl;
		}
		if (!file_exists($horizontalMenuTpl)) {
			$this->error("setHorizontalMenuTpl: file $horizontalMenuTpl does not exist.");
			return false;
		}
		$this->horizontalMenuTpl = $horizontalMenuTpl;
		return true;
	}
	
	function setVerticalMenuTpl($verticalMenuTpl) {
		if (str_replace("/", "", $verticalMenuTpl) == $verticalMenuTpl) {
			$verticalMenuTpl = $this->tpldir . $verticalMenuTpl;
		}
		if (!file_exists($verticalMenuTpl)) {
			$this->error("setVerticalMenuTpl: file $verticalMenuTpl does not exist.");
			return false;
		}
		$this->verticalMenuTpl = $verticalMenuTpl;
		return true;
	}
	
	function setSubMenuTpl($subMenuTpl) {
		if (str_replace("/", "", $subMenuTpl) == $subMenuTpl) {
			$subMenuTpl = $this->tpldir . $subMenuTpl;
		}
		if (!file_exists($subMenuTpl)) {
			$this->error("setSubMenuTpl: file $subMenuTpl does not exist.");
			return false;
		}
		$this->subMenuTpl = $subMenuTpl;
		return true;
	}
	
	function setTransparentIcon($transparentIcon) {
		$this->transparentIcon = $transparentIcon;
	}
	
	function setForwardArrowImg($forwardArrowImg) {
		if (!file_exists($this->imgdir . $forwardArrowImg)) {
			$this->error("setForwardArrowImg: file " . $this->imgdir . $forwardArrowImg . " does not exist.");
			return false;
		}
		$foobar = getimagesize($this->imgdir . $forwardArrowImg);
		$this->forwardArrowImg["src"] = $forwardArrowImg;
		$this->forwardArrowImg["width"] = $foobar[0];
		$this->forwardArrowImg["height"] = $foobar[1];
		return true;
	}
	
	function setDownArrowImg($downArrowImg) {
		if (!file_exists($this->imgdir . $downArrowImg)) {
			$this->error("setDownArrowImg: file " . $this->imgdir . $downArrowImg . " does not exist.");
			return false;
		}
		$foobar = getimagesize($this->imgdir . $downArrowImg);
		$this->downArrowImg["src"] = $downArrowImg;
		$this->downArrowImg["width"] = $foobar[0];
		$this->downArrowImg["height"] = $foobar[1];
		return true;
	}
	
	function parseCommon(
		$menu_name = ""	// non consistent default...
		) {
		$this->_hasIcons[$menu_name] = false;
		for ($cnt=$this->_firstItem[$menu_name]; $cnt<=$this->_lastItem[$menu_name]; $cnt++) {	// this counter scans all nodes of the new menu
			$this->_hasIcons[$cnt] = false;
			$this->tree[$cnt]["layer_label"] = "L" . $cnt;
			$current_node[$this->tree[$cnt]["level"]] = $cnt;
			if (!$this->tree[$cnt]["child_of_root_node"]) {
				$this->tree[$cnt]["father_node"] = $current_node[$this->tree[$cnt]["level"]-1];
				$this->father_keys .= ",'L" . $cnt . "'";
				$this->father_vals .= ",'" . $this->tree[$this->tree[$cnt]["father_node"]]["layer_label"] . "'";
			}
			$this->tree[$cnt]["not_a_leaf"] = ($this->tree[$cnt+1]["level"]>$this->tree[$cnt]["level"] && $cnt<$this->_lastItem[$menu_name]);
			// if the above condition is true, the node is not a leaf,
			// hence it has at least a child; if it is false, the node is a leaf
			if ($this->tree[$cnt]["not_a_leaf"]) {
				// initialize the corresponding layer content trought a void string
				$this->tree[$cnt]["layer_content"] = "";
				// the new layer is accounted for in the layers list
				$this->listl .= ",'" . $this->tree[$cnt]["layer_label"] . "'";
			}
	/*
			if ($this->tree[$cnt]["not_a_leaf"]) {
				$this->tree[$cnt]["parsed_href"] = "#";
	*/
			if ($this->tree[$cnt]["parsed_icon"] == "") {
				$this->tree[$cnt]["iconsrc"] = $this->transparentIcon;
				$this->tree[$cnt]["iconwidth"] = 16;
				$this->tree[$cnt]["iconheight"] = 16;
				$this->tree[$cnt]["iconalt"] = " ";
			} else {
				if ($this->tree[$cnt]["level"] > 1) {
					$this->_hasIcons[$this->tree[$cnt]["father_node"]] = true;
				} else {
					$this->_hasIcons[$menu_name] = true;
				}
				$this->tree[$cnt]["iconsrc"] = $this->tree[$cnt]["parsed_icon"];
				$this->tree[$cnt]["iconalt"] = "O";
			}
		}
	}
	
	function _updateFooter(
		$menu_name = ""	// non consistent default...
		) {
		$t = CreateTemplate(array('tplfile' => $this->subMenuTpl));
		$t->set_block("tplfile", "template", "template_blck");
		$t->set_block("template", "sub_menu_cell", "sub_menu_cell_blck");
		$t->set_var("sub_menu_cell_blck", "");
		$t->set_var("abscissaStep", $this->abscissaStep);
	
		for ($cnt=$this->_firstItem[$menu_name]; $cnt<=$this->_lastItem[$menu_name]; $cnt++) {
			if ($this->tree[$cnt]["not_a_leaf"]) {
				$t->set_var(array(
					"layer_label"		=> $this->tree[$cnt]["layer_label"],
					"layer_title"		=> $this->tree[$cnt]["text"],
					"sub_menu_cell_blck"	=> $this->tree[$cnt]["layer_content"]
				));
				$this->footer .= $t->parse("template_blck", "template");
			}
		}
	}
	
	function newHorizontalMenu(
		$menu_name = ""	// non consistent default...
		) {
		if (!isset($this->_firstItem[$menu_name]) || !isset($this->_lastItem[$menu_name])) {
			$this->error("newHorizontalMenu: the first/last item of the menu '$menu_name' is not defined; please check if you have parsed its menu data.");
			return 0;
		}
	
		$this->parseCommon($menu_name);
	
		$t = CreateTemplate(array('tplfile' => $this->horizontalMenuTpl));
		$t->set_block("tplfile", "template", "template_blck");
		$t->set_block("template", "horizontal_menu_cell", "horizontal_menu_cell_blck");
		$t->set_var("horizontal_menu_cell_blck", "");
		$t->set_block("horizontal_menu_cell", "cell_link", "cell_link_blck");
		$t->set_var("cell_link_blck", "");
		$t->set_block("cell_link", "cell_icon", "cell_icon_blck");
		$t->set_var("cell_icon_blck", "");
		$t->set_block("cell_link", "cell_arrow", "cell_arrow_blck");
		$t->set_var("cell_arrow_blck", "");
	
		$t_sub = CreateTemplate(array('tplfile' => $this->subMenuTpl));
		$t_sub->set_block("tplfile", "sub_menu_cell", "sub_menu_cell_blck");
		$t_sub->set_var("sub_menu_cell_blck", "");
		$t_sub->set_block("sub_menu_cell", "cell_icon", "cell_icon_blck");
		$t_sub->set_var("cell_icon_blck", "");
		$t_sub->set_block("sub_menu_cell", "cell_arrow", "cell_arrow_blck");
		$t_sub->set_var("cell_arrow_blck", "");
	
		$this->_firstLevelMenu[$menu_name] = "";
	
		$foobar = $this->_firstItem[$menu_name];
		$this->moveLayers .= "\tvar " . $menu_name . "TOP = getOffsetTop('" . $menu_name . "L" . $foobar . "');\n";
		$this->moveLayers .= "\tvar " . $menu_name . "HEIGHT = getOffsetHeight('" . $menu_name . "L" . $foobar . "');\n";
	
		for ($cnt=$this->_firstItem[$menu_name]; $cnt<=$this->_lastItem[$menu_name]; $cnt++) {	// this counter scans all nodes of the new menu
			if ($this->tree[$cnt]["not_a_leaf"]) {
				// geometrical parameters are assigned to the new layer, related to the above mentioned children
				if ($this->tree[$cnt]["child_of_root_node"]) {
					$this->moveLayers .= "\tsetTop('" . $this->tree[$cnt]["layer_label"] . "', "  . $menu_name . "TOP + " . $menu_name . "HEIGHT);\n";
					$this->moveLayers .= "\tmoveLayerX1('" . $this->tree[$cnt]["layer_label"] . "', '" . $menu_name . "');\n";
				}
			}
	
			if ($this->tree[$cnt]["child_of_root_node"]) {
				if ($this->tree[$cnt]["not_a_leaf"]) {
					$this->tree[$cnt]["onmouseover"] = " onmouseover=\"moveLayerX1('" . $this->tree[$cnt]["layer_label"] . "', '" . $menu_name . "') ; LMPopUp('" . $this->tree[$cnt]["layer_label"] . "', false);\"";
				} else {
					$this->tree[$cnt]["onmouseover"] = " onmouseover=\"shutdown();\"";
				}
				$t->set_var(array(
					"menu_layer_label"	=> $menu_name . $this->tree[$cnt]["layer_label"],
					"imgwww"		=> $this->imgwww,
					"transparent"		=> $this->transparentIcon,
					"href"			=> $this->tree[$cnt]["parsed_href"],
					"onmouseover"		=> $this->tree[$cnt]["onmouseover"],
					"title"			=> $this->tree[$cnt]["parsed_title"],
					"target"		=> $this->tree[$cnt]["parsed_target"],
					"text"			=> $this->tree[$cnt]["text"],
					"downsrc"		=> $this->downArrowImg["src"],
					"downwidth"		=> $this->downArrowImg["width"],
					"downheight"		=> $this->downArrowImg["height"]
				));
				if ($this->tree[$cnt]["parsed_icon"] != "") {
					$t->set_var(array(
						"imgwww"	=> $this->imgwww,
						"iconsrc"	=> $this->tree[$cnt]["iconsrc"],
						"iconwidth"	=> $this->tree[$cnt]["iconwidth"],
						"iconheight"	=> $this->tree[$cnt]["iconheight"],
						"iconalt"	=> $this->tree[$cnt]["iconalt"],
					));
					$t->parse("cell_icon_blck", "cell_icon");
				} else {
					$t->set_var("cell_icon_blck", "");
				}
				if ($this->tree[$cnt]["not_a_leaf"]) {
					$t->parse("cell_arrow_blck", "cell_arrow");
				} else {
					$t->set_var("cell_arrow_blck", "");
				}
				$foobar = $t->parse("cell_link_blck", "cell_link");
				$t->set_var(array(
					"cellwidth"		=> $this->abscissaStep,
					"cell_link_blck"	=> $foobar
				));
				$t->parse("horizontal_menu_cell_blck", "horizontal_menu_cell", true);
			} else {
				if ($this->tree[$cnt]["not_a_leaf"]) {
					$this->tree[$cnt]["onmouseover"] = " onmouseover=\"moveLayerX('" . $this->tree[$cnt]["layer_label"] . "') ; moveLayerY('" . $this->tree[$cnt]["layer_label"] . "') ; LMPopUp('" . $this->tree[$cnt]["layer_label"] . "', false);\"";
				} else {
					$this->tree[$cnt]["onmouseover"] = " onmouseover=\"LMPopUp('" . $this->tree[$this->tree[$cnt]["father_node"]]["layer_label"] . "', true);\"";
				}
				$t_sub->set_var(array(
					"imgwww"	=> $this->imgwww,
					"transparent"	=> $this->transparentIcon,
					"href"		=> $this->tree[$cnt]["parsed_href"],
					"refid"		=> "ref" . $this->tree[$cnt]["layer_label"],
					"onmouseover"	=> $this->tree[$cnt]["onmouseover"],
					"title"		=> $this->tree[$cnt]["parsed_title"],
					"target"	=> $this->tree[$cnt]["parsed_target"],
					"text"		=> $this->tree[$cnt]["text"],
					"arrowsrc"	=> $this->forwardArrowImg["src"],
					"arrowwidth"	=> $this->forwardArrowImg["width"],
					"arrowheight"	=> $this->forwardArrowImg["height"]
				));
				if ($this->_hasIcons[$this->tree[$cnt]["father_node"]]) {
					$t_sub->set_var(array(
						"iconsrc"	=> $this->tree[$cnt]["iconsrc"],
						"iconwidth"	=> $this->tree[$cnt]["iconwidth"],
						"iconheight"	=> $this->tree[$cnt]["iconheight"],
						"iconalt"	=> $this->tree[$cnt]["iconalt"]
					));
					$t_sub->parse("cell_icon_blck", "cell_icon");
				} else {
					$t_sub->set_var("cell_icon_blck", "");
				}
				if ($this->tree[$cnt]["not_a_leaf"]) {
					$t_sub->parse("cell_arrow_blck", "cell_arrow");
				} else {
					$t_sub->set_var("cell_arrow_blck", "");
				}
				$this->tree[$this->tree[$cnt]["father_node"]]["layer_content"] .= $t_sub->parse("sub_menu_cell_blck", "sub_menu_cell");
			}
		}	// end of the "for" cycle scanning all nodes
	
		$foobar = $this->_firstLevelCnt[$menu_name] * $this->abscissaStep;
		$t->set_var("menuwidth", $foobar);
		$t->set_var(array(
			"layer_label"	=> $menu_name,
			"menubody"	=> $this->_firstLevelMenu[$menu_name]
		));
		$this->_firstLevelMenu[$menu_name] = $t->parse("template_blck", "template");
	
		$this->_updateFooter($menu_name);
	
		return $this->_firstLevelMenu[$menu_name];
	}
	
	function newVerticalMenu(
		$menu_name = ""	// non consistent default...
		) {
		if (!isset($this->_firstItem[$menu_name]) || !isset($this->_lastItem[$menu_name])) {
			$this->error("newVerticalMenu: the first/last item of the menu '$menu_name' is not defined; please check if you have parsed its menu data.");
			return 0;
		}
	
		$this->parseCommon($menu_name);
	
		$t = new CreateTemplate(array('tplfile' => $this->verticalMenuTpl));
		$t->set_block("tplfile", "template", "template_blck");
		$t->set_block("template", "vertical_menu_box", "vertical_menu_box_blck");
		$t->set_var("vertical_menu_box_blck", "");
		$t->set_block("vertical_menu_box", "vertical_menu_cell", "vertical_menu_cell_blck");
		$t->set_var("vertical_menu_cell_blck", "");
		$t->set_block("vertical_menu_cell", "cell_icon", "cell_icon_blck");
		$t->set_var("cell_icon_blck", "");
		$t->set_block("vertical_menu_cell", "cell_arrow", "cell_arrow_blck");
		$t->set_var("cell_arrow_blck", "");
	
		$t_sub = CreateTemplate(array('tplfile' => $this->subMenuTpl));
		$t_sub->set_block("tplfile", "sub_menu_cell", "sub_menu_cell_blck");
		$t_sub->set_var("sub_menu_cell_blck", "");
		$t_sub->set_block("sub_menu_cell", "cell_icon", "cell_icon_blck");
		$t_sub->set_var("cell_icon_blck", "");
		$t_sub->set_block("sub_menu_cell", "cell_arrow", "cell_arrow_blck");
		$t_sub->set_var("cell_arrow_blck", "");
	
		$this->_firstLevelMenu[$menu_name] = "";
	
		$this->moveLayers .= "\tvar " . $menu_name . "TOP = getOffsetTop('" . $menu_name . "');\n";
		$this->moveLayers .= "\tvar " . $menu_name . "LEFT = getOffsetLeft('" . $menu_name . "');\n";
		$this->moveLayers .= "\tvar " . $menu_name . "WIDTH = getOffsetWidth('" . $menu_name . "');\n";
	
		for ($cnt=$this->_firstItem[$menu_name]; $cnt<=$this->_lastItem[$menu_name]; $cnt++) {	// this counter scans all nodes of the new menu
			if ($this->tree[$cnt]["not_a_leaf"]) {
				// geometrical parameters are assigned to the new layer, related to the above mentioned children
				if ($this->tree[$cnt]["child_of_root_node"]) {
					$this->moveLayers .= "\tsetLeft('" . $this->tree[$cnt]["layer_label"] . "', " . $menu_name . "LEFT + " . $menu_name . "WIDTH - menuRightShift);\n";
				}
			}
	
			if ($this->tree[$cnt]["child_of_root_node"]) {
				if ($this->tree[$cnt]["not_a_leaf"]) {
					$this->tree[$cnt]["onmouseover"] = " onmouseover=\"moveLayerX('" . $this->tree[$cnt]["layer_label"] . "') ; moveLayerY('" . $this->tree[$cnt]["layer_label"] . "') ; LMPopUp('" . $this->tree[$cnt]["layer_label"] . "', false);\"";
				} else {
					$this->tree[$cnt]["onmouseover"] = " onmouseover=\"shutdown();\"";
				}
				$t->set_var(array(
					"imgwww"	=> $this->imgwww,
					"transparent"	=> $this->transparentIcon,
					"href"		=> $this->tree[$cnt]["parsed_href"],
					"refid"		=> "ref" . $this->tree[$cnt]["layer_label"],
					"onmouseover"	=> $this->tree[$cnt]["onmouseover"],
					"title"		=> $this->tree[$cnt]["parsed_title"],
					"target"	=> $this->tree[$cnt]["parsed_target"],
					"text"		=> $this->tree[$cnt]["text"],
					"arrowsrc"	=> $this->forwardArrowImg["src"],
					"arrowwidth"	=> $this->forwardArrowImg["width"],
					"arrowheight"	=> $this->forwardArrowImg["height"]
				));
				if ($this->_hasIcons[$menu_name]) {
					$t->set_var(array(
						"iconsrc"	=> $this->tree[$cnt]["iconsrc"],
						"iconwidth"	=> $this->tree[$cnt]["iconwidth"],
						"iconheight"	=> $this->tree[$cnt]["iconheight"],
						"iconalt"	=> $this->tree[$cnt]["iconalt"]
					));
					$t->parse("cell_icon_blck", "cell_icon");
				} else {
					$t->set_var("cell_icon_blck", "");
				}
				if ($this->tree[$cnt]["not_a_leaf"]) {
					$t->parse("cell_arrow_blck", "cell_arrow");
				} else {
					$t->set_var("cell_arrow_blck", "");
				}
				$this->_firstLevelMenu[$menu_name] .= $t->parse("vertical_menu_cell_blck", "vertical_menu_cell");
			} else {
				if ($this->tree[$cnt]["not_a_leaf"]) {
					$this->tree[$cnt]["onmouseover"] = " onmouseover=\"moveLayerX('" . $this->tree[$cnt]["layer_label"] . "') ; moveLayerY('" . $this->tree[$cnt]["layer_label"] . "') ; LMPopUp('" . $this->tree[$cnt]["layer_label"] . "', false);\"";
				} else {
					$this->tree[$cnt]["onmouseover"] = " onmouseover=\"LMPopUp('" . $this->tree[$this->tree[$cnt]["father_node"]]["layer_label"] . "', true);\"";
				}
				$t_sub->set_var(array(
					"imgwww"	=> $this->imgwww,
					"transparent"	=> $this->transparentIcon,
					"href"		=> $this->tree[$cnt]["parsed_href"],
					"refid"		=> "ref" . $this->tree[$cnt]["layer_label"],
					"onmouseover"	=> $this->tree[$cnt]["onmouseover"],
					"title"		=> $this->tree[$cnt]["parsed_title"],
					"target"	=> $this->tree[$cnt]["parsed_target"],
					"text"		=> $this->tree[$cnt]["text"],
					"arrowsrc"	=> $this->forwardArrowImg["src"],
					"arrowwidth"	=> $this->forwardArrowImg["width"],
					"arrowheight"	=> $this->forwardArrowImg["height"]
				));
				if ($this->_hasIcons[$this->tree[$cnt]["father_node"]]) {
					$t_sub->set_var(array(
						"imgwww"	=> $this->imgwww,
						"iconsrc"	=> $this->tree[$cnt]["iconsrc"],
						"iconwidth"	=> $this->tree[$cnt]["iconwidth"],
						"iconheight"	=> $this->tree[$cnt]["iconheight"],
						"iconalt"	=> $this->tree[$cnt]["iconalt"]
					));
					$t_sub->parse("cell_icon_blck", "cell_icon");
				} else {
					$t_sub->set_var("cell_icon_blck", "");
				}
				if ($this->tree[$cnt]["not_a_leaf"]) {
					$t_sub->parse("cell_arrow_blck", "cell_arrow");
				} else {
					$t_sub->set_var("cell_arrow_blck", "");
				}
				$this->tree[$this->tree[$cnt]["father_node"]]["layer_content"] .= $t_sub->parse("sub_menu_cell_blck", "sub_menu_cell");
			}
		}	// end of the "for" cycle scanning all nodes
	
		$t->set_var(array(
			"menu_name"			=> $menu_name,
			"vertical_menu_cell_blck"	=> $this->_firstLevelMenu[$menu_name]
		));
		$this->_firstLevelMenu[$menu_name] = $t->parse("vertical_menu_box_blck", "vertical_menu_box");
		$t->set_var("abscissaStep", $this->abscissaStep);
		$t->set_var(array(
			"layer_label"			=> $menu_name,
			"vertical_menu_box_blck"	=> $this->_firstLevelMenu[$menu_name]
		));
		$this->_firstLevelMenu[$menu_name] = $t->parse("template_blck", "template");
	
		$this->_updateFooter($menu_name);
	
		return $this->_firstLevelMenu[$menu_name];
	}
	
	function makeHeader() {
		$t = CreateTemplate(array('tplfile' => $this->libjsdir . 'layersmenu-header.ijs'));
		$this->listl = "listl = [" . substr($this->listl, 1) . "];";
		$this->father_keys = "father_keys = [" . substr($this->father_keys, 1) . "];";
		$this->father_vals = "father_vals = [" . substr($this->father_vals, 1) . "];";
		$t->set_var(array(
			"packageName"	=> $this->_packageName,
			"version"	=> $this->version,
			"copyright"	=> $this->copyright,
			"author"	=> $this->author,
			"menuTopShift"	=> $this->menuTopShift,
			"menuRightShift"=> $this->menuRightShift,
			"menuLeftShift"	=> $this->menuLeftShift,
			"thresholdY"	=> $this->thresholdY,
			"abscissaStep"	=> $this->abscissaStep,
			"listl"		=> $this->listl,
			"nodesCount"	=> $this->_nodesCount,
			"father_keys"	=> $this->father_keys,
			"father_vals"	=> $this->father_vals,
			"moveLayers"	=> $this->moveLayers
		));
		$this->header = $t->parse("out", "tplfile");
		return $this->header;
	}
	
	function getHeader() {
		return $this->header;
	}
	
	function printHeader() {
		$this->makeHeader();
		print $this->header;
	}
	
	function getMenu($menu_name) {
		return $this->_firstLevelMenu[$menu_name];
	}
	
	function printMenu($menu_name) {
		print $this->_firstLevelMenu[$menu_name];
	}
	
	function makeFooter() {
		$t = CreateTemplate(array('tplfile' => $this->libjsdir . 'layersmenu-footer.ijs'));
		$t->set_var(array(
			"packageName"	=> $this->_packageName,
			"version"	=> $this->version,
			"copyright"	=> $this->copyright,
			"author"	=> $this->author,
			"footer"	=> $this->footer
			
		));
		$this->footer = $t->parse("out", "tplfile");
		return $this->footer;
	}
	
	function getFooter() {
		return $this->footer;
	}
	
	function printFooter() {
		$this->makeFooter();
		print $this->footer;
	}
}
?>
