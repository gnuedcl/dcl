<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Set-Cookie" content="DCLINFO=;expires=Sunday, 31-Dec-2000 23:59:59 GMT">
	<meta http-equiv="Expires" content="-1">
	<title>{$TXT_TITLE|escape}</title>
	<link rel="stylesheet" href="{$DIR_VENDOR}bootstrap/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="{$DIR_VENDOR}bootstrap/css/bootstrap-theme.min.css" type="text/css">
	<style>body { padding-top: 20px; background: url('{$DIR_IMG}login.jpg') no-repeat center center fixed; background-size: cover; }</style>
</head>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading"> <strong>{$VAL_WELCOME|escape}</strong></div>
				<div class="panel-body">
					{if $VAL_ERROR}<div class="alert alert-warning fade in" role="alert">
						<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						{$VAL_ERROR|escape}
					</div>{/if}
					<form class="form-horizontal" role="form" method="post" action="login.php" autocomplete="off">
						<div class="form-group">
							<label for="UID" class="col-sm-3 control-label">{$TXT_USER|escape}</label>
							<div class="col-sm-9">
								<input class="form-control" id="UID" name="UID" placeholder="{$TXT_USER|escape}" required>
							</div>
						</div>
						<div class="form-group">
							<label for="PWD" class="col-sm-3 control-label">{$TXT_PASSWORD|escape}</label>
							<div class="col-sm-9">
								<input class="form-control" id="PWD" name="PWD" placeholder="{$TXT_PASSWORD|escape}" required type="password">
							</div>
						</div>
						<div class="form-group last">
							<div class="col-sm-offset-3 col-sm-9">
								<button type="submit" class="btn btn-success btn-sm">{$BTN_LOGIN|escape}</button>
								<a href="reset.php" class="btn btn-link">Forgot Your Password?</a>
							</div>
						</div>
					</form>
				</div>
				<div class="panel-footer">Powered By <a target="_blank" href="http://www.gnuenterprise.org/">GNU Enterprise</a> <a target="_blank" href="http://dcl.sourceforge.net/">DCL</a> {$TXT_VERSION|escape}<br>Copyright (C) 1999-2014 <a target="_blank" href="http://www.fsf.org/">Free Software Foundation</a></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="{$DIR_JS}/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#UID").focus();
	})
</script>
</body></html>
