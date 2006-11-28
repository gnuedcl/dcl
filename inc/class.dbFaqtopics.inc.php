<?php
/*
 * $Id: class.dbFaqtopics.inc.php,v 1.1.1.1 2006/11/27 05:30:51 mdean Exp $
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
class dbFaqtopics extends dclDB
{
	function dbFaqtopics()
	{
		parent::dclDB();
		$this->TableName = 'faqtopics';
		LoadSchema($this->TableName);

		$this->foreignKeys = array('faqquestions' => 'topicid');
		
		parent::Clear();
	}

	function Add()
	{
		$this->AdjustSeq($this->seq);
		$this->createon = 'now()';
		return parent::Add();
	}

	function AdjustSeq($fromThisSeq, $editID = 0)
	{
		if (($fromThisSeq = DCL_Sanitize::ToInt($fromThisSeq)) === null ||
			($editID = DCL_Sanitize::ToInt($editID) === null))
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}
		
		$query = "SELECT topicid FROM faqtopics WHERE seq=$fromThisSeq and faqid=" . $this->faqid;
		if ($editID > 0)
			$query .= " AND topicid != $editID";
		$this->Query($query);
           // There is one with this weight and not this ID, so adjust it
		if ($this->next_record())
		{
			$thisID = $this->f('topicid');
			$this->FreeResult();
			$this->AdjustSeq($fromThisSeq + 1);
			$query = "UPDATE faqtopics SET seq=seq+1 WHERE topicid=$thisID";
			$this->Execute($query);
		}
	}

	function Edit()
	{
		if ($this->AdjustSeq($this->seq, $this->topicid) == -1)
		{
			return -1;
		}
		
		$this->modifyby = $GLOBALS['DCLID'];
		$this->modifyon = 'now()';
		return parent::Edit(array('createby', 'createon'));
	}

	function Delete()
	{
		$o =& CreateObject('dcl.dbFaqquestions');
		if ($o->DeleteByTopic($this->topicid) == -1)
		{
			return -1;
		}
		
		return parent::Delete(array('topicid' => $this->topicid));
	}

	function Load($id)
	{
		return parent::Load(array('topicid' => $id));
	}

	function LoadByFaqID($id, $orderby = 'seq')
	{
		if (($id = DCL_Sanitize::ToInt($id)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}
		
		$this->Clear();

		$sql = 'SELECT topicid, seq, faqid, name, description, createby, ';
		$sql .= $this->ConvertTimestamp('createon', 'createon');
		$sql .= ', modifyby, ';
		$sql .= $this->ConvertTimestamp('modifyon', 'modifyon');
		$sql .= ", active FROM faqtopics WHERE faqid=$id";
		if ($orderby != '')
			$sql .= " ORDER BY $orderby";

		if (!$this->Query($sql))
			return -1;

		return 1;
	}
	
	function DeleteByFaq($id)
	{
		if (($id = DCL_Sanitize::ToInt($id)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}

		$oDB = new dclDB;
		if ($oDB->Query("SELECT topicid FROM faqtopics WHERE faqid = $id") == -1)
		{
			return -1;
		}
		
		while ($oDB->next_record())
		{
			$this->topicid = $this->f(0);
			if ($this->Delete() == -1)
			{
				return -1;
			}
		}
		
		return 1;
	}
}
?>
