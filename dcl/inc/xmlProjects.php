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

class xmlProjects
{
	var $template = '';
	var $dataElementName = '';
	var $function = '';
	var $currentFile = '';
	var $currentFileTitle = '';
	var $comboHTML = '';
	var $comboJS = '';
	var $comboArrayJS = '';
	var $jcn;
	var $xmlParser;
	var $objWO;
	var $lBaseDate;
	var $sourceArray;
	var $templateArray;
	var $templateArrayIndex;
	var $varArray;
	var $oDate;

	function xmlProjects()
	{
		$this->oDate = new DateHelper;
	}

	function getSource($source)
	{
		$table = $source;
		if ($source == 'accounts' || $source == 'dcl_org')
		{
			$valField = 'org_id';
			$textField = 'name';
			$objName = 'OrganizationModel';
			$table = 'dcl_org';
		}
		elseif ($source == 'personnel')
		{
			$valField = 'id';
			$textField = 'short';
			$objName = 'PersonnelModel';
		}
		elseif ($source == 'products')
		{
			$valField = 'id';
			$textField = 'name';
			$objName = 'ProductModel';
		}

		$query = sprintf('select %s,%s from %s order by %s', $valField, $textField, $table, $textField);

		$obj = new $objName();
		if ($obj->Query($query) != -1)
		{
			$i = 0;
			$this->sourceArray[$source][$i]['id'] = 0;
			$this->sourceArray[$source][$i]['text'] = STR_CMMN_NOSELECTION;
			$i++;
			while ($obj->next_record())
			{
				$this->sourceArray[$source][$i]['id'] = trim($obj->f(0));
				$this->sourceArray[$source][$i]['text'] = trim($obj->f(1));
				$i++;
			}
		}
	}

	function startElement($parser, $name, $attrs)
	{
		global $dcl_info;

		$lName = strtolower($name);
		if ($this->function == 'createProjectFromTemplate')
		{
			if ($lName == 'wo')
			{
				$this->objWO->Clear();
				foreach ($attrs as $key => $val)
				{
					if (preg_match('/^[@](.*)/', $val))
					{
						$val = substr($val, 1);
						$found = false;
						foreach ($this->varArray as $kk => $vv)
						{
							if ($val == $kk)
							{
								$val = $vv;
								$found = true;
								break;
							}
						}
						
						if ($found == false)
							$val = '0';
					}
					if (strtolower($key) == 'deadlineon')
					{
						if (preg_match('/([+-])([0-9]*)([dh])/', $val, $deadline))
						{
							$pm = $deadline[1];
							$units = $deadline[2];
							$unit = $deadline[3];
							$oneUnit = 0.0;

							if ($unit == 'd')
								$oneUnit = 24 * 60 * 60 * $units;
							else
								$oneUnit = 60 * 60 * $units;

							if ($pm == '-')
								$oneUnit = -$oneUnit;
							$dDate = $this->lBaseDate + $oneUnit;
							$this->objWO->deadlineon = date($dcl_info['DCL_DATE_FORMAT'], $dDate);
							$this->objWO->estendon = date($dcl_info['DCL_DATE_FORMAT'], $dDate);
						}
					}
					else
						eval('$this->objWO->' . strtolower($key) . "= \"$val\";");
				}
			}

			if ($lName == 'summary' || $lName == 'notes' || $lName == 'description')
				$GLOBALS['dataElementName'] = $lName;
		}
		else if ($this->function == 'createCombo')
		{
			if ($lName == 'project')
			{
				$this->templateArrayIndex = 0;
				foreach ($attrs as $key => $val)
					if (strtolower($key) == 'name')
						$this->comboHTML .= '<option value="' . $this->currentFileTitle . '">' . $val . '</option>';
			}
			else if ($lName == 'promptitem')
			{
				foreach ($attrs as $key => $val)
				{
					if (strtolower($key) == 'source')
					{
						if (!IsSet($this->sourceArray[$val]))
						{
							$this->sourceArray[$val] = array();
							$this->getSource($val);
						}
						
						$this->templateArray[$this->currentFileTitle][$this->templateArrayIndex]['source'] = $val;
					}
					else if (strtolower($key) == 'text')
						$this->templateArray[$this->currentFileTitle][$this->templateArrayIndex]['text'] = $val;
					else if (strtolower($key) == 'varname')
						$this->templateArray[$this->currentFileTitle][$this->templateArrayIndex]['varname'] = $val;
				}
			}
		}
	}

