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

class CsvFileReader
{
    var $sFileName = '';
    var $hFile = null;
    var $bFirstLineHeader = false;
    var $aRecord = null;
    var $aHeaderRecord = null;
    var $sDelimiter = ',';
    var $sQualifier = '"';
    var $iMaxLineLength = 16384;
    
    public function __construct($sFileName = '', $bFirstLineHeader = false)
    {
        $this->sFileName = $sFileName;
        $this->bFirstLineHeader = $bFirstLineHeader;
        
        $this->aRecord = array();
    }
    
    public function Open()
    {
        $this->hFile = fopen($this->sFileName, 'r');
        $this->aRecord = array();
        
        if ($this->bFirstLineHeader)
        {
            $this->aHeaderRecord = fgetcsv($this->hFile, $this->iMaxLineLength, $this->sDelimiter, $this->sQualifier);
            for ($i = 0; $i < count($this->aHeaderRecord); $i++)
                $this->aHeaderRecord[trim($this->aHeaderRecord[$i])] = $i;
        }
    }
    
    public function Close()
    {
        fclose($this->hFile);
    }
    
    public function Read()
    {
        return (($this->aRecord = fgetcsv($this->hFile, $this->iMaxLineLength, $this->sDelimiter, $this->sQualifier)) !== false);
    }
    
    public function Value($vColumn)
    {
        if (is_int($vColumn))
            return trim($this->aRecord[$vColumn]);
            
        return trim($this->aRecord[$this->aHeaderRecord[$vColumn]]);
    }
}
