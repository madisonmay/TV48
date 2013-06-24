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

		if ($_SERVER['REQUEST_METHOD'] == "POST")
		{
			//convert pass to MD5
			$passMD5 = md5($_POST["pass"]);

			// Opens a connection
			$mysqli = new mysqli($server, $username, $password, $database);

			/* check connection */
			if ($mysqli->connect_errno)
			{
				printf("Connect failed: %s\n", $mysqli->connect_error);
				exit();
			}

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("SELECT id, confirmed FROM `ESF_users` WHERE email = ? AND password = ?");
			$stmt->bind_param('ss', $_POST["email"], $passMD5);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id,$confirmed);
			$stmt->fetch();

			// Check if id exists?
			if ($stmt->num_rows == 0)
			{
				echo "<h1>Error</h1>";
				echo "<p>De account met dit e-mailadres of wachtwoord was niet gevonden</p>";
				header ('Refresh: 4; url=login.php');
				$stmt->close();
			} else
			{
				$stmt->close();

				if($confirmed == 0)
				{
					echo "<h1>Registratie nog niet voltooid</h1>";
					echo "<p>Gelieve eerst de registratie te voltooien via de link in de bevestigingsmail</p>";
				} else
				{

					//add session id to database
					$_SESSION['id'] = session_id();

					$stmt = $mysqli->stmt_init();
					$stmt->prepare("UPDATE `ESF_users` SET sessionId = ? WHERE email = ?");
					$stmt->bind_param("ss", $_SESSION['id'], $_POST["email"]);
					$stmt->execute();
					$stmt->store_result();
					$stmt->close();

					if (!($stmt))
					{
						 die('Invalid query: ' . mysql_error());
					} else
					{
						?>
						<h1>Login geslaagd</h1>
						<?

						$registered = 0;

						$stmt = $mysqli->stmt_init();
						$stmt->prepare("SELECT registered FROM `ESF_users` WHERE sessionId = ?");
						$stmt->bind_param('s', $_SESSION['id']);
						$stmt->execute();
						$stmt->bind_result($registered);
						$stmt->store_result();
						$stmt->fetch();
						$stmt->close();

						if($registered)
						{
							header ('Location: home.php');
							exit();
						} else
						{
							header ('Location: home.php');}
							exit();
						}
					}
				}
			}

		 else
		{
			?>
			<h1>Error</h1>
			<p>U heeft geen toegang tot deze pagina</p>
			<?
			header ('Location: login.php');
			exit();
		}
		?>
	</div>
</body>
</html>