<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
 *
 * Double Choco Latte is free software; you can redistribute it and/or
 * This program is free software; you can redistribute it and/or
>>>>>>> 1.1.2.2
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

$GLOBALS['phpgw_baseline']['faqquestions'] = array(
	'fd' => array(
		'questionid' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
		'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'topicid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'questiontext' => array('type' => 'text', 'nullable' => false),
		'createby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'createon' => array('type' => 'timestamp', 'nullable' => false),
		'modifyby' => array('type' => 'int', 'precision' => 4),
		'modifyon' => array('type' => 'timestamp'),
		'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y', 'nullable' => false)
	),
	'pk' => array('questionid'),
	'fk' => array(),
	'ix' => array(),
	'uc' => array()
);
?>
