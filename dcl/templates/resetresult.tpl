<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Set-Cookie" content="DCLINFO=;expires=Sunday, 31-Dec-2000 23:59:59 GMT">
	<meta http-equiv="Expires" content="-1">
	<title>Password Reset</title>
	<link rel="stylesheet" href="{$DIR_VENDOR}bootstrap/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="{$DIR_VENDOR}bootstrap/css/bootstrap-theme.min.css" type="text/css">
	<style>body { padding-top: 20px; background: url('{$DIR_IMG}login.jpg') no-repeat center center fixed; background-size: cover; }</style>
</head>
<body onload="init();">
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<div class="panel panel-default">
				<div class="panel-heading"> <strong class="">Password Reset Request</strong></div>
				<div class="panel-body">
					<p>
						If the information you provided was correct, an email was sent to your account containing further instructions.  If you do not receive the email within a few minutes,
						be sure to check your junk and spam folders for the email.
					</p>
					<div class="form-group last">
						<div class="col-sm-offset-3 col-sm-9">
							<a href="index.php" class="btn btn-primary">Return to Login</a>
						</div>
					</div>
				</div>
				<div class="panel-footer">Powered By <a target="_blank" href="http://www.gnuenterprise.org/">GNU Enterprise</a> <a target="_blank" href="http://dcl.sourceforge.net/">DCL</a> {$TXT_VERSION|escape}<br>Copyright (C) 1999-2014 <a target="_blank" href="http://www.fsf.org/">Free Software Foundation</a></div>
			</div>
		</div>
	</div>
</div>
</body></html>
