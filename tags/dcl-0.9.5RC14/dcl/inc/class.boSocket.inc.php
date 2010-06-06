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

class boSocket
{
	var $sHost;
	var $iPort;
	var $iTimeout;
	var $iErr;
	var $sErr;
	var $sResponse;
	var $sResponseMode;
	var $hSocket;
	var $bDebug;

	function boSocket()
	{
		$this->sHost = '';
		$this->iPort = 0;
		$this->iTimeout = 30;
		$this->iErr = 0;
		$this->sErr = '';
		$this->sResponse = '';
		$this->sResponseMode = ''; // To change reading behavior
		$this->hSocket = 0;
		$this->bDebug = false;
	}

	function Connect($bResponse = false)
	{
		$this->hSocket = fsockopen($this->sHost, $this->iPort, $this->iErr, $this->sErr, $this->iTimeout);
		if (!$this->hSocket)
		{
			trigger_error('fsockopen(' . $this->sHost . ', ' .  $this->iPort . ', &$this->iErr, &$this->sErr, ' . $this->iTimeout . '):&nbsp;' . '(' . $this->iErr . ')&nbsp;' . $this->sErr);
			return -1;
		}

		if ($bResponse)
			$this->Read();

		return 1;
	}

	function Disconnect()
	{
		if ($this->hSocket)
			fclose($this->hSocket);
	}

	function Write($sValue, $bResponse = false)
	{
		if ($this->bDebug)
			echo '&gt;&gt;&gt; ', htmlspecialchars($sValue), '<br>';

		fwrite($this->hSocket, $sValue);
		if ($bResponse)
			$this->Read();
	}

	function Read()
	{
		if ($this->hSocket)
		{
			$this->sResponse = fgets($this->hSocket, 1024);
			if ($this->bDebug)
				echo '&lt;&lt;&lt; ', htmlspecialchars($this->sResponse), '<br>';

			if ($this->sResponseMode == 'smtp')
			{
				while (strlen($this->sResponse) > 3 && $this->sResponse[3] == '-')
				{
					$this->sResponse = fgets($this->hSocket, 1024);
					if ($this->bDebug)
						echo '&lt;&lt;&lt; ', htmlspecialchars($this->sResponse), '<br>';
				}
			}
		}
	}
}
?>
