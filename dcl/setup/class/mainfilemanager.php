<?php
/*
 * Derived from XOOPS Setup
 *
 * Double Choco Latte - Source Configuration Management System
 * Copyright (C) 1999  Michael L. Dean & Tim R. Norman
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
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

/**
* mainfile manager for XOOPS installer
*
* @author Haruki Setoyama  <haruki@planewave.org>
* @access public
**/
class mainfile_manager
{
	var $path = '../inc/config.php';
	var $distfile = '../inc/config.php.default';
	var $rewrite = array();

	var $report = '';
	var $error = false;

	function mainfile_manager()
	{
		//
	}

	function setRewrite($def, $val)
	{
		$this->rewrite[$def] = $val;
	}

	function copyDistFile()
	{
		if (!copy($this->distfile, $this->path))
		{
			$this->report .= _NGIMG . sprintf(_INSTALL_L126, "<b>" . $this->path . "</b>") . "<br />\n";
			$this->error = true;
			return false;
		}

		$this->report .= _OKIMG . sprintf(_INSTALL_L125, "<b>" . $this->path . "</b>", "<b>" . $this->distfile . "</b>") . "<br />\n";

		return true;
	}

    function doRewrite()
	{
        if ( ! $file = fopen($this->distfile,"r") )
		{
            $this->error = true;
            return false;
        }

        $content = fread($file, filesize($this->distfile));
        fclose($file);

        foreach($this->rewrite as $key => $val)
		{
			if (preg_match('/{VAL_' . $key . '}/', $content))
			{
				$content = preg_replace('/{VAL_' . $key . '}/', $val, $content);
				$this->report .= _OKIMG . sprintf(_INSTALL_L121, "<b>$key</b>", $val) . "<br />\n";
            }
			else
			{
                $this->error = true;
                $this->report .= _NGIMG.sprintf(_INSTALL_L122, "<b>$val</b>")."<br />\n";
            }
        }

        if ( !$file = fopen($this->path,"w") ) {
            $this->error = true;
            return false;
        }

        if ( fwrite($file,$content) == -1 ) {
            fclose($file);
            $this->error = true;
            return false;
        }

        fclose($file);

        return true;
    }

    function report(){
        $content = $this->report;
        return $content;
    }

    function error(){
        return $this->error;
    }
}

?>
