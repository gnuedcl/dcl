<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge;chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
Dear {$VAL_FIRSTNAME},
<br><br>
A request to reset your password was received.  If you made this request, please click the following link to reset your password:
<br><br>
<a href="{dcl_config name=DCL_ROOT}reset.php?token={$VAL_TOKEN}">{dcl_config name=DCL_ROOT}reset.php?token={$VAL_TOKEN}</a>
<br><br>
If you did not make this request, please delete this message.  Your password will remain unchanged.
</body>
</html>