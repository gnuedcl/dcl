<?php
/*
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

class htmlChecklistForm
{
	var $xml;
	var $oPersonnel;
	var $oProducts;
	var $bFirstState;
	var $oDate;
	var $oTimestamp;
	var $bIsView;
	var $oSelect;

	function htmlChecklistForm()
	{
		$this->oPersonnel = NULL;
		$this->oProducts = NULL;
		$this->oSelect = NULL;
		$this->bFirstState = true;
		$this->oDate = new DateHelper;
		$this->oTimestamp = new TimestampHelper;
	}

	function GetNodeValue(&$node)
	{
		$this->xml->FindChildNode($node, 'Values');
		if ($this->xml->currentNode != NULL)
		{
			if (count($this->xml->currentNode->childNodes) > 1)
			{
				$aRetVal = array();
				foreach ($this->xml->currentNode->childNodes as $v)
					$aRetVal[] = $v->data;

				return $aRetVal;
			}
			else if (count($this->xml->currentNode->childNodes) == 1)
			{
				return $this->xml->currentNode->childNodes[0]->data;
			}
		}

		return '';
	}

	function SetNodeValue(&$node, &$vValue)
	{
		if (is_array($vValue))
		{
			$this->xml->FindChildNode($node, 'Values');
			if ($this->xml->currentNode == NULL)
			{
				$this->xml->AddChildNode($node, 'Values', array());
				$this->xml->currentNode = &$this->xml->node->childNodes[count($this->xml->node->childNodes) - 1];
			}

			$this->xml->currentNode->childNodes = array();
			foreach ($vValue as $k => $v)
			{
				$this->xml->AddChildNode($this->xml->currentNode, 'Value', array());
				$oChild = &$this->xml->currentNode->childNodes[count($this->xml->currentNode->childNodes) - 1];
				$oChild->data = $v;
			}
		}
		else
		{
			$this->xml->FindChildNode($node, 'Value');
			if ($this->xml->currentNode != NULL)
				$this->xml->currentNode->data = $vValue;
		}
	}

	function RenderDate(&$node)
	{
		echo '<input data-input-type="date" type="text" size="10" maxlength="10" ';
		echo 'name="' . $node->attributes['name'] . '" ';
		echo 'id="' . $node->attributes['name'] . '" ';
		$sValue = $this->GetNodeValue($node);
		if ($sValue != '')
		{
			$this->oDate->SetFromANSI($sValue);
			echo ' value="' . $this->oDate->ToDisplay() . '"';
		}
		echo '>';
	}

	function RenderPersonnel(&$node)
	{
		if ($this->oSelect == NULL)
			$this->oSelect = new SelectHtmlHelper();

		if (substr($node->attributes['type'], -3, 3) == 'one')
			$this->oSelect->Size = 0;
		else
			$this->oSelect->Size = 8;

		$this->oSelect->Id = $node->attributes['name'];
		$this->oSelect->DefaultValue = $this->GetNodeValue($node);
		$this->oSelect->FirstOption = STR_CMMN_SELECTONE;
		$this->oSelect->SetOptionsFromDb('personnel', 'short', 'short');
		$this->oSelect->Render();
	}

	function RenderProducts(&$node)
	{
		if ($this->oSelect == NULL)
			$this->oSelect = new SelectHtmlHelper();

		if (substr($node->attributes['type'], -3, 3) == 'one')
			$this->oSelect->Size = 0;
		else
			$this->oSelect->Size = 8;

		$this->oSelect->Id = $node->attributes['name'];
		$this->oSelect->DefaultValue = $this->GetNodeValue($node);
		$this->oSelect->FirstOption = STR_CMMN_SELECTONE;
		$this->oSelect->SetOptionsFromDb('products', 'name', 'name');
		$this->oSelect->Render();
	}

	function RenderSelectOne(&$node)
	{
		$this->xml->FindChildNode($node, 'Options');
		if ($this->xml->currentNode != NULL)
		{
			echo '<select id="' . htmlentities($node->attributes['name']) . '" name="' . htmlentities($node->attributes['name']) . '">';
			$o = &$this->xml->currentNode;
			for ($i = 0; $i < count($o->childNodes); $i++)
				echo '<option value="' . htmlentities($o->childNodes[$i]->data) . '">' . htmlentities($o->childNodes[$i]->data) . '</option>';
			echo '</select>';
		}
	}

	function RenderField(&$node)
	{
		echo '<div>';
		echo '<label for="">', htmlentities($node->attributes['display']), ':</label>';
		if ($this->bIsView)
		{
			$sVal = $this->GetNodeValue($node);
			if ($node->attributes['type'] == 'date' || $node->attributes['type'] == 'createdate')
			{
				if ($sVal != '')
				{
					$this->oDate->SetFromANSI($sVal);
					echo htmlentities($this->oDate->ToDisplay());
				}
			}
			else
			{
				if (is_array($sVal))
					$sVal = implode(', ', $sVal);

				echo htmlentities($sVal);
			}

			echo '</div>';
			return;
		}

		switch ($node->attributes['type'])
		{
			case 'personnel-one':
			case 'personnel-multi':
				$this->RenderPersonnel($node);
				break;
			case 'products-one':
			case 'products-multi':
				$this->RenderProducts($node);
				break;
			case 'select-one':
				$this->RenderSelectOne($node);
				break;
			case 'textarea':
				echo '<textarea name="', $node->attributes['name'], '" rows="';
				echo $node->attributes['rows'], '" cols="', $node->attributes['cols'], '">';
				echo htmlentities($this->GetNodeValue($node));
				echo '</textarea>';
				break;
			case 'date':
				$this->RenderDate($node);
				break;
			case 'time':
				echo '<input type="text" name="' . $node->attributes['name'];
				echo '" size="5" maxlength="5" value="';
				echo htmlentities($this->GetNodeValue($node));
				echo '">';
				break;
			case 'phone':
			case 'email':
			case 'text':
				echo '<input type="text" name="' . $node->attributes['name'];
				if (isset($node->attributes['size']))
					echo '" size="' . $node->attributes['size'];
					
				echo '" value="';
				echo htmlentities($this->GetNodeValue($node));
				echo '">';
				break;
			case 'createdate':
				$sVal = $this->GetNodeValue($node);
				if ($sVal != '')
				{
					$this->oDate->SetFromANSI($sVal);
					echo htmlentities($this->oDate->ToDisplay());
				}
				echo GetHiddenVar($node->attributes['name'], $sVal);
				break;
			case 'createby':
			case 'autoincrement':
				$sVal = $this->GetNodeValue($node);
				echo $sVal;
				echo GetHiddenVar($node->attributes['name'], $sVal);
				break;
			default:
				echo $node->attributes['type'];
		}
		echo '</div>';
	}

	function RenderState(&$node)
	{
		if (!$this->bFirstState)
			echo '</fieldset>';
	
		$this->bFirstState = false;
		
		echo '<fieldset>';
		echo '<legend>', htmlentities($node->attributes['name']), '</legend>';
	}

	function RenderNode(&$node)
	{
		switch($node->name)
		{
			case 'Field':
				$this->RenderField($node);
				break;
			case 'State':
				$this->RenderState($node);
				break;
			case 'Name':
				// Render title - show state combo beneath
				echo '<fieldset><legend>' . htmlentities($node->data) . '</legend>';
				$this->RenderStateCombo();
				echo '</fieldset>';
				break;
		}

		if (is_array($node->childNodes))
		{
			foreach ($node->childNodes as $child)
				$this->RenderNode($child);
		}
	}

	function RenderStateCombo()
	{
		$currentState = '';
		$this->xml->FindChildNode($this->xml->root, 'CurrentState');
		if ($this->xml->currentNode != NULL)
			$currentState = $this->xml->currentNode->data;

		if ($this->bIsView)
		{
			echo '<div><label for="dcl_chklst_status">', STR_CHK_STATE, ':</label>';
			echo $currentState;
			echo '</div>';
			return;
		}

		$this->xml->ClearList();
		$this->xml->ListNodes($this->xml->root, 'State', 'name', '*');
		if (count($this->xml->nodes) > 0)
		{
			echo '<div class="form-group"><label for="dcl_chklst_status">', STR_CHK_STATE, '</label><div class="col-sm-4"><select class="form-control" id="dcl_chklst_status" name="dcl_chklst_status">';
			for ($i = 0; $i < count($this->xml->nodes); $i++)
			{
				echo '<option value="', htmlentities($this->xml->nodes[$i]->attributes['name']), '"';
				if ($this->xml->nodes[$i]->attributes['name'] == $currentState)
					echo ' selected';
					
				echo '>', htmlentities($this->xml->nodes[$i]->attributes['name']), '</option>';
			}
			
			echo '</select></div></div>';
		}
	}

	function show($id, $file, $bIsView = false)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		$this->xml = new XmlDocumentHelper();
		$this->xml->ParseFile($file);

		$t = new SmartyHelper();
		
		$t->assignByRef('root', $this->xml->root);

		$t->Render('ChecklistForm.tpl');

		echo '<form class="form-horizontal" method="post" action="' . menuLink() . '">';
		if (!$bIsView)
		{
			echo GetHiddenVar('menuAction', 'boChecklists.dbmodify');
			echo GetHiddenVar('dcl_chklst_id', $id);
		}
		$this->bFirstState = true;
		$this->bIsView = $bIsView;
		$this->RenderNode($this->xml->root);
		
		if (!$this->bFirstState)
			echo '</fieldset>';
			
		echo '<fieldset><div class="submit">';
		if (!$bIsView)
			echo '<input class="btn btn-primary" type="submit" value="' . STR_CMMN_SAVE . '">';

		echo '<input class="btn btn-link" type="button" value="' . STR_CMMN_CANCEL . '" onclick="location.href=\'' . menuLink('', 'menuAction=boChecklists.show') . '\';"></div></fieldset></form>';
	}

	function UpdateNodes(&$node)
	{
		if (IsSet($_REQUEST[$node->attributes['name']]))
		{
			$sData = $_REQUEST[$node->attributes['name']];
			if (!is_array($sData))
			{
				if (get_magic_quotes_gpc() == 1)
					$sData = stripslashes($sData);

				if ($node->data != $sData)
					$this->SetNodeValue($node, $sData);
			}
			else
				$this->SetNodeValue($node, $sData);
		}

		for ($i = 0; $i < count($node->childNodes); $i++)
			$this->UpdateNodes($node->childNodes[$i]);
	}
}
