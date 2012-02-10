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

class PubSub 
{
	private static $Subscriptions = array();
	
	private function __construct()
	{
	}
	
	public static function Subscribe($eventName, $eventHandler)
	{
		if (!isset(self::$Subscriptions[$eventName]))
			self::$Subscriptions[$eventName] = array();
		
		self::$Subscriptions[$eventName][] = $eventHandler;
	}
	
	public static function Unsubscribe($eventName)
	{
		if (isset(self::$Subscriptions[$eventName]))
			unset(self::$Subscriptions[$eventName]);
	}
	
	public static function Publish($eventName)
	{
		if (!isset(self::$Subscriptions[$eventName]) || !is_array(self::$Subscriptions[$eventName]) || count(self::$Subscriptions) == 0)
			return;
		
		$eventParams = func_get_args();
		array_shift($eventParams);
		
		foreach (self::$Subscriptions[$eventName] as $eventHandler)
		{
			if (is_callable($eventHandler))
			{
				call_user_func_array($eventHandler, $eventParams);
			}
		}
	}
}
