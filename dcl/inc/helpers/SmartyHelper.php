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

class SmartyHelper extends Smarty
{
	public function __construct()
	{
		parent::__construct();

		$defaultTemplateSet = GetDefaultTemplateSet();

		$this->assign('DIR_JS', DCL_WWW_ROOT . "js/");
		$this->assign('DIR_CSS', DCL_WWW_ROOT . "templates/$defaultTemplateSet/css/");
		$this->assign('DIR_IMG', DCL_WWW_ROOT . "templates/$defaultTemplateSet/img/");
		$this->assign('WWW_ROOT', DCL_WWW_ROOT);
		$this->assign('URL_MAIN_PHP', menuLink());

		$this->addPluginsDir(array(DCL_ROOT . 'vendor/Smarty/plugins', DCL_ROOT . 'inc/plugins'));

		$this->error_reporting = E_ALL & ~E_NOTICE;
	}

	public function Render($templateFileName, $templateSet = '')
	{
		$this->SmartyInit($templateFileName, $templateSet);
		$this->display($templateFileName);
	}

	public function ToString($templateFileName, $templateSet = '')
	{
		$this->SmartyInit($templateFileName, $templateSet);
		return $this->fetch($templateFileName);
	}

	private function SmartyInit($sTemplateName, $sTemplateSet = '')
	{
		if (substr($sTemplateSet, 0, 8) == 'plugins.')
		{
			$sPluginPath = substr($sTemplateSet, 8);
			$this->setTemplateDir(GetPluginDir() . $sPluginPath . '/templates/');
			$this->setCompileDir(GetPluginDir() . $sPluginPath . '/templates_c/');

			// Nothing more to do for plugins
			return;
		}

		if ($sTemplateSet == '')
			$sDefaultTemplateSet = GetDefaultTemplateSet();
		else
			$sDefaultTemplateSet = $sTemplateSet;

		$this->setTemplateDir(DCL_ROOT . "templates/$sDefaultTemplateSet/");
		if (!$this->templateExists($sTemplateName) && $sDefaultTemplateSet != 'default')
		{
			$sDefaultTemplateSet = 'default';
			$this->setTemplateDir(DCL_ROOT . "templates/default/");
			if (!$this->templateExists($sTemplateName))
			{
				ShowError("Cannot find template [$sTemplateName]");
				return;
			}
		}

		// Have the template
		$this->setCompileDir(DCL_ROOT . 'templates/' . $sDefaultTemplateSet . '/templates_c');
	}
}