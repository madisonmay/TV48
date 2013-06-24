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

		if ($_SERVER['REQUEST_METHOD'] == "POST")
		{
			//convert pass to MD5
			$bouwType = $_POST["bouwType"];
			$bouwjaar = $_POST["bouwjaar"];
			$gerenoveerd = $_POST["gerenoveerd"];
			$renovatiejaar = $_POST["renovatiejaar"];
			$geisoleerd = $_POST["geisoleerd"];
			$aantalKamers = $_POST["aantalKamers"];
			$kamerOppervlakte = $_POST["kamerOppervlakte"];
			$aantalStudios = $_POST["aantalStudios"];
			$studioOppervlakte = $_POST["studioOppervlakte"];
			$epcLabel = $_POST["epcLabel"];
			$verwarmingType = $_POST["verwarmingType"];
			$verwarmingTypeAndere = $_POST["verwarmingTypeAndere"];
			$verwarmingstoestel = $_POST["verwarmingstoestel"];
			$verwarmingstoestelAndere = $_POST["verwarmingstoestelAndere"];
			$ouderdomVerwarmingstoestel = $_POST["ouderdomVerwarmingstoestel"];
			$thermostaat = $_POST["thermostaat"];
			$toilet = $_POST["toilet"];
			$spaardouchekop = $_POST["spaardouchekop"];
			$regenwater = $_POST["regenwater"];
			$kookplaat = $_POST["kookplaat"];
			$kookplaatAndere = $_POST["kookplaatAndere"];

			$apparaten = '';

			if(isset($_POST['apparaten'])){
				if(is_array($_POST['apparaten'])){
					foreach($_POST['apparaten'] as $value){
						$apparaten = $apparaten.$value;
					}
				}
			}

			$apparatenAnder = $_POST["apparatenAnder"];

			$koelkast = '';

			if(isset($_POST['koelkast'])){
				if(is_array($_POST['koelkast'])){
					foreach($_POST['koelkast'] as $value){
						$koelkast = $koelkast.$value;
					}
				}
			}
			$koelkastAantalA3 = $_POST["koelkastAantalA3"];
			$koelkastAantalA2 = $_POST["koelkastAantalA2"];
			$koelkastAantalA1 = $_POST["koelkastAantalA1"];
			$koelkastAantalA = $_POST["koelkastAantalA"];
			$koelkastAantalB = $_POST["koelkastAantalB"];
			$koelkastAantalkB = $_POST["koelkastAantalkB"];
			$koelkastAantalGeenIdee = $_POST["koelkastAantalGeenIdee"];

			$diepvries = '';

			if(isset($_POST['diepvries'])){
				if(is_array($_POST['diepvries'])){
					foreach($_POST['diepvries'] as $value){
						$diepvries = $diepvries.$value;
					}
				}
			}

			$diepvriesAantalA3 = $_POST["diepvriesAantalA3"];
			$diepvriesAantalA2 = $_POST["diepvriesAantalA2"];
			$diepvriesAantalA1 = $_POST["diepvriesAantalA1"];
			$diepvriesAantalA = $_POST["diepvriesAantalA"];
			$diepvriesAantalB = $_POST["diepvriesAantalB"];
			$diepvriesAantalkB = $_POST["diepvriesAantalkB"];
			$diepvriesAantalGeenIdee = $_POST["diepvriesAantalGeenIdee"];
			$ramen = $_POST["ramen"];
			$ramenAnder = $_POST["ramenAnder"];

			$lichten = '';

			if(isset($_POST['lichten'])){
				if(is_array($_POST['lichten'])){
					foreach($_POST['lichten'] as $value){
						$lichten = $lichten.$value;
					}
				}
			}
			$lichtenAnder = $_POST["lichtenAnder"];
			$timer = $_POST["timer"];
			$sensor = $_POST["sensor"];
			$ventilatie = $_POST["ventilatie"];

			$facturatie = '';

			if(isset($_POST['facturatie'])){
				if(is_array($_POST['facturatie'])){
					foreach($_POST['facturatie'] as $value){
						$facturatie = $facturatie.$value;
					}
				}
			}


			$stmt = $mysqli->stmt_init();

			$stmt->prepare("SELECT id FROM `ESF_users` WHERE sessionId = ?");
			$stmt->bind_param('s', $_SESSION['id']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($userId);
			$stmt->fetch();
			$stmt->close();

			$stmt = $mysqli->stmt_init();

			$stmt->prepare("INSERT INTO `ESF_survey`(`id`, `bouwType`, `bouwjaar`, `gerenoveerd`, `renovatiejaar`, `geisoleerd`, `aantalKamers`, `kamerOppervlakte`, `aantalStudios`, `studioOppervlakte`, `epcLabel`, `verwarmingType`, `verwarmingTypeAndere`, `verwarmingstoestel`, `ouderdomVerwarmingstoestel`, `thermostaat`, `toilet`, `spaardouchekop`, `regenwater`, `kookplaat`, `kookplaatAndere`, `apparaten`, `apparatenAnder`, `koelkast`, `koelkastAantalA3`, `koelkastAantalA2`, `koelkastAantalA1`, `koelkastAantalA`, `koelkastAantalB`, `koelkastAantalkB`, `koelkastAantalGeenIdee`, `diepvries`, `diepvriesAantalA3`, `diepvriesAantalA2`, `diepvriesAantalA1`, `diepvriesAantalA`, `diepvriesAantalB`, `diepvriesAantalkB`, `diepvriesAantalGeenIdee`, `ramen`, `ramenAnder`, `lichten`, `lichtenAnder`, `timer`, `sensor`, `ventilatie`, `facturatie`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
			$stmt->bind_param('isisisiiiissssisssssssssiiiiiiisiiiiiiissssssss', $userId, $bouwType, $bouwjaar, $gerenoveerd, $renovatiejaar, $geisoleerd, $aantalKamers, $kamerOppervlakte, $aantalStudios, $studioOppervlakte, $epcLabel, $verwarmingType, $verwarmingTypeAndere, $verwarmingstoestel, $ouderdomVerwarmingstoestel, $thermostaat, $toilet, $spaardouchekop, $regenwater, $kookplaat, $kookplaatAndere, $apparaten, $apparatenAnder, $koelkast, $koelkastAantalA3, $koelkastAantalA2, $koelkastAantalA1, $koelkastAantalA, $koelkastAantalB, $koelkastAantalkB, $koelkastAantalGeenIdee, $diepvries, $diepvriesAantalA3, $diepvriesAantalA2, $diepvriesAantalA1, $diepvriesAantalA, $diepvriesAantalB, $diepvriesAantalkB, $diepvriesAantalGeenIdee, $ramen, $ramenAnder, $lichten, $lichtenAnder, $timer, $sensor, $ventilatie, $facturatie);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			$stmt = $mysqli->stmt_init();

			$stmt->prepare("UPDATE ESF_users SET registered = 1 WHERE sessionId = ?");
			$stmt->bind_param('s', $_SESSION['id']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();

			header ('Refresh: 0; url=home.php');






		}

		else
		{
			?>
			<h1>Error</h1>
			<p>U heeft geen toegang tot deze pagina</p>
			<?
			header ('Refresh: 0; url=ESF_login.php');
		}
		?>