<? 

	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	//There should really be a way to link new users to existing objects in the databse
	//If this could be created, the landlord would not have to wait for all of the tenants
		//to register in the system for he/she to be able to set the permissions for each of the 
		//rooms.  The question is how can this be done in a secure manner?
	include ("ESF_config.php");

	$address = $_POST['address'];
	$phone = $_POST['phone_number'];
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

		//find a way to make this secure -- should also probably add some error handling
		$pass = md5($_POST["password"]);
		$stmt = $mysqli->stmt_init();
		$stmt->prepare("UPDATE `ESF_users` SET password=?, address=?, phone_number=?, registered=1 WHERE email = ?");
		$stmt->bind_param('ssss', $pass, $address, $phone, $email);
		$stmt->execute();
		$stmt->store_result();
		$stmt->fetch();

		if (!($stmt)) {
		  	die('Invalid query: ' . mysql_error());
		} else {
			header ('Location: home.php');
			exit(0);
		}

	} else {
		header ('Location: login.php');
		exit(0);
	}

?>