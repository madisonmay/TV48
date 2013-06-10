<!DOCTYPE html>
<html lang="en"></html>
<head>
    <title>TV48 - Login</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type='text/css' href="stylesheets/bootstrap-combined.min.css">
    <link rel="stylesheet" type='text/css' href="stylesheets/jquery-ui.css">
    <link rel="stylesheet" type='text/css' href="style.css">
    <script src="scripts/jquery.min.js"></script>
    <script src="scripts/bootstrap.min.js"></script>
    <script src="scripts/jquery-ui.min.js"></script>
</head>
<body>
    <?
        session_start();
		include ('ESF_config.php');

		if( isset($_SESSION['id']) )
		{
			// Opens a connection to a MySQL server.
			$mysqli = new mysqli($server, $username, $password, $database);

			/* check connection */
			if ($mysqli->connect_errno) {
				printf("Connect failed: %s\n", $mysqli->connect_error);
				exit();
			}

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("SELECT * FROM `ESF_users` WHERE sessionID = ?");
			$stmt->bind_param('s', $_SESSION['id']);
			$stmt->execute();
			$stmt->store_result();

			if (!($stmt))
			{
				die('Invalid query: ' . mysql_error());
			}

			if ($stmt->num_rows != 0)
			{
				?>
				<p>Reeds ingelogd</p>
				<?
				$stmt->close();

				$registered = 0;

				$stmt = $mysqli->stmt_init();
				$stmt->prepare("SELECT registered FROM `ESF_users` WHERE sessionID = ?");
				$stmt->bind_param('s', $_SESSION['id']);
				$stmt->execute();
				$stmt->bind_result($registered);
				$stmt->store_result();
				$stmt->fetch();
				$stmt->close();

				if($registered)
				{
					header("Location: home.php"); /* Redirect browser */
					exit();
				} else
				{
					header("Location: home.php"); /* Redirect browser */
					exit();
				}
			} else
			{
				include ('loginform.html');
			}
		} else
		{
			include ('loginform.html');
		}
	?>
</body>
</html>