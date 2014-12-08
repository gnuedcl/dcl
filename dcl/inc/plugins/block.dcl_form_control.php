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

function smarty_block_dcl_form_control($params, $content, $template, &$repeat)
{
	$inputGroup = isset($params['inputGroup']) && $params['inputGroup'] == true;

	if (is_null($content) && $repeat) {
		$required = isset($params['required']) && $params['required'] == true;
		$labelUsed = isset($params['label']) && $params['label'] != '';
		$controlSizeUsed = isset($params['controlsize']) && $params['controlsize'] != '';
	?>	<div class="form-group<?php if ($required) {?> required<?php } ?>"<?php if ($required) {?> data-required="required"<?php } ?>>
			<?php if ($labelUsed) {?><label for="<?php echo $params['id']; ?>" class="col-sm-2 control-label"><?php echo htmlspecialchars($params['label'], ENT_QUOTES, 'UTF-8'); ?></label><?php } ?>
			<div<?php if ($controlSizeUsed || $inputGroup) { ?> class="<?php if ($controlSizeUsed) { ?>col-sm-<?php echo $params['controlsize']; ?><?php } ?><?php if ($inputGroup) { ?> inputGroupContainer<?php } ?>"<?php } ?>>
			<?php if ($inputGroup) { ?><div class="input-group"><?php }

		return;
	}
	else if (!$repeat)
	{
		$help = '';
		if (isset($params['help']))
			$help = '<div class="col-sm-offset-2 col-sm-10"><span class="help-block">' . htmlspecialchars($params['help'], ENT_QUOTES, 'UTF-8') . '</span></div>';

		$closeInputGroup = $inputGroup ? '</div>' : '';

		echo $content, '</div>', $closeInputGroup, $help, '</div>';
	}
}