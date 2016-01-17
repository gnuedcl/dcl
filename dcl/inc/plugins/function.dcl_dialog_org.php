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

function smarty_function_dcl_dialog_org($params, &$smarty)
{
	if (!isset($params['dialogId']))
		$params['dialogId'] = 'dialog';

	$dialogId = $params['dialogId'];

	if (!isset($params['saveButtonId']))
		$params['saveButtonId'] = 'saveOrganizations';

	$saveButtonId = $params['saveButtonId'];

	if (!isset($params['gridId']))
		$params['gridId'] = 'grid';

	$gridId = $params['gridId'];

	if (!isset($params['pager']))
		$params['pager'] = 'pager';

	$pagerId = $params['pager'];
?>
	<div id="<?php echo $dialogId; ?>" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<table id="<?php echo $gridId; ?>"></table>
				<div id="<?php echo $pagerId; ?>"></div>
			</div>
			<div class="modal-footer">
				<button id="<?php echo $saveButtonId; ?>" type="button" class="btn btn-primary" data-dismiss="modal">Save</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<?php
}