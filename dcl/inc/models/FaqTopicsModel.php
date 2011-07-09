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

LoadStringResource('db');
class FaqTopicsModel extends dclDB
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'faqtopics';
		LoadSchema($this->TableName);

		$this->foreignKeys = array('faqquestions' => 'topicid');
		
		parent::Clear();
	}

	public function Add()
	{
		$this->AdjustSeq($this->seq);
		$this->createon = DCL_NOW;
		return parent::Add();
	}

	public function AdjustSeq($fromThisSeq, $editID = 0)
	{
		if (($fromThisSeq = Filter::ToInt($fromThisSeq)) === null ||
			($editID = Filter::ToInt($editID) === null))
		{
			throw new InvalidDataException();
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

	public function Edit()
	{
		if ($this->AdjustSeq($this->seq, $this->topicid) == -1)
		{
			return -1;
		}
		
		$this->modifyby = $GLOBALS['DCLID'];
		$this->modifyon = DCL_NOW;
		return parent::Edit(array('createby', 'createon'));
	}

	public function Delete()
	{
		$o = new FaqQuestionsModel();
		if ($o->DeleteByTopic($this->topicid) == -1)
		{
			return -1;
		}
		
		return parent::Delete(array('topicid' => $this->topicid));
	}

	public function Load($id)
	{
		return parent::Load(array('topicid' => $id));
	}

	public function LoadByFaqID($id, $orderby = 'seq')
	{
		if (($id = Filter::ToInt($id)) === null)
		{
			throw new InvalidDataException();
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
	
	public function DeleteByFaq($id)
	{
		if (($id = Filter::ToInt($id)) === null)
		{
			throw new InvalidDataException();
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
