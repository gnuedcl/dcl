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

class LayersMenuCommon
{
	var $_packageName;
	var $version;
	var $copyright;
	var $author;
	var $prependedUrl = "";
	var $haltOnError = "yes";
	var $dirroot;
	var $libjsdir;
	var $imgdir;
	var $imgwww;
	var $tpldir;
	var $menuStructure;
	var $_nodesCount;
	var $tree;
	var $_maxLevel;
	var $_firstLevelCnt;
	var $_firstItem;
	var $_lastItem;

	function LayersMenuCommon()
	{
		$this->_packageName = "PHP Layers Menu";
		$this->version = "3.1.1";
		$this->copyright = "(C) 2001-2003";
		$this->author = "Marco Pratesi (marco at telug dot it)";
	
		$this->prependedUrl = "";
	
		$this->dirroot = DCL_ROOT;
		$this->libjsdir = DCL_ROOT . 'js/';
		$this->imgdir = DCL_ROOT . 'img/';
		$this->imgwww = DCL_WWW_ROOT . 'img/';
		$this->tpldir = DCL_ROOT . 'templates/' . GetDefaultTemplateSet() . '/';
		$this->menuStructure = "";
		$this->separator = "|";
	
		$this->_nodesCount = 0;
		$this->tree = array();
		$this->_maxLevel = array();
		$this->_firstLevelCnt = array();
		$this->_firstItem = array();
		$this->_lastItem = array();
	}

	function setPrependedUrl($prependedUrl)
	{
		// We do not perform any check
		$this->prependedUrl = $prependedUrl;
		return true;
	}

	function setDirrootCommon($dirroot)
	{
		if (!is_dir($dirroot)) {
			$this->error("setDirroot: $dirroot is not a directory.");
			return false;
		}
		if (substr($dirroot, -1) != "/") {
			$dirroot .= "/";
		}
		$oldlength = strlen($this->dirroot);
		$foobar = strpos($this->libjsdir, $this->dirroot);
		if (!($foobar === false || $foobar != 0)) {
			$this->libjsdir = $dirroot . substr($this->libjsdir, $oldlength);
		}
		$foobar = strpos($this->imgdir, $this->dirroot);
		if (!($foobar === false || $foobar != 0)) {
			$this->imgdir = $dirroot . substr($this->imgdir, $oldlength);
		}
		$foobar = strpos($this->tpldir, $this->dirroot);
		if (!($foobar === false || $foobar != 0)) {
			$this->tpldir = $dirroot . substr($this->tpldir, $oldlength);
		}
		$this->dirroot = $dirroot;
		return true;
	}

	function setLibjsdir($libjsdir)
	{
		if ($libjsdir != "" && substr($libjsdir, -1) != "/")
			$libjsdir .= "/";

		if ($libjsdir == "" || substr($libjsdir, 0, 1) != "/")
		{
			$foobar = strpos($libjsdir, $this->dirroot);
			if ($foobar === false || $foobar != 0)
				$libjsdir = $this->dirroot . $libjsdir;
		}

		if (!is_dir($libjsdir))
		{
			$this->error("setLibjsdir: $libjsdir is not a directory.");
			return false;
		}
		
		$this->libjsdir = $libjsdir;
		
		return true;
	}

	function setImgdir($imgdir)
	{
		if ($imgdir != "" && substr($imgdir, -1) != "/") {
			$imgdir .= "/";
		}
		if ($imgdir == "" || substr($imgdir, 0, 1) != "/") {
			$foobar = strpos($imgdir, $this->dirroot);
			if ($foobar === false || $foobar != 0) {
				$imgdir = $this->dirroot . $imgdir;
			}
		}
		if (!is_dir($imgdir)) {
			$this->error("setImgdir: $imgdir is not a directory.");
			return false;
		}
		$this->imgdir = $imgdir;
		return true;
	}

	function setImgwww($imgwww)
	{
		if ($imgwww != "" && substr($imgwww, -1) != "/") {
			$imgwww .= "/";
		}
		$this->imgwww = $imgwww;
	}

