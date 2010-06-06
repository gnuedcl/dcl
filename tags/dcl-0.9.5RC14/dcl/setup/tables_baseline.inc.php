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

$phpgw_baseline = array(
	'personnel' => array(
		'fd' => array(
			'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'short' => array('type' => 'char', 'precision' => 10, 'nullable' => false),
			'lastname' => array('type' => 'varchar', 'precision' => 25),
			'firstname' => array('type' => 'varchar', 'precision' => 20),
			'reportto' => array('type' => 'int', 'precision' => 4),
			'department' => array('type' => 'int', 'precision' => 4),
			'pwd' => array('type' => 'varchar', 'precision' => 50),
			'security' => array('type' => 'int', 'precision' => 4),
			'email' => array('type' => 'varchar', 'precision' => 80),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y')
		),
		'pk' => array('id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'accounts' => array(
		'fd' => array(
			'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'short' => array('type' => 'char', 'precision' => 25, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
			'add1' => array('type' => 'varchar', 'precision' => 30),
			'add2' => array('type' => 'varchar', 'precision' => 30),
			'city' => array('type' => 'varchar', 'precision' => 50),
			'state' => array('type' => 'char', 'precision' => 2),
			'zip' => array('type' => 'char', 'precision' => 11),
			'contact' => array('type' => 'varchar', 'precision' => 50),
			'voice' => array('type' => 'char', 'precision' => 10),
			'fax' => array('type' => 'char', 'precision' => 10),
			'data1' => array('type' => 'char', 'precision' => 10),
			'data2' => array('type' => 'char', 'precision' => 10),
			'notes' => array('type' => 'varchar', 'precision' => 1024),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y')
		),
		'pk' => array('id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'actions' => array(
		'fd' => array(
			'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'short' => array('type' => 'char', 'precision' => 10, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y')
		),
		'pk' => array('id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'priorities' => array(
		'fd' => array(
			'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'short' => array('type' => 'char', 'precision' => 10, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
			'weight' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y')
		),
		'pk' => array('id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'products' => array(
		'fd' => array(
			'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'short' => array('type' => 'char', 'precision' => 10, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
			'reportto' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y'),
			'ticketsto' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'wosetid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'tcksetid' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'statuses' => array(
		'fd' => array(
			'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'short' => array('type' => 'char', 'precision' => 10, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y')
		),
		'pk' => array('id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'timecards' => array(
		'fd' => array(
			'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'jcn' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'actionon' => array('type' => 'date', 'nullable' => false),
			'inputon' => array('type' => 'timestamp', 'nullable' => false),
			'actionby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'status' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'action' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'hours' => array('type' => 'float', 'precision' => 8, 'nullable' => false),
			'summary' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
			'description' => array('type' => 'varchar', 'precision' => 1024),
			'revision' => array('type' => 'varchar', 'precision' => 20)
		),
		'pk' => array('id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'severities' => array(
		'fd' => array(
			'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'short' => array('type' => 'char', 'precision' => 10, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
			'weight' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y')
		),
		'pk' => array('id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'departments' => array(
		'fd' => array(
			'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'short' => array('type' => 'char', 'precision' => 10, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y')
		),
		'pk' => array('id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'workorders' => array(
		'fd' => array(
			'jcn' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'product' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'account' => array('type' => 'int', 'precision' => 4),
			'createby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'createdon' => array('type' => 'timestamp', 'nullable' => false),
			'closedby' => array('type' => 'int', 'precision' => 4),
			'closedon' => array('type' => 'date'),
			'status' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'statuson' => array('type' => 'timestamp'),
			'lastactionon' => array('type' => 'timestamp'),
			'deadlineon' => array('type' => 'date'),
			'eststarton' => array('type' => 'date'),
			'estendon' => array('type' => 'date'),
			'starton' => array('type' => 'date'),
			'esthours' => array('type' => 'float', 'precision' => 8),
			'totalhours' => array('type' => 'float', 'precision' => 8),
			'priority' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'severity' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'contact' => array('type' => 'varchar', 'precision' => 50),
			'contactphone' => array('type' => 'char', 'precision' => 10),
			'summary' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
			'notes' => array('type' => 'varchar', 'precision' => 1024),
			'description' => array('type' => 'varchar', 'precision' => 1024, 'nullable' => false),
			'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'revision' => array('type' => 'varchar', 'precision' => 20),
			'publicview' => array('type' => 'bool'),
			'etchours' => array('type' => 'float', 'precision' => 8)
		),
		'pk' => array('jcn', 'seq'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'projects' => array(
		'fd' => array(
			'projectid' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
			'reportto' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'createdby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'createdon' => array('type' => 'timestamp', 'nullable' => false),
			'projectdeadline' => array('type' => 'date'),
			'description' => array('type' => 'varchar', 'precision' => 1024),
			'status' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'lastactivity' => array('type' => 'timestamp'),
			'finalclose' => array('type' => 'date'),
			'parentprojectid' => array('type' => 'int', 'precision' => 4, 'nullable' => false, 'default' => 0)
		),
		'pk' => array('projectid'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array('name')
	),
	'projectmap' => array(
		'fd' => array(
			'projectid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'jcn' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('projectid', 'jcn', 'seq'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'watches' => array(
		'fd' => array(
			'watchid' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'typeid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'whatid1' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'whatid2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'whoid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'actions' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('watchid'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'tickets' => array(
		'fd' => array(
			'ticketid' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'product' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'account' => array('type' => 'int', 'precision' => 4),
			'createdby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'createdon' => array('type' => 'timestamp'),
			'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'closedby' => array('type' => 'int', 'precision' => 4),
			'closedon' => array('type' => 'timestamp'),
			'status' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'statuson' => array('type' => 'timestamp', 'nullable' => false),
			'lastactionon' => array('type' => 'timestamp'),
			'priority' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'type' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'contact' => array('type' => 'varchar', 'precision' => 80),
			'contactphone' => array('type' => 'varchar', 'precision' => 20),
			'issue' => array('type' => 'text', 'nullable' => false),
			'version' => array('type' => 'varchar', 'precision' => 20),
			'summary' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
			'seconds' => array('type' => 'int', 'precision' => 4, 'default' => 0, 'nullable' => false),
			'contactemail' => array('type' => 'varchar', 'precision' => 100),
			'module_id' => array('type' => 'int', 'precision' => 4)
		),
		'pk' => array('ticketid'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'ticketresolutions' => array(
		'fd' => array(
			'resid' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'ticketid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'loggedby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'loggedon' => array('type' => 'timestamp'),
			'status' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'resolution' => array('type' => 'text', 'nullable' => false),
			'startedon' => array('type' => 'timestamp')
		),
		'pk' => array('resid'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'faq' => array(
		'fd' => array(
			'faqid' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 80, 'nullable' => false),
			'createby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'createon' => array('type' => 'timestamp', 'nullable' => false),
			'modifyby' => array('type' => 'int', 'precision' => 4),
			'modifyon' => array('type' => 'timestamp'),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y', 'nullable' => false),
			'description' => array('type' => 'text')
		),
		'pk' => array('faqid'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'faqtopics' => array(
		'fd' => array(
			'topicid' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'faqid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 80, 'nullable' => false),
			'description' => array('type' => 'text', 'nullable' => false),
			'createby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'createon' => array('type' => 'timestamp', 'nullable' => false),
			'modifyby' => array('type' => 'int', 'precision' => 4),
			'modifyon' => array('type' => 'timestamp'),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y', 'nullable' => false)
		),
		'pk' => array('topicid'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'faqquestions' => array(
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
	),
	'faqanswers' => array(
		'fd' => array(
			'answerid' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'questionid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'answertext' => array('type' => 'text', 'nullable' => false),
			'createby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'createon' => array('type' => 'timestamp', 'nullable' => false),
			'modifyby' => array('type' => 'int', 'precision' => 4),
			'modifyon' => array('type' => 'timestamp'),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y', 'nullable' => false)
		),
		'pk' => array('answerid'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'attributesets' => array(
		'fd' => array(
			'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'short' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
			'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y', 'nullable' => false)
		),
		'pk' => array('id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'attributesetsmap' => array(
		'fd' => array(
			'setid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'typeid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'keyid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'weight' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('setid', 'typeid', 'keyid'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	),
	'dcl_config' => array(
		'fd' => array(
			'dcl_config_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
			'dcl_config_field' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false),
			'dcl_config_int' => array('type' => 'int', 'precision' => 4),
			'dcl_config_double' => array('type' => 'float', 'precision' => 8),
			'dcl_config_date' => array('type' => 'date'),
			'dcl_config_datetime' => array('type' => 'timestamp'),
			'dcl_config_varchar' => array('type' => 'varchar', 'precision' => 255)
		),
		'pk' => array('dcl_config_name'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	)
);
?>