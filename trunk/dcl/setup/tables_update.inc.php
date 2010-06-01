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

$versions = array('0.5.1', '0.5.2', '0.5.3', '0.5.4', '0.5.5', '0.5.6', '0.5.7', '0.5.8', '0.5.9', '0.5.10', '0.5.11', '0.5.12',
						'0.5.13', '0.5.14', '0.5.15', '0.5.16', '0.5.17', '0.5.18', '0.9.0', '0.9.1', '0.9.2', '0.9.3', '0.9.4',
						'0.9.4.1', '0.9.4.2', '0.9.4.3', '0.9.4.4', '0.9.5RC1', '0.9.5RC2', '0.9.5RC3', '0.9.5RC4', '0.9.5RC5',
						'0.9.5RC6', '0.9.5RC7', '0.9.5RC8', '0.9.5RC9', '0.9.5RC10', '0.9.5RC11', '0.9.5RC12', '0.9.5RC13');

foreach ($versions as $version)
{
	$UPGRADE_VERSIONS[] = $version;
	require_once(DCL_ROOT . 'setup/update/' . $version . '.php');
}