	function setTpldirCommon($tpldir) {
		if ($tpldir != "" && substr($tpldir, -1) != "/") {
			$tpldir .= "/";
		}
		if ($tpldir == "" || substr($tpldir, 0, 1) != "/") {
			$foobar = strpos($tpldir, $this->dirroot);
			if ($foobar === false || $foobar != 0) {
				$tpldir = $this->dirroot . $tpldir;
			}
		}
		if (!is_dir($tpldir)) {
			$this->error("setTpldir: $tpldir is not a directory.");
			return false;
		}
		$this->tpldir = $tpldir;
		return true;
	}
	
	function setMenuStructureFile($tree_file) {
		if (!($fd = fopen($tree_file, "r"))) {
			$this->error("setMenuStructureFile: unable to open file $tree_file.");
			return false;
		}
		$this->menuStructure = "";
		while ($buffer = fgets($fd, 4096)) {
			$buffer = ereg_replace(chr(13), "", $buffer);	// Microsoft Stupidity Suppression
			$this->menuStructure .= $buffer;
		}
		fclose($fd);
		if ($this->menuStructure == "") {
			$this->error("setMenuStructureFile: $tree_file is empty.");
			return false;
		}
		return true;
	}
	
	function setMenuStructureString($tree_string) {
		$this->menuStructure = ereg_replace(chr(13), "", $tree_string);	// Microsoft Stupidity Suppression
		if ($this->menuStructure == "") {
			$this->error("setMenuStructureString: empty string.");
			return false;
		}
		return true;
	}
	
	function setSeparator($separator) {
		$this->separator = $separator;
	}
	
	function setDBConnParms($dsn, $persistent=false) {
		if (!is_string($dsn)) {
			$this->error("initdb: \$dsn is not an string.");
			return false;
		}
		if (!is_bool($persistent)) {
			$this->error("initdb: \$persistent is not a boolean.");
			return false;
		}
		$this->dsn = $dsn;
		$this->persistent = $persistent;
		return true;
	}
	
	function setTableName($tableName) {
		if (!is_string($tableName)) {
			$this->error("setTableName: \$tableName is not a string.");
			return false;
		}
		$this->tableName = $tableName;
		return true;
	}
	
	function setTableName_i18n($tableName_i18n) {
		if (!is_string($tableName_i18n)) {
			$this->error("setTableName_i18n: \$tableName_i18n is not a string.");
			return false;
		}
		$this->tableName_i18n = $tableName_i18n;
		return true;
	}
	
	function setTableFields($tableFields) {
		if (!is_array($tableFields)) {
			$this->error("setTableFields: \$tableFields is not an array.");
			return false;
		}
		if (count($tableFields) == 0) {
			$this->error("setTableFields: \$tableFields is a zero-length array.");
			return false;
		}
		reset ($tableFields);
		while (list($key, $value) = each($tableFields)) {
			$this->tableFields[$key] = ($value == "") ? "''" : $value;
		}
		return true;
	}
	
	function setTableFields_i18n($tableFields_i18n) {
		if (!is_array($tableFields_i18n)) {
			$this->error("setTableFields_i18n: \$tableFields_i18n is not an array.");
			return false;
		}
		if (count($tableFields_i18n) == 0) {
			$this->error("setTableFields_i18n: \$tableFields_i18n is a zero-length array.");
			return false;
		}
		reset ($tableFields_i18n);
		while (list($key, $value) = each($tableFields_i18n)) {
			$this->tableFields_i18n[$key] = ($value == "") ? "''" : $value;
		}
		return true;
	}
	
