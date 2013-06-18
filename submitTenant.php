<? 

	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	//There should really be a way to link new users to existing objects in the databse
	//If this could be created, the landlord would not have to wait for all of the tenants
		//to register in the system for he/she to be able to set the permissions for each of the 
		//rooms.  The question is how can this be done in a secure manner?
	include ("ESF_config.php");
	include ("check.php");

	$room_id = $_POST['room_id'];
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];
	$balance = $_POST['balance'];
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$pid = $_POST['property_id'];
	$email = $_POST['email'];

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

		$stmt = $mysqli->stmt_init();
		$stmt->prepare("INSERT INTO `Tenants` (start_date, end_date, property_id, balance) VALUES (?, ?, ?, ?)");
		$stmt->bind_param('ssid', $startDate, $endDate, $pid, $balance);
		$stmt->execute();
		$stmt->store_result();
		$stmt->close();

		if (!($stmt)) {
		  	die('Invalid query: ' . mysql_error());
		} else {

			if ($room_id != 0 && $room_id != "-1") {
				$has_room = 1;
			} else {
				$has_room = 0;
			}

			$pay_public = 1;
			$has_studio = 0;
			if ($has_room) {
				$stmt = $mysqli->stmt_init();
				$stmt->prepare("SELECT `type` FROM `Rooms` WHERE id = ? ");
				$stmt->bind_param('i', $room_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($room_type);
				$stmt->fetch();
				$stmt->close();

				if ($room_type == 'Studio') {
					$pay_public = 0;
					$has_studio = 1;
				}
			}

			// print_r($pay_public);
			// print_r($has_room);
			// print_r($has_studio);
			// print_r($room_type);
			// print_r($room_id);
			// exit(0);

			$code = rand(000000001,999999999);

			//add in language later
			$stmt = $mysqli->stmt_init();
			$stmt->prepare("INSERT INTO `ESF_users` (firstName, lastName, email, confirmationCode, sessionId, landlord, landlord_id, tenant_id, has_room, property_id, has_studio) 
							VALUES (?, ?, ?, ?, NULL, 0, ?, LAST_INSERT_ID(), ?, ?, ?)");
			$stmt->bind_param('sssiiiii', $firstName, $lastName, $email, $code, $landlord_id, $has_room, $pid, $has_studio);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			$stmt = $mysqli->stmt_init();
			$stmt->prepare('SELECT `id` FROM `ESF_users` WHERE `property_id` = ? AND firstname = ? and lastName = ? ORDER BY `id` DESC LIMIT 1');
			$stmt->bind_param('iss', $pid, $firstName, $lastName);
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
			// print_r($pid);
			// print('<br>');
			// exit(0);

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("SELECT `id` FROM `Rooms` WHERE type = 'Public' AND property_id = ?");
			$stmt->bind_param('i', $pid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($_room_id);

			while ($stmt->fetch()) {
				$_stmt = $mysqli->stmt_init();
				$_stmt->prepare("INSERT IGNORE INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, ?, 1, ?, 0, ?)");
				$_stmt->bind_param('iiii', $new_user_id, $_room_id, $pay_public, $pid);
				$_stmt->execute();
				$_stmt->store_result();
				$_stmt->close();
			}
			
			$stmt->close();

			if ($has_room) {
				$stmt = $mysqli->stmt_init();
				$stmt->prepare("INSERT IGNORE INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, ?, 1, 1, 1, ?)");
				$stmt->bind_param('iii', $new_user_id, $room_id, $pid);
				$stmt->execute();
				$stmt->store_result();
				$stmt->close();

				$stmt = $mysqli->stmt_init();
				$stmt->prepare("UPDATE `Rooms` SET available = 0 WHERE id = ? ");
				$stmt->bind_param('i', $room_id);
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
							<h1>Hi, ".$firstName."</h1>
							<p>Start your account setup process by clicking on the link below:</p>
							<a href=http://www.thinkcore.be/TV48/tenantConfirmation.php?code=".$code."&email=".$email."&property_id=".$pid.">Setup Your Account</a><br/>
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