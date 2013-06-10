<?
	session_start();
	include ("ESF_config.php");

	// Opens a connection to a MySQL server.
	$mysqli = new mysqli($server, $username, $password, $database);

	/* check connection */
	if ($mysqli->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}

	$stmt = $mysqli->stmt_init();
	$stmt->prepare("SELECT `id`, `registered`, `confirmed` FROM `ESF_users` WHERE sessionId = ?");
	$stmt->bind_param("s", $_SESSION['id']);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($userId, $registered, $confirmed);
	//$stmt->fetch();
	if ($stmt->num_rows != 0){
		//ingelogd
	}
	else{
		header ('Refresh: 0; url=login.php');
		exit();
	}
	$stmt->close();
?>