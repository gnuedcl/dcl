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

function smarty_function_dcl_selector_org($params, &$smarty)
{
	if (!isset($params['name']))
	{
		$smarty->trigger_error('dcl_selector_org: missing parameter name');
		return;
	}

	if (!isset($params['id']))
		$params['id'] = $params['name'];

	if (!isset($params['value']))
		$params['value'] = '';
	else if (is_array($params['value']))
		$params['value'] = join($params['value'], ',');

	if (!isset($params['decoded']))
		$params['decoded'] = '';

	if (!isset($params['multiple']))
		$params['multiple'] = 'N';
	else if ($params['multiple'] == 'true')
		$params['multiple'] = 'Y';
	else if ($params['multiple'] == 'false')
		$params['multiple'] = 'N';
		
	if (!isset($params['text']))
		$params['text'] = STR_CMMN_SELECTONE;
		
	if (!isset($params['window_name']))
		$params['window_name'] = '_dcl_selector_';

	$sArrayName = 'a_' . $params['name'];
?>
<script language="JavaScript">
var <?php echo $sArrayName; ?> = new Array();
<?php
	$decoded = $params['decoded'];
	if (is_array($params['decoded']))
	{
		foreach ($params['decoded'] as $aValue)
			echo $sArrayName . '.push("' . str_replace('"', '\"', $aValue) . '");' . "\n";
			
		$decoded = join(';', $params['decoded']);
	}
	else if ($params['decoded'] != '')
	{
		echo $sArrayName . '[0] = "' . str_replace('"', '\"', $params['decoded']) . '";' . "\n";
	}
?>
function render_<?php echo $sArrayName; ?>()
{
<?php if ($params['multiple'] == 'Y') { ?>
	renderItems(document.getElementById("div_<?php echo $params['name']; ?>"), <?php echo $sArrayName; ?>);
<?php } else { ?>
	if (<?php echo $sArrayName; ?>.length > 0)
		document.getElementById("<?php echo $params['id']; ?>Link").innerHTML = <?php echo $sArrayName; ?>[0];
	else
		document.getElementById("<?php echo $params['id']; ?>Link").innerHTML = '<?php echo $params['text']; ?>';
<?php } ?>
}
</script>
<a id="<?php echo $params['id']; ?>Link" href="javascript:;" onclick="showSelector(document.getElementById('<?php echo $params['id']; ?>'), <?php echo $sArrayName; ?>, render_<?php echo $sArrayName; ?>, 'htmlOrganizationSelector', '<?php echo $params['multiple'] == 'Y' ? 'true' : 'false' ?>', '<?php echo $params['window_name']; ?>');"><?php echo $params['multiple'] == 'Y' || $params['decoded'] == '' ? htmlspecialchars($params['text'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($decoded, ENT_QUOTES, 'UTF-8'); ?></a>
<input type="hidden" id="<?php echo $params['id']; ?>" name="<?php echo $params['name']; ?>" value="<?php print(is_array($params['value']) ? join(',', $params['value']) : $params['value']); ?>">
<?php
}
?>
