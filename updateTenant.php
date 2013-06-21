<? 
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


		function removeStudio($mysqli, $room_id, $user_id) {
			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE `ESF_users` SET has_studio=0 WHERE id = ?");
			$stmt->bind_param('i', $user_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE `User_X_Room` SET pay=1 WHERE user_id = ? AND view=1 AND pay=0");
			$stmt->bind_param('i', $user_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();
		}

		function addStudio($mysqli, $room_id, $user_id) {
			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE `ESF_users` SET has_studio=1 WHERE id = ?");
			$stmt->bind_param('i', $user_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE `User_X_Room` SET pay=0 WHERE user_id = ? AND pay=1 and modify=0");
			$stmt->bind_param('i', $user_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();
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
		$old_room_type = $_POST['old_room_type'];
		$stmt->close();

		//Need more consistency for Null value check
		if ($old_room_id != 0) {
			$old_room_exists = 1;
		} else {
			$old_room_exists = 0;
		}

		if ($room_id != "-1") {
			$room_exists = 1;
		} else {
			$room_exists = 0;
		}

		if ($old_room_type != "Studio") {
			$had_studio = 0;
		} else {
			$had_studio = 1;
		}

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

			if ($has_room) {
				$stmt = $mysqli->stmt_init();
				$stmt->prepare("SELECT `type` FROM `Rooms` WHERE id = ?");
				$stmt->bind_param('i', $room_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($room_type);
				$stmt->fetch();
				$stmt->close();
			} else {
				$room_type = 'None';
			}


			if ($room_type != "Studio") {
				$has_studio = 0;
			} else {
				$has_studio = 1;
			}

			// print_r($room_id);
			// print('<br>');
			// print_r($user_id);
			// print('<br>');
			// print_r($has_studio);
			// print('<br>');
			// exit(0);

			//add in language later
			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE `ESF_users` SET firstName=?, lastName=?, email=?, has_room=?, has_studio=? WHERE id = ?");
			$stmt->bind_param('sssiii', $_POST['firstName'], $_POST['lastName'], $_POST['email'], $has_room, $has_studio, $id);
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
					$stmt->prepare("UPDATE `User_X_Room` SET view=0, pay=0, modify=0 WHERE room_id = ? AND user_id=?");
					$stmt->bind_param('i', $old_room_id, $id);
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
					$stmt->prepare("INSERT IGNORE INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, ?, 1, 1, 1, ?)");
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

			if ($had_studio && !$has_studio) {
				removeStudio($mysqli, $old_room_id, $id);
			} elseif (!$had_studio && $has_studio) {
				addStudio($mysqli, $room_id, $id);
			}

			if (!($stmt)) {
			  	die('Invalid query: ' . mysql_error());
			} else {
				header ('Location: editProperty.php');
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