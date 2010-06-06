<?php
LoadStringResource('db');
class dbSccs extends dclDB
{
	function dbSccs()
	{
		parent::dclDB();
		$this->TableName = 'dcl_sccs';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	function LoadByPath($sPath)
	{
		$sSQL = 'SELECT ' . $this->SelectAllColumns() . ' FROM dcl_sccs WHERE ';
		$sSQL .= $this->GetUpperSQL('sccs_repository') . ' = ' . $this->Quote(strtoupper($sPath));

		if ($this->Query($sSQL) == -1 || !$this->next_record())
			return -1;
		
		return $this->GetRow();
	}
}
?>