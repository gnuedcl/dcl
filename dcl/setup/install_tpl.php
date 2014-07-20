<!DOCTYPE html>
<html>
<head>
	<title>Double Choco Latte Installation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" media="all" href="../vendor/bootstrap/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" media="all" href="../vendor/bootstrap/css/bootstrap-theme.min.css" />
	<style type="text/css">
		div.row.top12 { margin-top: 12px; }
		body { background-color: #eaeaea; padding-top: 50px; }
		div.container { background-color: #ffffff; border-radius: 4px; padding-bottom: 12px; }
	</style>
</head>
<body>
<form action='index.php' method='post'>
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h2>Double Choco Latte Installation</h2>
			</div>
		</div>
		<div class="row top12">
			<div class="col-xs-12">
			<?php if(!empty($title)) echo '<h4>' . $title . '</h4>'; echo '<p>' . $content . '</p>'; ?>
			</div>
		</div>
		<div class="row top12">
			<div class="col-xs-1">
				<?php echo b_back($b_back); ?>
			</div>
			<div class="col-xs-1">
				<?php echo b_reload($b_reload); ?>
			</div>
			<div class="col-xs-1">
				<?php echo b_next($b_next); ?>
			</div>
		</div>
	</div>
</form>
</body>
</html>
<?php
function b_back($option = null)
{
    if (!isset($option) || !is_array($option))
		return '';

    $content = '';
	$href = (isset($option[0]) && $option[0] != '') ? htmlspecialchars($option[0], ENT_QUOTES, 'UTF-8') : 'javascript:history.back();';
	$content .= '<a class="btn btn-default" href="index.php?op=' . $href . '"><span class="glyphicon glyphicon-backward"></span> ' . _INSTALL_L42 . '</a>';

    return $content;
}

function b_reload($option='')
{
    if(empty($option))
		return '';

	return '<a class="btn btn-default" href="javascript:location.reload();"><span class="glyphicon glyphicon-refresh"></span> Reload</a>';
}

function b_next($option=null)
{
    if(!isset($option) || !is_array($option))
		return '';

	$content = '';
    $content .= "<input type='hidden' name='op' value='" . htmlspecialchars($option[0], ENT_QUOTES, 'UTF-8') . "' />\n";
    $content .= '<input class="btn btn-primary" type="submit" name="submit" value="' . _INSTALL_L47 . "\" />\n";

    return $content;
}
