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

function smarty_function_dcl_notifications($params, Smarty_Internal_Template $smarty)
{
    $notifications = NotificationCollectionModel::GetInstance()->Items();
    foreach ($notifications as $notification) {
        ?><div class="alert alert-<?php echo $notification->Type == DCL_LOG_ERROR ? 'danger' : ($notification->Type == DCL_LOG_WARN ? 'warning' : 'info'); ?> fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
            <?php echo htmlspecialchars($notification->Message, ENT_QUOTES, 'UTF-8'); ?>
        </div><?php
    }
}