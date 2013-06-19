<? 

	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	include ("../ESF_config.php");
	include ("check_admin.php");

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

		$stmt = $mysqli->stmt_init();
		$stmt->prepare("SELECT `admin` FROM `ESF_users` WHERE sessionId = ?");
		$stmt->bind_param('s', $session_id);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($admin);
		$stmt->fetch();
		$stmt->close();

		if (!$admin) {
			header ('Location: ../login.php');
			exit(0);
		}

		$type = $_POST['type'];
		$room_id = $_POST['room_id'];
		$channel = $_POST['channel'];
		$name = $_POST['name'];
		// Check if id exist?

		$stmt = $mysqli->stmt_init();

		if ($type == 'Lighting') {
			$stmt->prepare("INSERT INTO `lightStreams` (location, roomId, channel) VALUES (?, ?, ?)");	
		} elseif ($type == 'Heating') {
			$stmt->prepare("INSERT INTO `Heat` (name, room_id, channel) VALUES (?, ?, ?)");		
		} elseif ($type == 'Electric') {
			$stmt->prepare("INSERT INTO `Power` (name, room_id, channel) VALUES (?, ?, ?)");					
		} else {
			print('Type: ');
			print_r($type);
			exit(0);
		}

		$stmt->bind_param('sii', $name, $room_id, $channel);
		$stmt->execute();
		$stmt->store_result();
		$stmt->close();

		if (!($stmt)) {
		  	die('Invalid query: ' . mysql_error());
		} else {
			header ('Location: index.php');
			exit(0);
		}

	} else {
		header ('Location: index.php');
		exit(0);
	}

?>