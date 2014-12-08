<?php
/*
 * Derived from XOOPS Setup
 *
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

include_once 'class/textsanitizer.php';

/**
* setting manager for XOOPS installer
*
* @author Haruki Setoyama  <haruki@planewave.org>
* @access public
**/
class setting_manager {

    var $dbType;
    var $dbHost;
	var $dbPort;
    var $dbUser;
    var $dbPassword;
    var $dbName;
    var $dcl_root;
    var $dcl_www_root;

    var $sanitizer;

    function setting_manager($post=false){
        $this->sanitizer =& TextSanitizer::getInstance();
        if($post){
            $this->readPost();
        }else{
            $this->dbType = 'pgsql';
            $this->dbHost = 'localhost';
			$this->dbPort = '5432';

            $this->dcl_root = str_replace("\\","/",getcwd()); // "
            $this->dcl_root = str_replace("/setup", "/", $this->dcl_root);

            $filepath = (! empty($_SERVER['REQUEST_URI']))
                            ? dirname($_SERVER['REQUEST_URI'])
                            : dirname($_SERVER['SCRIPT_NAME']);

            $filepath = str_replace("\\", "/", $filepath); // "
            $filepath = str_replace("/setup", "", $filepath);
            if ( mb_substr($filepath, 0, 1) == "/" ) {
                $filepath = mb_substr($filepath,1);
            }
            if ( mb_substr($filepath, -1) == "/" ) {
                $filepath = mb_substr($filepath, 0, -1);
            }

			$this->dcl_www_root = "/" . $filepath . "/";
        }
    }

    function readPost(){
        if(isset($_POST['dbType']))
            $this->dbType = $this->sanitizer->stripSlashesGPC($_POST['dbType']);
        if(isset($_POST['dbHost']))
            $this->dbHost = $this->sanitizer->stripSlashesGPC($_POST['dbHost']);
        if(isset($_POST['dbPort']))
            $this->dbPort = $this->sanitizer->stripSlashesGPC($_POST['dbPort']);
        if(isset($_POST['dbUser']))
            $this->dbUser = $this->sanitizer->stripSlashesGPC($_POST['dbUser']);
        if(isset($_POST['dbPassword']))
            $this->dbPassword = $this->sanitizer->stripSlashesGPC($_POST['dbPassword']);
        if(isset($_POST['dbName']))
            $this->dbName = $this->sanitizer->stripSlashesGPC($_POST['dbName']);
        if(isset($_POST['dcl_root']))
            $this->dcl_root = $this->sanitizer->stripSlashesGPC($_POST['dcl_root']);
        if(isset($_POST['dcl_www_root']))
            $this->dcl_www_root = $this->sanitizer->stripSlashesGPC($_POST['dcl_www_root']);
    }

    function readConstant(){
		global $dcl_domain_info;

		if(isset($dcl_domain_info['default']['dbType']))
			$this->dbType = $this->sanitizer->stripSlashesGPC($dcl_domain_info['default']['dbType']);
		if(isset($dcl_domain_info['default']['dbHost']))
			$this->dbHost = $this->sanitizer->stripSlashesGPC($dcl_domain_info['default']['dbHost']);
		if(isset($dcl_domain_info['default']['dbPort']))
			$this->dbPort = $this->sanitizer->stripSlashesGPC($dcl_domain_info['default']['dbPort']);
		if(isset($dcl_domain_info['default']['dbUser']))
			$this->dbUser = $this->sanitizer->stripSlashesGPC($dcl_domain_info['default']['dbUser']);
		if(isset($dcl_domain_info['default']['dbPassword']))
			$this->dbPassword = $this->sanitizer->stripSlashesGPC($dcl_domain_info['default']['dbPassword']);
		if(isset($dcl_domain_info['default']['dbName']))
			$this->dbName = $this->sanitizer->stripSlashesGPC($dcl_domain_info['default']['dbName']);
		if(isset($dcl_domain_info['default']['dcl_root']))
			$this->dcl_root = $this->sanitizer->stripSlashesGPC($dcl_domain_info['default']['dcl_root']);
		if(isset($dcl_domain_info['default']['dcl_www_root']))
			$this->dcl_www_root = $this->sanitizer->stripSlashesGPC($dcl_domain_info['default']['dcl_www_root']);
    }

