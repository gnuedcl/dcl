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

function smarty_function_dcl_selector_project($params, &$smarty)
{
	if (!isset($params['name']))
	{
		$smarty->trigger_error('dcl_selector_project: missing parameter name');
		return;
	}

	if (!isset($params['id']))
		$params['id'] = $params['name'];

	if (!isset($params['value']))
		$params['value'] = '';

	if (!isset($params['decoded']))
		$params['decoded'] = '';

	$sArrayName = 'a_' . $params['name'];
?>
<script language="JavaScript">
var <?php echo $sArrayName; ?> = new Array();
<?php
	if ($params['decoded'] != '')
	{
		echo $sArrayName . '[0] = "' . str_replace('"', '\"', $params['decoded']) . '";' . "\n";
	}
?>
function render_<?php echo $sArrayName; ?>()
{
	if (<?php echo $sArrayName; ?>.length > 0)
		document.getElementById("<?php echo $params['id']; ?>Link").innerHTML = <?php echo $sArrayName; ?>[0];
	else
		document.getElementById("<?php echo $params['id']; ?>Link").innerHTML = '<?php echo STR_CMMN_SELECTONE; ?>';
}
</script>
<a id="<?php echo $params['id']; ?>Link" href="javascript:;" onclick="showSelector(document.getElementById('<?php echo $params['id']; ?>'), <?php echo $sArrayName; ?>, render_<?php echo $sArrayName; ?>, 'htmlProjectSelector', 'false');"><?php echo $params['decoded'] == '' ? STR_CMMN_SELECTONE : htmlspecialchars($params['decoded'], ENT_QUOTES); ?></a>
<input type="hidden" id="<?php echo $params['id']; ?>" name="<?php echo $params['name']; ?>" value="<?php echo $params['value']; ?>">
<?php
}
?>
