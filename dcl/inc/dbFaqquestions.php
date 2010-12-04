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
class dbFaqquestions extends dclDB
{
	function dbFaqquestions()
	{
		parent::dclDB();
		$this->TableName = 'faqquestions';
		LoadSchema($this->TableName);

		$this->foreignKeys = array('faqanswers' => 'questionid');
		
		parent::Clear();
	}

	function Add()
	{
		if ($this->AdjustSeq($this->seq) == -1)
		{
			return;
		}
		
		$this->createon = DCL_NOW;
		return parent::Add();
	}

	function AdjustSeq($fromThisSeq, $editID = 0)
	{
		if (($fromThisSeq = DCL_Sanitize::ToInt($fromThisSeq)) === null ||
			($editID = DCL_Sanitize::ToInt($editID)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}

		$query = "SELECT questionid FROM faqquestions WHERE seq=$fromThisSeq and topicid=" . $this->topicid;
		if ($editID > 0)
			$query .= " AND questionid != $editID";
		
		$this->Query($query);
        
		// There is one with this seq and not this ID, so adjust it
		if ($this->next_record())
		{
			$thisID = $this->f('questionid');
			$this->FreeResult();
			$this->AdjustSeq($fromThisSeq + 1);
			$query = "UPDATE faqquestions SET seq=seq+1 WHERE questionid=$thisID";
			$this->Execute($query);
		}
	}

	function Edit()
	{
		if ($this->AdjustSeq($this->seq, $this->questionid) == -1)
		{
			return;
		}
		
		$this->modifyby = $GLOBALS['DCLID'];
		$this->modifyon = DCL_NOW;
		return parent::Edit(array('createby', 'createon'));
	}

	function Delete()
	{
		$o = new dbFaqanswers();
		if ($o->DeleteByQuestion($this->questionid) == -1)
		{
			return -1;
		}
		
		return parent::Delete(array('questionid' => $this->questionid));
	}

	function Load($id)
	{
		return parent::Load(array('questionid' => $id));
	}

	function LoadByFaqTopicID($id, $orderby = 'seq')
	{
		if (($id = DCL_Sanitize::ToInt($id)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}
		
		$this->Clear();

		$sql = 'SELECT questionid, seq, topicid, questiontext, createby, ';
		$sql .= $this->ConvertTimestamp('createon', 'createon');
		$sql .= ', modifyby, ';
		$sql .= $this->ConvertTimestamp('modifyon', 'modifyon');
		$sql .= ", active FROM faqquestions WHERE topicid=$id";
		if ($orderby != '')
			$sql .= " ORDER BY $orderby";

		if (!$this->Query($sql))
			return -1;

		return 1;
	}
		
	function DeleteByTopic($id)
	{
		if (($id = DCL_Sanitize::ToInt($id)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}

		$oDB = new dclDB;
		if ($oDB->Query("SELECT questionid FROM faqquestions WHERE topicid = $id") == -1)
		{
			return -1;
		}
		
		while ($oDB->next_record())
		{
			$this->questionid = $this->f(0);
			if ($this->Delete() == -1)
			{
				return -1;
			}
		}
		
		return 1;
	}
}