    function checkData(){
        $ret = '';
        $error = array();

        if ( empty($this->dbHost) ) {
            $error[] = sprintf(_INSTALL_L57, _INSTALL_L27);
        }
        if ( empty($this->dbPort) ) {
            $error[] = sprintf(_INSTALL_L57, 'Database Port');
        }
        if ( empty($this->dbName) ) {
            $error[] = sprintf(_INSTALL_L57, _INSTALL_L29);
        }
        if ( empty($this->dcl_root) ) {
            $error[] = sprintf(_INSTALL_L57, _INSTALL_L55);
        }
        if ( empty($this->dcl_www_root) ) {
            $error[] = sprintf(_INSTALL_L57, _INSTALL_L56);
        }

        if (!empty($error)) {
            foreach ( $error as $err ) {
                $ret .=  "<p><span style='color:#ff0000;'><b>".$err."</b></span></p>\n";
            }
        }

        return $ret;
    }

    function editform(){
        $ret =
            "<table class='table table-striped'>
                <tr valign='top' align='left'>
                    <td class='head'>
                        <b>"._INSTALL_L51."</b><br />
                        <span style='font-size:85%;'>"._INSTALL_L66."</span>
                    </td>
                    <td class='even'>
                        <select  size='1' name='dbType' id='dbType'>";
        $dblist = $this->getDBList();
        foreach($dblist as $val){
            $ret .= "<option value='$val'";
            if($val == $this->dbType) $ret .= " selected='selected'";
            $ret .= "'>$val</option>";
        }
        $ret .=         "</select>
                    </td>
                </tr>
                ";
        $ret .= $this->editform_sub(_INSTALL_L27, _INSTALL_L67, 'dbHost', $this->sanitizer->htmlSpecialChars($this->dbHost));
        $ret .= $this->editform_sub('Database Port', 'What port is this server running on the host?', 'dbPort', $this->sanitizer->htmlSpecialChars($this->dbPort));
        $ret .= $this->editform_sub(_INSTALL_L28, _INSTALL_L65, 'dbUser', $this->sanitizer->htmlSpecialChars($this->dbUser));
        $ret .= $this->editform_sub(_INSTALL_L52, _INSTALL_L68, 'dbPassword', $this->sanitizer->htmlSpecialChars($this->dbPassword));
        $ret .= $this->editform_sub(_INSTALL_L29, _INSTALL_L64, 'dbName', $this->sanitizer->htmlSpecialChars($this->dbName));
        $ret .= $this->editform_sub(_INSTALL_L55, _INSTALL_L59, 'dcl_root', $this->sanitizer->htmlSpecialChars($this->dcl_root));
        $ret .= $this->editform_sub(_INSTALL_L56, _INSTALL_L58, 'dcl_www_root', $this->sanitizer->htmlSpecialChars($this->dcl_www_root));

        $ret .= "</table>";
        return $ret;
    }

	function GetMethodCombo($title, $desc, $name, $value)
	{
		$ret = "                <tr valign='top' align='left'>
                    <td class='head'>
                        <b>".$title."</b><br />
                        <span style='font-size:85%;'>".$desc."</span>
                    </td>
                    <td class='even'>
                        <select  size='1' name='$name' id='$name'>";

        $aMethods = $this->GetMethods();
        foreach ($aMethods as $val)
		{
            $ret .= "<option value='$val'";
            if($val == $value)
				$ret .= " selected='selected'";

            $ret .= "'>$val</option>";
        }
        $ret .=         "</select>
                    </td>
                </tr>
				";

		return $ret;
	}

