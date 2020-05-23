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

class TextSanitizer
{

	/*
	* Constructor of this class
	* Gets allowed html tags from admin config settings
	* <br> should not be allowed since nl2br will be used
	* when storing data
	*/
	function TextSanitizer()
	{

	}

	static function &getInstance()
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new TextSanitizer();
		}
		return $instance;
	}

	function &makeClickable(&$text)
	{
		$patterns = array("/([^]_a-z0-9-=\"'\/])([a-z]+?):\/\/([^, \r\n\"\(\)'<>]+)/i", "/([^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i", "/([^]_a-z0-9-=\"'\/])([a-z0-9\-_.]+?)@([^, \r\n\"\(\)'<>]+)/i");
		$replacements = array("\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", "\\1<a href=\"http://www.\\2.\\3\" target=\"_blank\">www.\\2.\\3</a>", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>");
		return preg_replace($patterns, $replacements, $text);
	}

	function &nl2Br($text)
	{
		return preg_replace("/(\015\012)|(\015)|(\012)/","<br />",$text);
	}

	function &addSlashes($text, $force=false)
	{
		$text =& addslashes($text);
		return $text;
	}

	function &stripSlashesGPC($text)
	{
		return $text;
	}

	/*
	*  for displaying data in html textbox forms
	*/
	function &htmlSpecialChars($text)
	{
		$result = preg_replace("/&amp;/i", '&', htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
		return $result;
	}

	function &undoHtmlSpecialChars(&$text)
	{
		return preg_replace(array("/&gt;/i", "/&lt;/i", "/&quot;/i", "/&#039;/i"), array(">", "<", "\"", "'"), $text);
	}

	/*
	*  Filters textarea form data in DB for display
	*/
	function &displayText($text, $html=false)
	{
		if (! $html) {
			// html not allowed
			$text =& $this->htmlSpecialChars($text);
		}
		$text =& $this->makeClickable($text);
		$text =& $this->nl2Br($text);
		return $text;
	}

	/*
	*  Filters textarea form data submitted for preview
	*/
	function &previewText($text, $html=false)
	{
		$text =& $this->stripSlashesGPC($text);
		return $this->displayText($text, $html);
	}

##################### Deprecated Methods ######################

	function sanitizeForDisplay($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
	{
		if ( $allowhtml == 0 ) {
			$text = $this->htmlSpecialChars($text);
		} else {
			//$config =& $GLOBALS['xoopsConfig'];
			//$allowed = $config['allowed_html'];
			//$text = strip_tags($text, $allowed);
			$text = $this->makeClickable($text);
		}
		if ( $smiley == 1 ) {
			$text = $this->smiley($text);
		}
		if ( $bbcode == 1 ) {
			$text = $this->xoopsCodeDecode($text);
		}
		$text = $this->nl2Br($text);
		return $text;
	}

	function sanitizeForPreview($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
	{
		$text = $this->oopsStripSlashesGPC($text);
		if ( $allowhtml == 0 ) {
			$text = $this->htmlSpecialChars($text);
		} else {
			//$config =& $GLOBALS['xoopsConfig'];
			//$allowed = $config['allowed_html'];
			//$text = strip_tags($text, $allowed);
			$text = $this->makeClickable($text);
		}
		if ( $smiley == 1 ) {
			$text = $this->smiley($text);
		}
		if ( $bbcode == 1 ) {
			$text = $this->xoopsCodeDecode($text);
		}
		$text = $this->nl2Br($text);
		return $text;
	}

	function makeTboxData4Save($text)
	{
		//$text = $this->undoHtmlSpecialChars($text);
		return $this->addSlashes($text);
	}

	function makeTboxData4Show($text, $smiley=0)
	{
		$text = $this->htmlSpecialChars($text);
		return $text;
	}

	function makeTboxData4Edit($text)
	{
		return $this->htmlSpecialChars($text);
	}

	function makeTboxData4Preview($text, $smiley=0)
	{
		$text = $this->stripSlashesGPC($text);
		$text = $this->htmlSpecialChars($text);
		return $text;
	}

	function makeTboxData4PreviewInForm($text)
	{
		$text = $this->stripSlashesGPC($text);
		return $this->htmlSpecialChars($text);
	}

	function makeTareaData4Save($text)
	{
		return $this->addSlashes($text);
	}

	function &makeTareaData4Show(&$text, $html=1, $smiley=1, $xcode=1)
	{
		return $this->displayTarea($text, $html, $smiley, $xcode);
	}

	function makeTareaData4Edit($text)
	{
		return htmlSpecialChars($text, ENT_QUOTES, 'UTF-8');
	}

	function &makeTareaData4Preview(&$text, $html=1, $smiley=1, $xcode=1)
	{
		return $this->previewTarea($text, $html, $smiley, $xcode);
	}

	function makeTareaData4PreviewInForm($text)
	{
		$text = $this->stripSlashesGPC($text);
		return htmlSpecialChars($text, ENT_QUOTES, 'UTF-8');
	}

	function makeTareaData4InsideQuotes($text)
	{
		return $this->htmlSpecialChars($text);
	}

	function &oopsStripSlashesGPC($text)
	{
		return $this->stripSlashesGPC($text);
	}

	function &oopsStripSlashesRT($text)
	{
		return $text;
	}

	function &oopsAddSlashes($text)
	{
		return $this->addSlashes($text);
	}

	function &oopsHtmlSpecialChars($text)
	{
		return $this->htmlSpecialChars($text);
	}

	function &oopsNl2Br($text)
	{
		return $this->nl2br($text);
	}
}
?>
