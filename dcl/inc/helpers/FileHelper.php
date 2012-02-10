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

LoadStringResource('bo');

class FileHelper
{
	var $iType;
	var $iKey1;
	var $iKey2;
	var $sFileName;
	var $sTempFileName;
	var $sRoot;

	public function IsValid()
	{
		return ($this->iType == DCL_ENTITY_WORKORDER || $this->iType == DCL_ENTITY_PROJECT || $this->iType == DCL_ENTITY_TICKET || $this->iType == DCL_ENTITY_WORKORDER_TASK);
	}

	public function Download()
	{
		if (!$this->IsValid())
			return trigger_error('Invalid attachment type.');

		if (file_exists($this->GetFilePath()))
		{
			$hFile = fopen($this->GetFilePath(), 'rb');
			if ($hFile)
			{
				// Output the file and nothing else
				ob_end_clean();
				ob_start();
				header('Content-Type: application/binary; name="' . $this->sFileName . '"');
				header('Content-Disposition: attachment; filename="' . $this->sFileName . '"');

				while (!feof($hFile))
				{
					echo fread($hFile, 4096);
					ob_flush();
					flush();
				}
				
				fclose($hFile);

				exit;
			}
		}
	}

	public function Upload()
	{
		if (!$this->IsValid() || $this->sTempFileName == '' || $this->sTempFileName == 'none')
			return;

		if (!copy($this->sTempFileName, $this->GetFilePath()))
			trigger_error(STR_BO_UPLOADERR);
	}

	public function AddPath($sPath, $sDir)
	{
		$retVal = $sPath . '/' . $sDir;
		if (!is_dir($retVal))
			mkdir($retVal, 0750);
		return $retVal;
	}

	public function GetFileDir($bCreateDir = true)
	{
		if (!$bCreateDir)
		{
			switch($this->iType)
			{
				case DCL_ENTITY_WORKORDER:
					return sprintf('%s/%s/%s/%s/%s', $this->sRoot, 'wo', substr($this->iKey1, -1), $this->iKey1, $this->iKey2);
				case DCL_ENTITY_TICKET:
					return sprintf('%s/%s/%s/%s', $this->sRoot, 'tck', substr($this->iKey1, -1), $this->iKey1);
				case DCL_ENTITY_PROJECT:
					return sprintf('%s/%s/%s/%s', $this->sRoot, 'prj', substr($this->iKey1, -1), $this->iKey1);
				case DCL_ENTITY_WORKORDER_TASK:
					return sprintf('%s/%s/%s/%s', $this->sRoot, 'wotask', substr($this->iKey1, -1), $this->iKey1);
			}

			return trigger_error('Invalid attachment type.');;
		}

		$retVal = $this->sRoot;
		switch($this->iType)
		{
			case DCL_ENTITY_WORKORDER:
				$retVal = $this->AddPath($retVal, 'wo');
				$retVal = $this->AddPath($retVal, substr($this->iKey1, -1));
				$retVal = $this->AddPath($retVal, $this->iKey1);
				$retVal = $this->AddPath($retVal, $this->iKey2);
				break;
			case DCL_ENTITY_TICKET:
				$retVal = $this->AddPath($retVal, 'tck');
				$retVal = $this->AddPath($retVal, substr($this->iKey1, -1));
				$retVal = $this->AddPath($retVal, $this->iKey1);
				break;
			case DCL_ENTITY_PROJECT:
				$retVal = $this->AddPath($retVal, 'prj');
				$retVal = $this->AddPath($retVal, substr($this->iKey1, -1));
				$retVal = $this->AddPath($retVal, $this->iKey1);
				break;
			case DCL_ENTITY_WORKORDER_TASK:
				$retVal = $this->AddPath($retVal, 'wotask');
				$retVal = $this->AddPath($retVal, substr($this->iKey1, -1));
				$retVal = $this->AddPath($retVal, $this->iKey1);
				break;
			default:
				return '';
		}

		return $retVal;
	}

	public function GetFilePath()
	{
		// don't even bother
		if (!Filter::IsValidFileName($this->sFileName))
			return trigger_error('Invalid characters detected in filename.');

		return $this->GetFileDir() . '/' . $this->sFileName;
	}

	public function GetAttachments($entityTypeId, $id1, $id2 = 0)
	{
		global $dcl_info;

		$this->Init($entityTypeId, $id1, $id2);

		$aFiles = array();
		$sDir = $this->GetFileDir(false);
		if ($sDir != '' && $hDir = @opendir($sDir))
		{
			while ($sFileName = @readdir($hDir))
			{
				$sFullPath = $sDir . '/' . $sFileName;
				if (is_file($sFullPath) && is_readable($sFullPath))
				{
					$aFiles[] = array(
									'filename' => $sFileName,
									'filesize' => filesize($sFullPath),
									'filedate' => date($dcl_info['DCL_TIMESTAMP_FORMAT'], filemtime($sFullPath))
									);
				}
			}
		}

		return $aFiles;
	}
	
	public function DeleteAttachments($entityTypeId, $id1, $id2 = 0)
	{
		$this->Init($entityTypeId, $id1, $id2);
		$attachPath = $this->GetFileDir(false) . '/';
		
		if (($hDir = @opendir($attachPath)) != null)
		{
			while ($fileName = @readdir($hDir))
			{
				if (is_file($attachPath . $fileName) && is_readable($attachPath . $fileName))
					unlink($attachPath . $fileName);
			}

			@closedir($hDir);
		}
	}
	
	private function Init($entityTypeId, $id1, $id2 = 0)
	{
		global $dcl_info;

		$this->iType = $entityTypeId;
		$this->iKey1 = $id1;
		$this->iKey2 = $id2;
		$this->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';

		if (!$this->IsValid())
			throw new InvalidArgumentException('Invalid attachment type.');
	}
}
