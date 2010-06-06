<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <!-- $Id$ -->
  <title>Double Choco Latte Installation</title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo _INSTALL_CHARSET ?>" />
  <link rel="stylesheet" type="text/css" media="all" href="style.css" />
</head>
<body style="margin: 0; padding: 0;">
<form action='index.php' method='post'>
<table style="width: 778px;" align="center" cellpadding="2" cellspacing="0">
	<tr><th class="formTitle">Double Choco Latte Installation</th></tr>
	<tr>
		<td class="formContainer">
			<table style="border: 0px none; width: 100%" cellpadding="2" cellspacing="0">
				<tr>
					<td width='5%'>&nbsp;</td>
					<td colspan="3"><?php if(!empty($title)) echo '<h4 style="margin-top: 10px; margin-bottom: 5px; padding: 10px;">'.$title.'</h4>'; echo '<div style="padding: 10px;">'.$content.'</div>'; ?></td>
					<td width='5%'>&nbsp;</td>
				</tr>
				<tr class="formFooter">
					<td width='5%'>&nbsp;</td>
					<td width='35%' align='left'><?php echo b_back($b_back); ?></td>
					<td width='20%' align='center'><?php echo b_reload($b_reload); ?></td>
					<td width='35%' align='right'><?php echo b_next($b_next); ?></td>
					<td width='5%'>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
</body>
</html>
<?php
function b_back($option = null)
{
    if(!isset($option) || !is_array($option)) return '';
    $content = '';
    if(isset($option[0]) && $option[0] != ''){
        $content .= "<input type='button' value='"
            ._INSTALL_L42."' onclick=\"location='index.php?op="
            .htmlspecialchars($option[0])."'\" /> ";
    }else{
        $content .= "<input type='button' value='"
            ._INSTALL_L42."' onclick=\"javascript:history.back();\" /> ";
    }
    if(isset($option[1]) && $option[1] != ''){
        $content .= "<span style='font-size:85%;'><< "
                .htmlspecialchars($option[1])."</span> ";
    }
    return $content;
}

function b_reload($option=''){
    if(empty($option)) return '';
    return  "<input type='button' value='"
        .'reload'."' onclick=\"location.reload();\" /> ";
}

function b_next($option=null){
    if(!isset($option) || !is_array($option)) return '';
    $content = '';
    if(isset($option[1]) && $option[1] != ''){
        $content .= "<span style='font-size:85%;'>"
                .htmlspecialchars($option[1])." >></span>";
    }
    $content .= "<input type='hidden' name='op' value='"
                .htmlspecialchars($option[0])."' />\n";
    $content .= "<input type='submit' name='submit' value='"._INSTALL_L47."' />\n";
    return $content;
}
?>
