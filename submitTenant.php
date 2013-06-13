<? 
	session_start();

	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	//There should really be a way to link new users to existing objects in the databse
	//If this could be created, the landlord would not have to wait for all of the tenants
		//to register in the system for he/she to be able to set the permissions for each of the 
		//rooms.  The question is how can this be done in a secure manner?

	include ("ESF_config.php");
	if ($_SERVER['REQUEST_METHOD'] == "POST")
	{

		// Opens a connection to a MySQL server.
		$mysqli = new mysqli($server, $username, $password, $database);

		/* check connection */
		if ($mysqli->connect_errno) {
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}

		$stmt = $mysqli->stmt_init();
		$stmt->prepare("SELECT * FROM `ESF_users` WHERE sessionId = ?");
		$stmt->bind_param('s', $_SESSION["id"]);
		$stmt->execute();
		$stmt->store_result();

		// Check if id exist?
		if (($stmt->num_rows != 0))
		{
			echo "Dit e-mailadres bestaat al in onze database</br>";
			header ('Refresh: 4; url=register.php');
			exit();
		}

		$stmt->close();

		$stmt = $mysqli->stmt_init();
		$stmt->prepare("INSERT INTO `ESF_users` (id, firstName, lastName, email, password, sessionId, confirmationCode, landlord) VALUES (NULL,?,?,?,?,NULL,?,0)");
		$stmt->bind_param('sssss', $_POST["firstName"], $_POST["lastName"], $_POST["email"], $pass, $code);
		$stmt->execute();
		$stmt->store_result();
		$stmt->close();

		if (!($stmt))
		{
		  die('Invalid query: ' . mysql_error());
		} else
		{
			echo "<h1>Bedankt voor uw registratie</h1>";

			$to=''.$_POST["email"].'';

			$subject = 'CORE meetplatform registratie';

			$message = "
				<html>
					<head>
						<title>CORE meetplatform registratie</title>
					</head>
					<body>
						<h1>Bedankt voor het registreren, ".$_POST['firstName']."</h1>
						<p>Uw logingegevens zijn:</p>
						<p>Gebruikersnaam: ".$_POST['email']."</p>
						<p>Wachtwoord: ".$_POST['password']."</p></br>
						<a href=http://www.thinkcore.be/TV48/confirmation.php?code=".$code."&email=".$_POST['email'].">Klik hier om de registratie te voltooien</a><br/>
						<p>Gelieve niet te reageren op deze e-mail</p>
				</html>
				";

			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			// Additional headers
			$headers .= 'From: CORE_cvba-so' . "\r\n";

			// Mail it
			mail($to, $subject, $message, $headers);

			echo "<p>Een e-mail met uw registratiegegevens werd verzonden naar uw e-mailadres</p>";
			echo "<p>Klik op de link in de e-mail om uw registratie te voltooien</p>";

			header ('Refresh: 4; url=login.php');

		}
	} else
	{
		?>
		<h1>Error</h1>
		<p>U hebt geen toestemming tot deze pagina</p>
		<p>U wordt teruggeleid naar de startpagina</p>
		<?
		header ('Refresh: 4; url=login.php');
	}
?>