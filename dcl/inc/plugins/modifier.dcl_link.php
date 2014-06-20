<?php
/*
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

function smarty_modifier_dcl_link($string)
{
	$sText = nl2br($string);
	$sRetVal = preg_replace('#(http|ftp|telnet|irc|https)://[^<>[:space:]]+[[:alnum:]/]#i', '<a target="_blank" href="\0">\0</a>', $sText);

	// Pseudo stuff
	$sRetVal = preg_replace('#dcl://workorders/([0-9]+)[-]([0-9]+)#i', '<a href="' . menuLink() . '?menuAction=WorkOrder.Detail&jcn=\1&seq=\2">\0</a>', $sRetVal);
	$sRetVal = preg_replace('#dcl://tickets/([0-9]+)#i', '<a href="' . menuLink() . '?menuAction=boTickets.view&ticketid=\1">\0</a>', $sRetVal);
	$sRetVal = preg_replace('#dcl://projects/([0-9]+)#i', '<a href="' . menuLink() . '?menuAction=Project.Detail&id=\1&wostatus=0">\0</a>', $sRetVal);

	return $sRetVal;
}