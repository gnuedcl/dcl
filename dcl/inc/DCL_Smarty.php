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

class DCL_Smarty extends Smarty
{
	function __construct()
	{
		$defaultTemplateSet = GetDefaultTemplateSet();

		$this->assign('DIR_JS', DCL_WWW_ROOT . "js/");
		$this->assign('DIR_CSS', DCL_WWW_ROOT . "templates/$defaultTemplateSet/css/");
		$this->assign('DIR_IMG', DCL_WWW_ROOT . "templates/$defaultTemplateSet/img/");
		$this->assign('WWW_ROOT', DCL_WWW_ROOT);
		$this->assign('URL_MAIN_PHP', menuLink());

		// Add the DCL plugins (now maintained separate from Smarty plugins)
		$this->plugins_dir = array(DCL_ROOT . 'vendor/Smarty/plugins', DCL_ROOT . 'inc/plugins');
	}

	function Render($templateFileName, $templateSet = '')
	{
		$this->SmartyInit($templateFileName, $templateSet);
		$this->display($templateFileName);
	}

	function ToString($templateFileName, $templateSet = '')
	{
		$this->SmartyInit($templateFileName, $templateSet);
		return $this->fetch($templateFileName);
	}

	private function SmartyInit($sTemplateName, $sTemplateSet = '')
	{
		if (substr($sTemplateSet, 0, 8) == 'plugins.')
		{
			$sPluginPath = substr($sTemplateSet, 8);
			$this->template_dir = GetPluginDir() . $sPluginPath . '/templates/';
			$this->compile_dir = GetPluginDir() . $sPluginPath . '/templates_c/';

			// Nothing more to do for plugins
			return;
		}

		if ($sTemplateSet == '')
			$sDefaultTemplateSet = GetDefaultTemplateSet();
		else
			$sDefaultTemplateSet = $sTemplateSet;

		$this->template_dir = DCL_ROOT . "templates/$sDefaultTemplateSet/";
		if (!$this->template_exists($sTemplateName) && $sDefaultTemplateSet != 'default')
		{
			$sDefaultTemplateSet = 'default';
			$this->template_dir = DCL_ROOT . "templates/default/";
			if (!$this->template_exists($sTemplateName))
			{
				trigger_error("Cannot find template [$sTemplateName]");
				return;
			}
		}

		// Have the template
		$this->compile_dir = DCL_ROOT . 'templates/' . $sDefaultTemplateSet . '/templates_c';
	}
}