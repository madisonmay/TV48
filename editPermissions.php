<?
	session_start();
	include ("ESF_config.php");

	if ($_SERVER['REQUEST_METHOD'] == "POST") {

		// Opens a connection
		$mysqli = new mysqli($server, $username, $password, $database);

		/* check connection */
		if ($mysqli->connect_errno)
		{
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}

		$stmt = $mysqli->stmt_init();
		$stmt->prepare("UPDATE `ESF_users` SET `rooms`=? WHERE id = ?");
		$stmt->bind_param('ss', json_encode($_POST['rooms']), $_POST["id"]);
		$stmt->execute();
		$stmt->store_result();
		$stmt->close();
	}
?>