    function editform_sub($title, $desc, $name, $value){
        return  "<tr valign='top' align='left'>
                    <td class='head'>
                        <b>".$title."</b><br />
                        <span style='font-size:85%;'>".$desc."</span>
                    </td>
                    <td class='even'>
                        <input type='text' name='".$name."' id='".$name."' size='30' maxlength='100' value='".htmlspecialchars($value, ENT_QUOTES, 'UTF-8')."' />
                    </td>
                </tr>
                ";
    }

    function confirmForm(){
        $yesno = empty($this->db_pconnect) ? _INSTALL_L24 : _INSTALL_L23;
        $ret =
            "<table class='table table-striped'>
				<tr>
					<td class='bg3'><b>"._INSTALL_L51."</b></td>
					<td class='bg1'>".$this->sanitizer->htmlSpecialChars($this->dbType)."</td>
				</tr>
				<tr>
					<td class='bg3'><b>"._INSTALL_L27."</b></td>
					<td class='bg1'>".$this->sanitizer->htmlSpecialChars($this->dbHost)."</td>
				</tr>
				<tr>
					<td class='bg3'><b>".'Database Port'."</b></td>
					<td class='bg1'>".$this->sanitizer->htmlSpecialChars($this->dbPort)."</td>
				</tr>
				<tr>
					<td class='bg3'><b>"._INSTALL_L28."</b></td>
					<td class='bg1'>".$this->sanitizer->htmlSpecialChars($this->dbUser)."</td>
				</tr>
				<tr>
					<td class='bg3'><b>"._INSTALL_L52."</b></td>
					<td class='bg1'>".$this->sanitizer->htmlSpecialChars($this->dbPassword)."</td>
				</tr>
				<tr>
					<td class='bg3'><b>"._INSTALL_L29."</b></td>
					<td class='bg1'>".$this->sanitizer->htmlSpecialChars($this->dbName)."</td>
				</tr>
				<tr>
					<td class='bg3'><b>"._INSTALL_L55."</b></td>
					<td class='bg1'>".$this->sanitizer->htmlSpecialChars($this->dcl_root)."</td>
				</tr>
				<tr>
					<td class='bg3'><b>"._INSTALL_L56."</b></td>
					<td class='bg1'>".$this->sanitizer->htmlSpecialChars($this->dcl_www_root)."</td>
				</tr>
            </table>
            <input type='hidden' name='dbType' value='".$this->sanitizer->htmlSpecialChars($this->dbType)."' />
            <input type='hidden' name='dbHost' value='".$this->sanitizer->htmlSpecialChars($this->dbHost)."' />
            <input type='hidden' name='dbPort' value='".$this->sanitizer->htmlSpecialChars($this->dbPort)."' />
            <input type='hidden' name='dbUser' value='".$this->sanitizer->htmlSpecialChars($this->dbUser)."' />
            <input type='hidden' name='dbPassword' value='".$this->sanitizer->htmlSpecialChars($this->dbPassword)."' />
            <input type='hidden' name='dbName' value='".$this->sanitizer->htmlSpecialChars($this->dbName)."' />
            <input type='hidden' name='dcl_root' value='".$this->sanitizer->htmlSpecialChars($this->dcl_root)."' />
            <input type='hidden' name='dcl_www_root' value='".$this->sanitizer->htmlSpecialChars($this->dcl_www_root)."' />
            ";
        return $ret;
    }


    function getDBList()
    {
		$retVal = array();
		if (function_exists('pg_connect'))
			$retVal[] = 'pgsql';

		if (function_exists('mysql_connect'))
			$retVal[] = 'mysql';

		if (function_exists('mssql_connect'))
			$retVal[] = 'mssql';

		if (function_exists('sybase_connect'))
			$retVal[] = 'sybase';

		if (function_exists('oci8_connect'))
			$retVal[] = 'oracle';

		return $retVal;
    }

	function GetMethods()
	{
		return array('php', 'header', 'meta');
	}
}


?>
