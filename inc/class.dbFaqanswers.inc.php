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

LoadStringResource('db');
class dbFaqanswers extends dclDB
{
	function dbFaqanswers()
	{
		parent::dclDB();
		$this->TableName = 'faqanswers';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}

	function Add()
	{
		$this->createon = DCL_NOW;
		return parent::Add();
	}

	function Edit()
	{
		$this->modifyby = $GLOBALS['DCLID'];
		$this->modifyon = DCL_NOW;
		return parent::Edit(array('createby', 'createon'));
	}

	function Delete()
	{
		return parent::Delete(array('answerid' => $this->answerid));
	}

	function Load($id)
	{
		return parent::Load(array('answerid' => $id));
	}

	function LoadByQuestionID($id, $orderby = 'createon desc')
	{
		if (($id = DCL_Sanitize::ToInt($id)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}
		
		$this->Clear();

		$sql = 'SELECT answerid, questionid, answertext, createby, ';
		$sql .= $this->ConvertTimestamp('createon', 'createon');
		$sql .= ', modifyby, ';
		$sql .= $this->ConvertTimestamp('modifyon', 'modifyon');
		$sql .= ", active FROM faqanswers WHERE questionid=$id";
		if ($orderby != '')
			$sql .= " ORDER BY $orderby";

		if (!$this->Query($sql))
			return -1;

		return 1;
	}
		
	function DeleteByQuestion($id)
	{
		if (($id = DCL_Sanitize::ToInt($id)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}

		$oDB = new dclDB;
		if ($oDB->Query("SELECT answerid FROM faqanswers WHERE questionid = $id") == -1)
		{
			return -1;
		}
		
		while ($oDB->next_record())
		{
			$this->answerid = $this->f(0);
			if ($this->Delete() == -1)
			{
				return -1;
			}
		}
		
		return 1;
	}
}
?>
