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

class xmlDoc
{
	// PHP 4 required due to passing/setting objects by reference
	// and use of xml_set_object
	var $root;
	var $parser;
	var $currentNode;
	var $nodes;

	function xmlDoc()
	{
		$this->root = NULL;
		$this->parser = NULL;
		$this->currentNode = NULL;
		$this->nodes = array();
	}

	function ParseString($sXML)
	{
		$this->parser = xml_parser_create();
		xml_set_object($this->parser, &$this);
		xml_set_element_handler($this->parser, 'StartElement', 'EndElement');
		xml_set_character_data_handler($this->parser, 'DataElement');

		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);

		if (!xml_parse($this->parser, $sXML, true))
		{
			trigger_error(sprintf(STR_CMMN_PARSEERR, 'XML string',
					xml_error_string(xml_get_error_code($this->parser)),
					xml_get_current_line_number($this->parser)));
					
			return;
		}

		xml_parser_free($this->parser);
	}

	function ParseFile($sFileName)
	{
		$this->parser = xml_parser_create();
		xml_set_object($this->parser, &$this);
		xml_set_element_handler($this->parser, 'StartElement', 'EndElement');
		xml_set_character_data_handler($this->parser, 'DataElement');

		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);

		if (!($fp = fopen($sFileName, 'r')))
			die(sprintf(STR_CMMN_FILEOPENERR, $sFileName));

		while ($sXML = fread($fp, 4096))
		{
			if (!xml_parse($this->parser, $sXML, feof($fp)))
			{
				trigger_error(sprintf(STR_CMMN_PARSEERR, $sFileName,
						xml_error_string(xml_get_error_code($this->parser)),
						xml_get_current_line_number($this->parser)));

				return;
			}
		}

		fclose($fp);
		xml_parser_free($this->parser);
	}

	function AddChildNode(&$oParent, $sName, $aAttributes)
	{
		$oNew = &CreateObject('dcl.xmlNode');
		$oNew->name = &$sName;
		$oNew->attributes = &$aAttributes;
		$oNew->parentNode = &$oParent;
		$nodeIdx = count($oParent->childNodes);
		$oParent->childNodes[$nodeIdx] = &$oNew;
	}

	function FindChildNode(&$oStart, $element)
	{
		unset($this->currentNode);
		$this->currentNode = NULL;
		for ($i = 0; $i < count($oStart->childNodes) && $this->currentNode == NULL; $i++)
		{
			if ($oStart->childNodes[$i]->name == $element)
			{
				$this->currentNode = &$oStart->childNodes[$i];
			}
			else
			{
				$this->FindChildNode($oStart->childNodes[$i], $element);
				if ($this->currentNode != NULL && $this->currentNode->name == $element)
					return;
			}
		}
	}

	function ListNodes(&$oStart, $element, $attribute, $value)
	{
		if ($oStart->name == $element && IsSet($oStart->attributes[$attribute]) && ($oStart->attributes[$attribute] == $value || $value == "*"))
			$this->nodes[] = &$oStart;

		for ($i = 0; $i < count($oStart->childNodes); $i++)
			$this->ListNodes($oStart->childNodes[$i], $element, $attribute, $value);
	}

	function ClearList()
	{
		$this->nodes = array();
	}

	function StartElement($parser, $name, $attributes)
	{
		if ($this->root == NULL)
		{
			$this->root = &CreateObject('dcl.xmlNode');
			$this->root->name = $name;
			$this->root->attributes = $attributes;
			$this->currentNode = &$this->root;
			return;
		}

		if ($this->currentNode == NULL)
			return;

		// Add new node and set it to be current node
		$this->AddChildNode($this->currentNode, $name, $attributes);
		$this->currentNode = &$this->currentNode->childNodes[count($this->currentNode->childNodes) - 1];
	}

	function EndElement($parser, $name)
	{
		// Get rid of extra junk in data, if any
		$this->currentNode->data = trim($this->currentNode->data);

		// pop current node up the tree
		if ($this->currentNode->parentNode != NULL)
		{
			$parent = &$this->currentNode->parentNode;
			$this->currentNode = &$parent;
			return;
		}

		unset($this->currentNode);
		$this->currentNode = NULL;
	}

	function DataElement($parser, $data)
	{
		$this->currentNode->data .= $data;
	}

	function RenderNode(&$oNode)
	{
		// Opening tag
		$sNode = '<' . $oNode->name;
		if (count($oNode->attributes) > 0)
		{
			foreach ($oNode->attributes as $k => $v)
				$sNode .= ' ' . $k . '="' . $v . '"';
		}

		if (count($oNode->childNodes) == 0 && $oNode->data == '')
		{
			return $sNode . ' />';
		}

		$sNode .= '>';

		$sNode .= $oNode->data;

		// Children
		for ($i = 0; $i < count($oNode->childNodes); $i++)
			$sNode .= $this->RenderNode($oNode->childNodes[$i]);

		// Close Tag
		return $sNode . '</' . $oNode->name . '>';
	}

	function ToXML()
	{
		$retVal = '<?xml version="1.0" ?>' . phpCrLf;

		return $retVal . $this->RenderNode($this->root);
	}

	function ToFile($sFileName)
	{
		if (!($fp = fopen($sFileName, 'w+')))
		{
			trigger_error(sprintf(STR_CMMN_FILEOPENERR, $sFileName));
			return false;
		}

		if (!fwrite($fp, $this->ToXML()))
		{
			trigger_error(sprintf('Could not write to file %s', $sFileName));
			return false;
		}

		fclose($fp);

		return true;
	}
}
?>