	function parseStructureForMenu(
		$menu_name = ""	// non consistent default...
		) {
		$this->_maxLevel[$menu_name] = 0;
		$this->_firstLevelCnt[$menu_name] = 0;
		$this->_firstItem[$menu_name] = $this->_nodesCount + 1;
		$cnt = $this->_firstItem[$menu_name];
		$menuStructure = $this->menuStructure;
	
		/* *********************************************** */
		/* Partially based on a piece of code taken from   */
		/* TreeMenu 1.1 - Bjorge Dijkstra (bjorge@gmx.net) */
		/* *********************************************** */
	
		while ($menuStructure != "") {
			$before_cr = strcspn($menuStructure, "\n");
			$buffer = substr($menuStructure, 0, $before_cr);
			$menuStructure = substr($menuStructure, $before_cr+1);
			if (substr($buffer, 0, 1) != "#") {	// non commented item line...
				$tmp = rtrim($buffer);
				$node = explode($this->separator, $tmp);
				for ($i=count($node); $i<=6; $i++) {
					$node[$i] = "";
				}
				$this->tree[$cnt]["level"] = strlen($node[0]);
				$this->tree[$cnt]["text"] = $node[1];
				$this->tree[$cnt]["href"] = $node[2];
				$this->tree[$cnt]["title"] = $node[3];
				$this->tree[$cnt]["icon"] = $node[4];
				$this->tree[$cnt]["target"] = $node[5];
				$this->tree[$cnt]["expanded"] = $node[6];
				$cnt++;
			}
		}
	
		/* *********************************************** */
	
		$this->_lastItem[$menu_name] = count($this->tree);
		$this->_nodesCount = $this->_lastItem[$menu_name];
		$this->tree[$this->_lastItem[$menu_name]+1]["level"] = 0;
		$this->_postParse($menu_name);
	}
	
	function _depthFirstSearch($tmpArray, $menu_name, $parent_id=1, $level) {
		reset ($tmpArray);
		while (list($id, $foobar) = each($tmpArray)) {
			if ($foobar["parent_id"] == $parent_id) {
				unset($tmpArray[$id]);
				unset($this->_tmpArray[$id]);
				$cnt = count($this->tree) + 1;
				$this->tree[$cnt]["level"] = $level;
				$this->tree[$cnt]["text"] = $foobar["text"];
				$this->tree[$cnt]["href"] = $foobar["href"];
				$this->tree[$cnt]["title"] = $foobar["title"];
				$this->tree[$cnt]["icon"] = $foobar["icon"];
				$this->tree[$cnt]["target"] = $foobar["target"];
				$this->tree[$cnt]["expanded"] = $foobar["expanded"];
				unset($foobar);
				if ($id != $parent_id) {
					$this->_depthFirstSearch($this->_tmpArray, $menu_name, $id, $level+1);
				}
			}
		}
	}
	
	function _postParse($menu_name = '')
	{
		for ($cnt=$this->_firstItem[$menu_name]; $cnt<=$this->_lastItem[$menu_name]; $cnt++) {	// this counter scans all nodes of the new menu
			$this->tree[$cnt]["child_of_root_node"] = ($this->tree[$cnt]["level"] == 1);
			$this->tree[$cnt]["parsed_text"] = stripslashes($this->tree[$cnt]["text"]);
			$this->tree[$cnt]["parsed_href"] = (ereg_replace(" ", "", $this->tree[$cnt]["href"]) == "") ? "#" : $this->prependedUrl . $this->tree[$cnt]["href"];
			$this->tree[$cnt]["parsed_title"] = ($this->tree[$cnt]["title"] == "") ? "" : " title=\"" . addslashes($this->tree[$cnt]["title"]) . "\"";
			$fooimg = $this->imgdir . $this->tree[$cnt]["icon"];
			if ($this->tree[$cnt]["icon"] == "" || !(file_exists($fooimg))) {
				$this->tree[$cnt]["parsed_icon"] = "";
			} else {
				$this->tree[$cnt]["parsed_icon"] = $this->tree[$cnt]["icon"];
				$foobar = getimagesize($fooimg);
				$this->tree[$cnt]["iconwidth"] = $foobar[0];
				$this->tree[$cnt]["iconheight"] = $foobar[1];
			}
			$this->tree[$cnt]["parsed_target"] = ($this->tree[$cnt]["target"] == "") ? "" : " target=\"" . $this->tree[$cnt]["target"] . "\"";
			$this->_maxLevel[$menu_name] = max($this->_maxLevel[$menu_name], $this->tree[$cnt]["level"]);
			if ($this->tree[$cnt]["level"] == 1) {
				$this->_firstLevelCnt[$menu_name]++;
			}
		}
	}
	
	function error($errormsg) {
		print "<b>LayersMenu Error:</b> " . $errormsg . "<br />\n";
		if ($this->haltOnError == "yes") {
			die("<b>Halted.</b><br />\n");
		}
	}
} /* END OF CLASS */
?>
