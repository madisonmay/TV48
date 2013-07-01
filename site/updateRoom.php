<? 
	include ("check.php");


	if ($_SERVER['REQUEST_METHOD'] == "POST")
	{


		//Sketchy use of both id, user_id, and old_user_id -- should sort that out.

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
		$stmt->prepare("SELECT `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
		$stmt->bind_param('s', $_SESSION["id"]);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($landlord, $landlord_id);
		$stmt->fetch();
		$stmt->close();

		if (!$landlord) {
			header ('Location: login.php');
			exit(0);
		}

		$user_id = $_POST['user_id'];
		$id = $user_id;
		$property_id = $_POST['property_id'];
		$old_user_id = $_POST['old_user_id'];
		$room_id = $_POST['room_id'];


		if ($_POST['user_id'] == '-1') {
			$available = 1;
		} else {
			$available = 0;
		}

		//Debugging

		// print_r($_POST['user_id']);
		// print('<br>');
		// print_r($_POST['old_user_id']);
		// print('<br>');
		// print_r($_POST['room_id']);
		// print('<br>');
		// print_r($_POST['room_type']);
		// print('<br>');
		// print_r($available);
		// print('<br>');
		// exit(0);

		$stmt = $mysqli->stmt_init();
		$stmt->prepare("UPDATE `Rooms` SET name=?, type=?, available=? WHERE id = ?");
		$stmt->bind_param('ssii', $_POST['room_name'], $_POST['room_type'], $available, $room_id);
		$stmt->execute();
		$stmt->store_result();
		$stmt->close();

		//some of these variables may not be necessary, but they're convenient
		//ternary statements?
		if ($_POST['room_type'] == 'Studio') {
			$is_studio = 1;
		} else {
			$is_studio = 0;
		}

		if ($_POST['old_room_type'] == 'Studio') {
			$was_studio = 1;
		} else {
			$was_studio = 0;
		}

		if ($old_user_id != 0) {
			$old_user_exists = 1;
		} else {
			$old_user_exists = 0;
		}

		if ($id != "-1") {
			$user_exists = 1;
		} else {
			$user_exists = 0;
		}

		//a mess of logic below -- should be simplified and streamlined
		//there is no way this will cut it for production code
		if($_POST['room_type'] != 'Public' && $_POST['old_room_type'] != 'Public') {
			if ($user_id != $old_user_id) {	
				if ((int) $old_user_id != 0) {

					$stmt = $mysqli->stmt_init();
					$stmt->prepare("UPDATE `User_X_Room` SET view=0, pay=0, modify=0 WHERE room_id = ? and user_id = ?");
					$stmt->bind_param('ii', $room_id, $old_user_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->close();

					$stmt = $mysqli->stmt_init();
					$stmt->prepare("UPDATE `ESF_users` SET has_room=0 WHERE id = ?");
					$stmt->bind_param('i', $old_user_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->close();

					$stmt = $mysqli->stmt_init();
					$stmt->prepare("UPDATE `Rooms` SET available = 0 WHERE id = ?");
					$stmt->bind_param('i', $room_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->close();

				} 
				if (!$available) {
					$stmt = $mysqli->stmt_init();
					$stmt->prepare("INSERT IGNORE INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, ?, 1, 1, 1, ?)");
					$stmt->bind_param('iii', $id, $room_id, $property_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->close();

					$stmt = $mysqli->stmt_init();
					$stmt->prepare("UPDATE `Rooms` SET available = 1 WHERE id = ?");
					$stmt->bind_param('i', $room_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->close();
				}
			}
		} elseif ($_POST['room_type'] == 'Public') {

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE `User_X_Room` SET view=0, pay=0, modify=0 WHERE room_id = ?");
			$stmt->bind_param('i', $room_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("UPDATE `Rooms` SET available = 0 WHERE id = ?");
			$stmt->bind_param('i', $room_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			if ($user_id != "-1") {
				$stmt = $mysqli->stmt_init();
				$stmt->prepare("UPDATE `ESF_users` SET has_room=0 WHERE id = ?");
				$stmt->bind_param('i', $user_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->close();
			}

			$stmt = $mysqli->stmt_init();
			$stmt->prepare('SELECT `id` FROM `ESF_users` WHERE landlord_id = ? AND landlord != 1 and property_id = ?');
			$stmt->bind_param('ii', $landlord_id, $property_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($_user_id);

			while ($stmt->fetch()) {
			    //for each user of the property, add view access by adding entry to the cross table
			    $_stmt = $mysqli->stmt_init();
			    $_stmt->prepare("INSERT IGNORE INTO `User_X_Room` (user_id, room_id, view, pay, modify, property_id) VALUES (?, ?, 1, 1, 0, ?)");
			    $_stmt->bind_param('iii', $_user_id, $room_id, $property_id);
			    $_stmt->execute();
			    $_stmt->store_result();
			    $_stmt->close(); 
			}
			$stmt->close(); 

		} else {
		    $stmt = $mysqli->stmt_init();
		    $stmt->prepare("UPDATE `User_X_Room` SET view = 0, pay = 0, modify = 0 WHERE room_id = ? AND property_id = ?");
		    $stmt->bind_param('ii', $room_id, $property_id);
		    $stmt->execute();
		    $stmt->store_result();
		    $stmt->close(); 

		    if ($user_id != "-1") {
				$stmt = $mysqli->stmt_init();
				$stmt->prepare("UPDATE `User_X_Room` SET view=1, pay=1, modify=1 WHERE room_id = ? AND user_id = ?");
				$stmt->bind_param('ii', $room_id, $user_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->close();

				$stmt = $mysqli->stmt_init();
				$stmt->prepare("UPDATE `ESF_users` SET has_room=1 WHERE id = ?");
				$stmt->bind_param('i', $user_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->close();
		    } else {
		    	$stmt = $mysqli->stmt_init();
		    	$stmt->prepare("UPDATE `Rooms` SET available=1 WHERE id = ?");
		    	$stmt->bind_param('i', $room_id);
		    	$stmt->execute();
		    	$stmt->store_result();
		    	$stmt->close();
		    }
		}


		//More debuggin

		print('Old User Id: ');
		print_r($old_user_id);
		print('<br>');
		print('Old User Exists: ');
		print_r($old_user_exists);
		print('<br>');
		print('Current User Id: ');
		print_r($user_id);
		print('<br>');
		print('Current User Exists: ');
		print_r($user_exists);
		print('<br>');
		print('Room Id: ');
		print_r($room_id);
		print('<br>');
		print('Was Studio: ');
		print($was_studio);
		print('<br>');
		print('Is Studio: ');
		print_r($is_studio);
		print('<br>');

		if ($is_studio && $was_studio && $old_user_id != $user_id) {
			print("Case 1");
			// exit(0);
			if ($old_user_exists) {removeStudio($mysqli, $room_id, $old_user_id);} 
			if ($user_exists) {addStudio($mysqli, $room_id, $user_id);}
		} elseif (($old_user_id == $user_id) && $was_studio && !$is_studio) {
			print("Case 2");
			// exit(0);
			if ($user_exists) {removeStudio($room_id, $user_id);}
		} elseif (($old_user_id == $user_id) && $is_studio && !$was_studio) {
			print("Case 3");
			// exit(0);
			if ($user_exists) {addStudio($mysqli, $room_id, $user_id);}
		} elseif(($old_user_id != $user_id) && $was_studio && !$is_studio) {
			print("Case 4");
			// exit(0);
			if ($old_user_exists) {removeStudio($mysqli, $room_id, $old_user_id);}
			if ($user_exists) {removeStudio($mysqli, $room_id, $user_id);}
		} elseif(($old_user_id != $user_id) && !$was_studio && $is_studio) {
			print("Case 5");
			// exit(0);
			if ($user_exists) {addStudio($mysqli, $room_id, $user_id);}
		} else {
			print("Case 6");
			// exit(0);
		}

		header ('Location: editProperty.php');
		exit(0);

	} else {
		print("Method is not POST");
		exit(0);
		header ('Location: login.php');
		exit(0);
	}
?>