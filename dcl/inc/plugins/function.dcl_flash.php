<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2015 Free Software Foundation
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

function smarty_function_dcl_flash($params, Smarty_Internal_Template $smarty)
{
    global $g_oSession;

    if ($g_oSession->IsRegistered('REDIRECT_TEXT'))
    {
        $title = htmlspecialchars($g_oSession->Value('REDIRECT_TITLE'), ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($g_oSession->Value('REDIRECT_TEXT'), ENT_QUOTES, 'UTF-8');
?>
        <div class="dcl-notification" title="<?php echo $title; ?>"><?php echo $message; ?></div>
<?php
        $g_oSession->Unregister('REDIRECT_TITLE');
        $g_oSession->Unregister('REDIRECT_TEXT');
        $g_oSession->Edit();
    }
}