<? 

	//There should really be a way to link new users to existing objects in the databse
	//If this could be created, the landlord would not have to wait for all of the tenants
		//to register in the system for he/she to be able to set the permissions for each of the 
		//rooms.  The question is how can this be done in a secure manner?
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

		$session_id = $_SESSION['id'];
		$name = $_POST['name'];

		$stmt = $mysqli->stmt_init();
		$stmt->prepare("SELECT `id`, `landlord`, `landlord_id` FROM `ESF_users` WHERE sessionId = ?");
		$stmt->bind_param('s', $session_id);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($user_id, $landlord, $landlord_id);
		$stmt->fetch();

		if (!$landlord) {
			header ('Location: login.php');
			exit(0);
		}


		// Check if id exist?

		$stmt->close();

		$stmt = $mysqli->stmt_init();
		$stmt->prepare("INSERT INTO `Properties` (name) VALUES (?)");
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$stmt->store_result();
		$stmt->close();

		if (!($stmt)) {
		  	die('Invalid query: ' . mysql_error());
		} else {

			$stmt = $mysqli->stmt_init();
			$stmt->prepare("INSERT INTO `Property_X_Landlord` (landlord_id, property_id, property_name) VALUES (?, LAST_INSERT_ID(), ?)");
			$stmt->bind_param('is', $landlord_id, $name);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($property_id);
			$stmt->close();

			if (!($stmt)) {
			  	die('Invalid query: ' . mysql_error());
			} else {
				header ('Location: management.php');
				exit(0);
			}
		}

	} else {
		header ('Location: login.php');
		exit(0);
	}

?>