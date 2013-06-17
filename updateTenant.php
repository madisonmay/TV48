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

		$id = $_POST['user_id'];
		$property_id = $_POST['property_id'];
		$old_room_id = $_POST['old_room_id'];
		$room_id = $_POST['room_id'];
		$stmt->close();

		//Query now successful
		$stmt = $mysqli->stmt_init();
		$stmt->prepare("UPDATE `Tenants` SET start_date=?, end_date=?, balance=? WHERE user_id = ?");
		$stmt->bind_param('ssdi', $_POST["startDate"], $_POST['endDate'], $_POST['balance'], $id);
		$stmt->execute();
		$stmt->store_result();
		$stmt->close();

		if (!($stmt)) {
		  	die('Invalid query: ' . mysql_error());
		} else {

			if ($_POST['room_id'] != '-1') {
				$has_room = 1;
			} else {
				$has_room = 0;
			}

			//add in language later
			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE `ESF_users` SET firstName=?, lastName=?, email=?, has_room=? WHERE id = ?");
			$stmt->bind_param('sssii', $_POST['firstName'], $_POST['lastName'], $_POST['email'], $has_room, $id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			// print_r($old_room_id);
			// print('<br>');
			// print_r($room_id);
			// print('<br>');
			// print_r($has_room);
			// print('<br>');
			// exit(0);

			if ($room_id != $old_room_id) {	
				if ((int) $old_room_id != 0) {

					$stmt = $mysqli->stmt_init();
					$stmt->prepare("UPDATE `User_X_Room` SET view=0, pay=0, modify=0 WHERE room_id = ?");
					$stmt->bind_param('i', $old_room_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->close();

					$stmt = $mysqli->stmt_init();
					$stmt->prepare("UPDATE `Rooms` SET available = 1 WHERE id = ?");
					$stmt->bind_param('i', $old_room_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->close();

				} 
				if ($has_room) {
					$stmt = $mysqli->stmt_init();
					$stmt->prepare("INSERT INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, ?, 1, 1, 1, ?)");
					$stmt->bind_param('iii', $id, $_POST['room_id'], $property_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->close();

					$stmt = $mysqli->stmt_init();
					$stmt->prepare("UPDATE `Rooms` SET available = 0 WHERE id = ?");
					$stmt->bind_param('i', $_POST['room_id']);
					$stmt->execute();
					$stmt->store_result();
					$stmt->close();
				}
			}

			//funky errors still occuring here


			if (!($stmt)) {
			  	die('Invalid query: ' . mysql_error());
			} else {
				header ('Location: management.php');
				exit(0);
			}
		}

	} else {
		print("Method is not POST");
		exit(0);
		header ('Location: login.php');
		exit(0);
	}
?>