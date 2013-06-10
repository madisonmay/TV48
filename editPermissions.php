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
		$stmt->prepare("SELECT id, confirmed FROM `ESF_users` WHERE email = ? AND password = ?");
		$stmt->bind_param('ss', $_POST["email"], $passMD5);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($id,$confirmed);
		$stmt->fetch();

	}
?>