	function endElement($parser, $name)
	{
		global $dcl_info;

		$lName = strtolower($name);
		if ($this->function == 'createProjectFromTemplate')
		{
			if ($lName == 'summary' || $lName == 'notes' || $lName == 'description')
				$GLOBALS['dataElementName'] = '';

			if ($lName == 'wo')
			{
				$this->oDate->SetFromDisplay($this->objWO->estendon);
				$dDate = $this->oDate->time;
				$calcHours = $this->objWO->esthours - 8.0;
				if ($this->objWO->esthours <= 8.0)
					$calcHours = 0.0;
				$this->objWO->eststarton = date($dcl_info['DCL_DATE_FORMAT'], $dDate - (($calcHours / 8.0) * 24 * 60 * 60));
				$this->objWO->createby = $GLOBALS['DCLID'];
				$this->objWO->etchours = $objWO->esthours;
				if ($this->objWO->responsible == 0)
				{
					$objProduct = new ProductModel();
					$objProduct->Load($this->objWO->product);
					$this->objWO->responsible = $objProduct->reportto;
				}
				if ($this->jcn > 0)
				{
					$this->objWO->jcn = $this->jcn;
					$this->objWO->Add();
				}
				else
				{
					$this->objWO->Add();
					$this->jcn = $this->objWO->jcn;
				}

				//if ($this->objWO->responsible != $GLOBALS["DCLID"])
				//  $this->objWO-SendNewMailMessage();
			}
		}
		else if ($this->function == 'createCombo')
		{
			if ($lName == 'promptitem')
			{
				$this->templateArrayIndex++;
			}
		}
	}

	function dataElement($parser, $data)
	{
		if ($this->function != 'createProjectFromTemplate')
			return;

		if ($GLOBALS['dataElementName'] != '')
		{
			$field = $GLOBALS['dataElementName'];
			$this->objWO->$field .= $data;
		}
	}

	function createProjectFromTemplate($projectid, $template, $baseDate, $vars)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$this->xmlParser = xml_parser_create();
		xml_set_object($this->xmlParser, $this);
		$this->function = 'createProjectFromTemplate';
		xml_set_element_handler($this->xmlParser, 'startElement', 'endElement');
		xml_set_character_data_handler($this->xmlParser, 'dataElement');

		$this->oDate->SetFromDisplay($baseDate);
		$this->lBaseDate = $this->oDate->time;

		$this->objWO = new WorkOrderModel();
		$this->varArray = $vars;

		$template .= '.xml';
		if (!($fp = fopen($dcl_info['DCL_FILE_PATH'] . '/prj/' . $template, 'r')))
			die(sprintf(STR_CMMN_FILEOPENERR, $template));

		$this->jcn = 0;
		while ($data = fread($fp, 4096))
		{
			if (!xml_parse($this->xmlParser, $data, feof($fp)))
			{
				die(sprintf(STR_CMMN_PARSEERR, $template,
						xml_error_string(xml_get_error_code($this->xmlParser)),
						xml_get_current_line_number($this->xmlParser)));
			}
		}
		fclose($fp);

		xml_parser_free($this->xmlParser);

		$objPM = new ProjectMapModel();
		$objPM->projectid = $projectid;
		$objPM->jcn = $this->jcn;
		$objPM->seq = 0;
		$objPM->Add();
	}

	function createCombo($name = 'template')
	{
		global $dcl_info;

		$this->comboHTML = '<input type="hidden" name="encodedparams" value="">';
		$this->comboHTML .= '<select name="' . $name . '" onChange="changeTemplate(this.form);">';
		$this->comboHTML .= '<option value="0">Select One.';

		$this->sourceArray = array();
		$this->templateArray = array();
		$this->templateArrayIndex = 0;

		$hDir = opendir($dcl_info['DCL_FILE_PATH'] . '/prj/');
		while ($fileName = readdir($hDir))
		{
			if (preg_match('/(.*)(\.xml)$/', $fileName))
			{
				if ($fp = fopen($dcl_info['DCL_FILE_PATH'] . '/prj/' . $fileName, 'r'))
				{
					$this->currentFile = $fileName;
					$this->xmlParser = xml_parser_create();

					xml_set_object($this->xmlParser, $this);
					$this->function = 'createCombo';
					xml_set_element_handler($this->xmlParser, 'startElement', 'endElement');

					$this->currentFileTitle = substr($fileName, 0, -4);

					if (!IsSet($this->templateArray[$this->currentFileTitle]))
						$this->templateArray[$this->currentFileTitle] = array();

					while ($data = fread($fp, 4096))
					{
						if (!xml_parse($this->xmlParser, $data, feof($fp)))
						{
							$this->comboHTML .= '</select>';
							die(sprintf(STR_CMMN_PARSEERR,
									$fileName,
									xml_error_string(xml_get_error_code($this->xmlParser)),
									xml_get_current_line_number($this->xmlParser)));
						}
					}
					fclose($fp);
					$this->currentFile = '';
					$this->currentFileTitle = '';
					xml_parser_free($this->xmlParser);
				}
			}
		}

		$this->comboHTML .= '</select>';
		$this->comboJS = '';
		foreach ($this->sourceArray as $key => $val)
		{
			$this->comboJS .= "params['$key'] = new Array();\n";
			foreach ($val as $key2 => $val2)
			{
				$this->comboJS .= "params['$key'][$key2] = new Array();\n";
				foreach ($val2 as $key3 => $val3)
				{
					$this->comboJS .= "params['$key'][$key2]['$key3'] = \"$val3\";\n";
				}
			}
		}

		foreach ($this->templateArray as $key => $val)
		{
			$this->comboJS .= "templ['$key'] = new Array();\n";

			foreach ($val as $key2 => $val2)
			{
				$this->comboJS .= "templ['$key'][$key2] = new Array();\n";

				foreach ($val2 as $key3 => $val3)
				{
					$this->comboJS .= "templ['$key'][$key2]['$key3'] = \"$val3\";\n";
				}
			}
		}
	}
}
