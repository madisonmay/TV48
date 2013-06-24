<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>ESF</title>
	<link href="ESF.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div class="contentWrapper">
		<?
		include ("ESF_config.php");

		if ($_GET)
		{
			$code = $_GET["code"];
			$email = $_GET["email"];

			$mysqli = new mysqli($server, $username, $password, $database);
			if ($mysqli->connect_errno)
			{
				printf("Connect failed: %s\n", $mysqli->connect_error);
				exit();
			}

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE ESF_users SET confirmed = 1 WHERE email = ? AND confirmationCode = ?");
			$stmt->bind_param('ss', $email, $code);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			?>
			<h1> Registratie compleet </h1>
			<?
			header ('Location: login.php');


		} else
		{
			?>
			<h1>Error</h1>
			<p>U heeft geen toegang tot deze pagina</p>
			<?
			header ('Location: login.php');
		}
		?>
	</div>
	<div class="footerWrapper">
	footer
	</div>
</body>
</html>