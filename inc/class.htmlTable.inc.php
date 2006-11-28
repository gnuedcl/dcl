<?php
/*
 * $Id: class.htmlTable.inc.php,v 1.1.1.1 2006/11/27 05:30:45 mdean Exp $
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

class htmlTable
{
	var $aData;
	var $aToolbar;
	var $aCols;
	var $aFooter;
	var $aGroups;
	var $aCheckVals;
	var $sCaption;
	var $bShowRownum;
	var $sTemplate;
	var $bInline;
	var $bShowChecks;
	var $oSmarty;
	var $sWidth;
	var $bSpacer;
	
	function htmlTable()
	{
		$this->oSmarty = CreateSmarty();
		
		$this->aData = array();
		$this->aToolbar = array();
		$this->aCols = array();
		$this->aFooter = array();
		$this->aGroups = array();
		$this->aCheckVals = array();
		$this->sCaption = '';
		$this->sTemplate = 'htmlTable.tpl';
		$this->bShowRownum = false;
		$this->bInline = false;
		$this->bShowChecks = false;
		$this->sWidth = 0;
		$this->bSpacer = false;
	}
	
	function setData($aData)
	{
		$this->aData = $aData;
	}
	
	function setWidth($sWidth)
	{
		$this->sWidth = $sWidth;
	}
	
	function setSpacer($bSpacer)
	{
		$this->bSpacer = $bSpacer;
	}
	
	function addRow($aRow)
	{
		array_push($this->aData, $aRow);
	}
	
	function addColumn($sCol, $sType)
	{
		array_push($this->aCols, array('title' => $sCol, 'type' => $sType));
	}
	
	function addGroup($iGroupColumn)
	{
		array_push($this->aGroups, $iGroupColumn);
	}
	
	function setFooter($aFooter)
	{
		$this->aFooter = $aFooter;
	}
	
	function addFooter($sFooter)
	{
		array_push($this->aFooter, $sFooter);
	}
	
	function addToolbar($sLink, $sText)
	{
		array_push($this->aToolbar, array('link' => $sLink, 'text' => $sText));
	}
	
	function setCaption($sCaption)
	{
		$this->sCaption = $sCaption;
	}
	
	function setShowRownum($bShowRownum)
	{
		$this->bShowRownum = (bool)$bShowRownum;
	}
	
	function setShowChecks($bShowChecks)
	{
		$this->bShowChecks = $bShowChecks;
	}
	
	function setInline($bInline)
	{
		$this->bInline = $bInline;
	}
	
	function assign($sVar, $oValue)
	{
		$this->oSmarty->assign($sVar, $oValue);
	}
	
	function render()
	{
		$this->oSmarty->assign('columns', $this->aCols);
		$this->oSmarty->assign('footer', $this->aFooter);
		$this->oSmarty->assign('records', $this->aData);
		$this->oSmarty->assign('groups', $this->aGroups);
		$this->oSmarty->assign('toolbar', $this->aToolbar);
		$this->oSmarty->assign('caption', $this->sCaption);
		$this->oSmarty->assign('rownum', $this->bShowRownum);
		$this->oSmarty->assign('inline', $this->bInline);
		$this->oSmarty->assign('checks', $this->bShowChecks);
		$this->oSmarty->assign('checkvals', $this->aCheckVals);
		$this->oSmarty->assign('width', $this->sWidth);
		$this->oSmarty->assign('spacer', $this->bSpacer);
		
		SmartyDisplay($this->oSmarty, $this->sTemplate);
	}
}
?>