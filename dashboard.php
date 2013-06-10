<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>ESF</title>
	<link href="ESF.css" rel="stylesheet" type="text/css" />

</head>
<?
include ("ESF_config.php");
include ("check.php");

// Opens a connection to a MySQL server.
$mysqli = new mysqli($server, $username, $password, $database);

/* check connection */
if ($mysqli->connect_errno) {
	printf("Connect failed: %s\n", $mysqli->connect_error);
	exit();
}

$stmt = $mysqli->stmt_init();

$stmt->prepare("SELECT s.id, s.verwarmingType FROM ESF_survey s INNER JOIN ESF_users u ON u.id = s.id WHERE sessionId = ?");
$stmt->bind_param('s', $_SESSION['id']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($userId, $verwarmingType);
$stmt->fetch();
$stmt->close();

//echo $userId;
//echo $verwarmingType;

?>

<body>
	<div class="headerWrapper">
		<a href="ESF_logout.php">Log out</a>
	</div>
	<div class="titleWrapper">
		<h1>home</h1>
	</div>
	<div class="contentWrapper">
		<form action="values_db.php" method="post">
			<fieldset>
				<legend>Ingeven energiegegevens</legend>
				<!-- <label for="tijd">Tijdstip</label>								<input type="datetime" id="tijd" name="tijd" placeholder="dd/mm/yyyy HH:MM" pattern="/([0-2][0-9]{3})\-([0-1][0-9])\-([0-3][0-9])T([0-5][0-9])\:([0-5][0-9])\:([0-5][0-9])(Z|([\-\+]([0-1][0-9])\:00))/" required><br> --!>

				<label for="elektriciteit">Waarde elektriciteitsmeter</label>	<input type="number" id="elektriciteit" name="elektriciteit" required>&nbsp;kWh<br>
				<? if ($verwarmingType == 'A') { ?><label for="gas">Waarde gasmeter</label>						<input type="number" id="gas" name="gas" required>&nbsp;m&sup3;<br><? } ?>

				<input type="submit" value="Indienen" class="button">
			</fieldset>
		</form>
		<div>
			<p>Laatste waarden:</p>
			<?
				$stmt = $mysqli->stmt_init();

				$stmt->prepare("SELECT time, elektriciteit, gas FROM ESF_values WHERE id = ? ORDER BY time DESC LIMIT 0,5");
				$stmt->bind_param('i', $userId);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($time, $elektriciteit, $gas);

				while($stmt->fetch()){
					?><p>Tijdstip: <? echo $time ?>, Elektriciteit: <? echo $elektriciteit ?> kWh, <? if($verwarmingType == 'A'){ ?>Gas: <? echo $gas ?> m&sup3 <? } ?></p><?
				}
				$stmt->close();
			?>
		</div>
	</div>
</body>
</html>