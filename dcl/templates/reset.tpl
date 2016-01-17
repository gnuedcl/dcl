<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Set-Cookie" content="DCLINFO=;expires=Sunday, 31-Dec-2000 23:59:59 GMT">
	<meta http-equiv="Expires" content="-1">
	<title>{$TXT_TITLE}</title>
	<script language="JavaScript">function init(){ document.getElementById("login").focus(); }</script>
	<link rel="stylesheet" href="{$DIR_VENDOR}bootstrap/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="{$DIR_VENDOR}bootstrap/css/bootstrap-theme.min.css" type="text/css">
	<style>body { padding-top: 20px; background: url('{$DIR_IMG}login.jpg') no-repeat center center fixed; background-size: cover; }</style>
</head>
<body onload="init();">
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading"> <strong>Reset Your Password</strong></div>
				<div class="panel-body">
					<p>
						Enter your user name and email address below.  An email will be sent to your account with further instructions.
					</p>
					<form class="form-horizontal" role="form" method="post" action="reset.php" autocomplete="off">
						<div class="form-group">
							<label for="login" class="col-sm-3 control-label">User</label>
							<div class="col-sm-9">
								<input class="form-control" id="login" name="login" placeholder="User" required type="text">
							</div>
						</div>
						<div class="form-group">
							<label for="email" class="col-sm-3 control-label">Email</label>
							<div class="col-sm-9">
								<input class="form-control" id="email" name="email" placeholder="Email" required type="text">
							</div>
						</div>
						<div class="form-group last">
							<div class="col-sm-offset-3 col-sm-9">
								<button type="submit" class="btn btn-primary">Reset My Password</button>
								<a href="index.php" class="btn btn-link">Return to Login</a>
							</div>
						</div>
					</form>
				</div>
				<div class="panel-footer">Powered By <a target="_blank" href="http://www.gnuenterprise.org/">GNU Enterprise</a> <a target="_blank" href="http://dcl.sourceforge.net/">DCL</a> {$TXT_VERSION|escape}<br>Copyright (C) 1999-2014 <a target="_blank" href="http://www.fsf.org/">Free Software Foundation</a></div>
			</div>
		</div>
	</div>
</div>
</body></html>
