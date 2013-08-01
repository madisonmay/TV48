<? 
	$username = 'deb72907';
	$password = 'krDWtXeDHa';
	$database = 'deb72907_coreweb';
	$server = 'localhost';
	
	//connect to the database
	$mysqli = new mysqli($server, $username, $password, $database);
	$stmt = $mysqli->stmt_init();
					
	//check connection
	if ($mysqli->connect_errno)
	{
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}
	
	if($_GET){
		
		$channel = $_GET["channel"];
		
		$stmt = $mysqli->prepare
		("
			UPDATE lightStreams
			SET request = 0
			WHERE deviceId = 1 AND channel = ? 		
		");
		$stmt->bind_param('i', $channel);
		$stmt->execute();
		$stmt->store_result();
		$stmt->close();
	}
	
	if($_POST){
	
	$PWM = array($_REQUEST["PWM1"],$_REQUEST["PWM2"],$_REQUEST["PWM3"],$_REQUEST["PWM4"],$_REQUEST["PWM5"],$_REQUEST["PWM6"],$_REQUEST["PWM7"],$_REQUEST["PWM8"],$_REQUEST["PWM9"],$_REQUEST["PWM10"],$_REQUEST["PWM11"],$_REQUEST["PWM12"],$_REQUEST["PWM13"],$_REQUEST["PWM14"],$_REQUEST["PWM15"],$_REQUEST["PWM16"],$_REQUEST["PWM17"],$_REQUEST["PWM18"],$_REQUEST["PWM19"],$_REQUEST["PWM20"],$_REQUEST["PWM21"],$_REQUEST["PWM22"],$_REQUEST["PWM23"],$_REQUEST["PWM24"]);
	var_dump($PWM);	
		/*$PWM1 = $_REQUEST["PWM1"];
		$PWM2 = $_REQUEST["PWM1"];
		$PWM3 = $_REQUEST["PWM3"];
		$PWM4 = $_REQUEST["PWM4"];
		$PWM5 = $_REQUEST["PWM5"];
		$PWM6 = $_REQUEST["PWM6"];
		$PWM7 = $_REQUEST["PWM7"];
		$PWM8 = $_REQUEST["PWM8"];
		$PWM9 = $_REQUEST["PWM9"];
		$PWM10 = $_REQUEST["PWM10"];
		$PWM11 = $_REQUEST["PWM11"];
		$PWM12 = $_REQUEST["PWM12"];
		$PWM13 = $_REQUEST["PWM13"];
		$PWM14 = $_REQUEST["PWM14"];
		$PWM15 = $_REQUEST["PWM15"];
		$PWM16 = $_REQUEST["PWM16"];
		$PWM17 = $_REQUEST["PWM17"];
		$PWM18 = $_REQUEST["PWM18"];
		$PWM19 = $_REQUEST["PWM19"];
		$PWM20 = $_REQUEST["PWM20"];
		$PWM21 = $_REQUEST["PWM21"];
		$PWM22 = $_REQUEST["PWM22"];
		$PWM23 = $_REQUEST["PWM23"];
		$PWM24 = $_REQUEST["PWM24"];*/
		
		/*
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 3;		
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 4;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 5;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 6;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 7;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 8;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 9;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 10;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 11;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 12;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 13;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 14;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 15;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 16;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 17;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 18;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 19;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 20;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 21;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 22;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 23;
			
			UPDATE lightStreams
			SET pwm = ?
			WHERE deviceId = 1 AND channel = 24;*/
		
		/*$stmt = $mysqli->prepare
		("
			UPDATE lightStreams
			SET pwm = O
			WHERE deviceId = 1 AND channel = 1;
			
			
			UPDATE lightStreams
			SET pwm = 0
			WHERE deviceId = 1 AND channel = 2;
			
		");*/
		//$stmt->bind_param('ii', $PWM1, $PWM2/*, $PWM3, $PWM4, $PWM5, $PWM6, $PWM7, $PWM8, $PWM9, $PWM10, $PWM11, $PWM12, $PWM13, $PWM14, $PWM15, $PWM16, $PWM17, $PWM18, $PWM19, $PWM20, $PWM21, $PWM22, $PWM23, $PWM24*/);
		/*$stmt->execute();
		$stmt->store_result();
		$stmt->close();*/
	}
	
	$stmt = $mysqli->prepare
	("
		SELECT channel, pwm
		FROM lightStreams
		WHERE deviceId = 1 AND request = 1
		ORDER BY time ASC
		limit 0,1; 		
	");
	$stmt->execute();
	$stmt->store_result();

	$stmt->bind_result($channel,$pwm);
	
	while($stmt->fetch()){
	echo $channel;
	echo "\n";
	echo $pwm;
	}	
	
	$stmt->close();
	
?>