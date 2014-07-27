<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

function smarty_function_dcl_modal($params, &$smarty)
{
	$defaults = array('id' => 'dialog', 'class' => '', 'title' => '', 'content' => '', 'escape' => true);

	$attributes = array_merge($defaults, $params);

	$attributes['class'] .= ' modal fade';

	if ($attributes['escape'] == true && $attributes['content'] != '')
		$attributes['content'] = htmlspecialchars($attributes['content'], ENT_QUOTES, 'UTF-8');
?>
<div id="<?php echo $attributes['id']; ?>" class="<?php echo $attributes['class']; ?>">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title"><?php echo htmlspecialchars($attributes['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
			</div>
			<div class="modal-body">
				<p><?php echo $attributes['content']; ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<?php } ?>
