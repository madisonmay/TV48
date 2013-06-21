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
			session_start();
			include ("ESF_config.php");


			// Opens a connection
			$mysqli = new mysqli($server, $username, $password, $database);

			/* check connection */
			if ($mysqli->connect_errno)
			{
				printf("Connect failed: %s\n", $mysqli->connect_error);
				exit();
			}

			//echo $_SESSION['id'];
			//$_SESSION['id'] = session_id();

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE `ESF_users` SET sessionId = '' WHERE sessionId = ?");
			$stmt->bind_param("s", $_SESSION['id']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			header ('Refresh: 0; url=login.php');
		?>
	</div>
</body>
</html>