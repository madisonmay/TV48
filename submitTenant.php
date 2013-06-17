<? 

	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	//There should really be a way to link new users to existing objects in the databse
	//If this could be created, the landlord would not have to wait for all of the tenants
		//to register in the system for he/she to be able to set the permissions for each of the 
		//rooms.  The question is how can this be done in a secure manner?
	include ("ESF_config.php");
	include ("check.php");

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
		$stmt->prepare("SELECT `id`, `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
		$stmt->bind_param('s', $_SESSION["id"]);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($user_id, $landlord, $landlord_id);
		$stmt->fetch();

		if (!$landlord) {
			header ('Location: login.php');
			exit(0);
		}


		$stmt->close();

		$stmt = $mysqli->stmt_init();

		//sketchy
		$property_id = $_POST['property_id'];

		$stmt = $mysqli->stmt_init();
		$stmt->prepare("INSERT INTO `Tenants` (start_date, end_date, property_id, balance) VALUES (?, ?, ?, ?)");
		$stmt->bind_param('ssid', $_POST["startDate"], $_POST['endDate'], $property_id, $_POST['balance']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->close();

		if (!($stmt)) {
		  	die('Invalid query: ' . mysql_error());
		} else {

			if ($_POST['room_id'] >= 0 && $_POST['room_id'] != "None") {
				$has_room = 1;
			} else {
				$has_room = 0;
			}

			$code = rand(000000001,999999999);

			//add in language later
			$stmt = $mysqli->stmt_init();
			$stmt->prepare("INSERT INTO `ESF_users` (firstName, lastName, email, confirmationCode, sessionId, landlord, landlord_id, tenant_id, has_room, property_id) 
							VALUES (?, ?, ?, ?, NULL, 0, ?, LAST_INSERT_ID(), ?, ?)");
			$stmt->bind_param('sssiibi', $_POST['firstName'], $_POST['lastName'], $_POST['email'], $code, $landlord_id, $has_room, $property_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			$stmt = $mysqli->stmt_init();
			$stmt->prepare('SELECT `id` FROM `ESF_users` WHERE `property_id` = ? AND firstname = ? and lastName = ? ORDER BY `id` DESC LIMIT 1');
			$stmt->bind_param('iss', $property_id, $_POST['firstName'], $_POST['lastName']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($new_user_id);
			$stmt->fetch();
			$stmt->close();

			//Debugging

			// print('User id: ');
			// print_r($new_user_id);
			// print('<br>');
			// print('First name: ');
			// print_r($_POST['firstName']);
			// print('<br>');
			// print('Last name: ');
			// print_r($_POST['lastName']);
			// print('<br>');
			// print('Property id: ');
			// print_r($property_id);
			// print('<br>');
			// exit(0);

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("SELECT `id` FROM `Rooms` WHERE type = 'Public' and property_id = ?");
			$stmt->bind_param('i', $property_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($_room_id);

			while($stmt->fetch()) {
				$stmt = $mysqli->stmt_init();
				$stmt->prepare("INSERT INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, ?, 1, 1, 0, ?)");
				$stmt->bind_param('iii', $new_user_id, $_room_id, $property_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->close();
			}
			
			$stmt->close();

			if ($has_room) {
				$stmt = $mysqli->stmt_init();
				$stmt->prepare("INSERT INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (LAST_INSERT_ID(), ?, 1, 1, 1, ?)");
				$stmt->bind_param('ii', $_POST['room_id'], $property_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->close();

				$stmt = $mysqli->stmt_init();
				$stmt->prepare("UPDATE `Rooms` SET available = 0 WHERE id = ? ");
				$stmt->bind_param('i', $_POST['room_id']);
				$stmt->execute();
				$stmt->store_result();
				$stmt->close();
			} 

			if (!($stmt)) {
			  	die('Invalid query: ' . mysql_error());
			} else {

				$to=''.$_POST["email"].'';

				$subject = 'CORE registration -- setup your account';

				$message = "
					<html>
						<head>
							<title>CORE registration -- setup your account</title>
						</head>
						<body>
							<h1>Hi, ".$_POST['firstName']."</h1>
							<p>Start your account setup process by clicking on the link below:</p>
							<a href=http://www.thinkcore.be/TV48/tenantConfirmation.php?code=".$code."&email=".$_POST['email']."&property_id=".$property_id.">Setup Your Account</a><br/>
							<p>Thanks, </p>
							<p>The CORE team</p>
					</html>
					";

				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

				// Additional headers
				$headers .= 'From: CORE_cvba-so' . "\r\n";

				// Mail it
				mail($to, $subject, $message, $headers);

				header ('Location: editProperty.php');
				exit(0);
			}
		}

	} else {
		header ('Location: login.php');
		exit(0);
	}

?>