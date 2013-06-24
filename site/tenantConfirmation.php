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
			$pid = $_GET['property_id']

			$mysqli = new mysqli($server, $username, $password, $database);
			if ($mysqli->connect_errno)
			{
				printf("Connect failed: %s\n", $mysqli->connect_error);
				exit();
			}

			//Needs error handling
			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE ESF_users SET confirmed = 1, property_id = ? WHERE email = ? AND confirmationCode = ?");
			$stmt->bind_param('iss', $pid, $email, $code);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("SELECT `id`, `tenant_id` FROM `ESF_users` WHERE email = ? AND confirmationCode = ?");
			$stmt->bind_param('ss', $email, $code);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($user_id, $tenant_id);
			$stmt->fetch();
			$stmt->close();

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE Tenants SET user_id = ? WHERE id = ?");
			$stmt->bind_param('ii', $user_id, $tenant_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE ESF_users SET confirmed = 1, property_id = ? WHERE email = ? AND confirmationCode = ?");
			$stmt->bind_param('iss', $pid, $email, $code);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			?>
			<h1> Registratie compleet </h1>
			<?
			header ('Location: newTenant.php');
			exit(0);

		} else {
			header ('Location: login.php');
			exit(0);
		}
		?>
	</div>
	<div class="footerWrapper">
	</div>
</body>
</html>