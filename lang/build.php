<?php
/*
 * $Id: build.php,v 1.1.1.1 2006/11/27 05:30:54 mdean Exp $
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

	if (php_sapi_name() != 'cli')
	{
		echo 'This script can only be run via PHP CLI.';
		exit;
	}
	
	if (version_compare(PHP_VERSION,'5','<'))
	{
		echo "This script requires PHP version 5.\n";
		exit;
	}
	
	$oXML= new DOMDocument();
	$oXML->load('lang.xml');
	
	$aLangs = array('de', 'en', 'es', 'fr', 'it', 'ru', 'sl', 'sv');
	$aModuleNodes = $oXML->getElementsByTagName('module');
	foreach ($aModuleNodes as $oModuleNode)
	{
		// open a file for each language
		$aLangFiles = array();
		foreach ($aLangs as $sLang)
		{
			$aLangFiles[$sLang] = fopen("$sLang/" . $oModuleNode->getAttribute('name') . ".php", 'w');
			fwrite($aLangFiles[$sLang], "<?php\n");
		}
		
		$aPhraseNodes = $oModuleNode->getElementsByTagName('phrase');
		foreach ($aPhraseNodes as $oPhraseNode)
		{
			// Get default phrase from en lang
			$sDefaultPhrase = '';
			$aXltNodes = $oPhraseNode->getElementsByTagName('xlt');
			foreach ($aXltNodes as $oXltNode)
			{
				if ($oXltNode->getAttribute('lang') == 'en')
					$sDefaultPhrase = $oXltNode->nodeValue;
			}

			// Write all available translations
			$aHaveLangs = array();
			foreach ($aXltNodes as $oXltNode)
			{
				$sThisLang = $oXltNode->getAttribute('lang');
				fwrite($aLangFiles[$sThisLang], "\tdefine('" . $oPhraseNode->getAttribute('name') . "', \"" . addslashes(utf8_decode($oXltNode->nodeValue)) . "\");\n");
				$aHaveLangs[$sThisLang] = true;
			}
			
			if (count($aHaveLangs) < count($aLangs))
			{
				// Missing some, so write defaults
				foreach ($aLangs as $sThisLang)
				{
					if (!isset($aHaveLangs[$sThisLang]))
					{
						fwrite($aLangFiles[$sThisLang], "\tdefine('" . $oPhraseNode->getAttribute('name') . "', \"" . addslashes($sDefaultPhrase) . "\");\n");
					}
				}
			}
		}
		
		// close all of the files for each language
		foreach ($aLangFiles as $hFile)
		{
			fwrite($hFile, "?>");
			fclose($hFile);
		}
	}
?>