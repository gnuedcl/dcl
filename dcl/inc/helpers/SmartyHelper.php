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

		$this->assign('DIR_JS', DCL_WWW_ROOT . "templates/js/");
		$this->assign('DIR_CSS', DCL_WWW_ROOT . "templates/css/");
		$this->assign('DIR_IMG', DCL_WWW_ROOT . "templates/img/");
		$this->assign('DIR_VENDOR', DCL_WWW_ROOT . "vendor/");
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

	private function SmartyInit($sTemplateSet = '')
	{
		$templateDirs = array();
		if (mb_substr($sTemplateSet, 0, 8) == 'plugins.')
		{
			$sPluginPath = mb_substr($sTemplateSet, 8);
			$templateDirs[] = GetPluginDir() . $sPluginPath . '/templates/';
		}

		$templateDirs[] = DCL_ROOT . "templates/";

		$this->setTemplateDir($templateDirs)
			->setCompileDir(DCL_ROOT . 'templates/templates_c');
	}
}