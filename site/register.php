<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Registreer</title>
	<link href="ESF.css" rel="stylesheet" type="text/css" />
	<link href="stylesheets/bootstrap-combined.min.css" rel="stylesheet" type="text/css" />
	<link href="style.css" rel="stylesheet" type="text/css" />

	<script src='scripts/register.js'></script>
</head>
<body>
	<? include('header.php'); ?>
	Register
	<? include('header2.php'); ?>
	<div class="contentWrapper">
		<form name="register" id="register" action="register_db.php" method="post">
			<input type="text" name="firstName" placeholder="First name..." required></br>
			<input type="text" name="lastName" placeholder="Last name..." required></br>
			<input type="email" id="email" name="email" placeholder="Email..." required></br>
			<input type="email" id="email2" name="email2" placeholder="Confirm email..." oninput="checkEmail(this)" required></br>
			<input type="password" id="password" name="password" placeholder="Password..." required></br>
			<input type="password" id="password2" name="password2" placeholder="Confirm password..." oninput="checkPassword(this)" required><br>
			<input type="hidden" name="session" value="<? session_start(); echo session_id() ?>" />
			<input type="submit" value="Registreer!" class="btn btn-success centered">
		</form>
	</div>
	<div class="footerWrapper">
	</div>
</body>
</html>
