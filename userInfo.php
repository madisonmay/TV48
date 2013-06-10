<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>ESF</title>
<!-- 	<link rel="stylesheet" type='text/css' href="stylesheets/bootstrap-combined.min.css">
    <link rel="stylesheet" type='text/css' href="stylesheets/jquery-ui.css">
    <link rel="stylesheet" type='text/css' href="style.css"> -->
	<link href="ESF.css" rel="stylesheet" type="text/css" />
    <script src="scripts/jquery.min.js"></script>
    <script src="scripts/bootstrap.min.js"></script>
    <script src="scripts/jquery-ui.min.js"></script>
	<? include ("check.php"); ?>
</head>
<body>
	<div class="headerWrapper">
		<a href="logout.php">Log out</a>
	</div>
	<div class="titleWrapper">
		<h1>Vragenlijst gebouw</h1>
	</div>
	<div class="contentWrapper">
		<form action="userInfo_db.php" method="post">
			<fieldset>
				<legend>Algemene gegevens kot</legend>
				<p>Type bebouwing:</p>
				<label for="bouwTypeA">Open</label>								<input type="radio" id="bouwTypeA" name="bouwType" value="A" required><br>
				<label for="bouwTypeB">Half open</label>						<input type="radio" id="bouwTypeB" name="bouwType" value="B" required><br>
				<label for="bouwTypeC">Gesloten</label>							<input type="radio" id="bouwTypeC" name="bouwType" value="C" required>
				<br>
				<p>Bouwjaar:</p>
				<label for="bouwjaarA">Jaar</label>         					<input type="text" id="bouwjaarA" name="bouwjaar"><br>
				<label for="bouwjaar">Geen idee</label>							<input type="checkbox" id="bouwjaar" name="bouwjaar" value="X">
				<br>
				<p>Gerenoveerd (isolatie, nieuwe vensters, nieuwe verwarming, ...):</p>
				<label for="gerenoveerdA">Ja</label>							<input type="radio" id="gerenoveerdA" name="gerenoveerd" value="A" required>
				<label for="renovatiejaar">Jaar</label>							<input type="text" id="renovatiejaar" name="renovatiejaar"><br>
				<label for="gerenoveerdB">Nee</label>							<input type="radio" id="gerenoveerdB" name="gerenoveerd" value="B" required><br>
				<label for="gerenoveerdX">Geen idee</label>						<input type="radio" id="gerenoveerdX" name="gerenoveerd" value="X" required>
				<br>
				<p>Woning is voldoende geïsoleerd:</p>
				<label for="geisoleerdA">Ja</label>								<input type="radio" id="geisoleerdA" name="geisoleerd" value="A" required><br>
				<label for="geisoleerdB">Nee</label>							<input type="radio" id="geisoleerdB" name="geisoleerd" value="B" required><br>
				<label for="geisoleerdX">Geen idee</label>						<input type="radio" id="geisoleerdX" name="geisoleerd" value="X" required>
				<br>
				<p>Aantal kamers:</p>
				<label for="aantalKamers">Aantal</label>						<input type="text" id="aantalKamers" name="aantalKamers" value="0"><br>
				<label for="kamerOppervlakte">Gemiddelde oppervlakte</label>	<input type="text" id="kamerOppervlakte" name="kamerOppervlakte">&nbsp;m&sup2;
				<br>
				<p>Aantal studio's:</p>
				<label for="aantalStudios">Aantal</label>						<input type="text" id="aantalStudios" name="aantalStudios" value="0"><br>
				<label for="studioOppervlakte">Gemiddelde oppervlakte</label>	<input type="text" id="studioOppervlakte" name="studioOppervlakte">&nbsp;m&sup2;
				<br>
				<p><abbr title="Energieprestatiecertificaat">EPC</abbr> label:</p>
				<label for="epcLabelX">Label</label>							<input type="text" id="epcLabelX" name="epcLabel" class="long"><br>
				<label for="epcLabel">Geen idee</label>							<input type="checkbox" id="epcLabel" name="epcLabel" value="X">
			</fieldset>
			<fieldset>
				<legend>Verwarming</legend>
				<p>Type verwarming:</p>
				<label for="verwarmingTypeA">Aardgas</label>					<input type="radio" id="verwarmingTypeA" name="verwarmingType" value="A" required><br>
				<label for="verwarmingTypeB">Stookolie</label>					<input type="radio" id="verwarmingTypeB" name="verwarmingType" value="B" required><br>
				<label for="verwarmingTypeC">Elektriciteit</label>				<input type="radio" id="verwarmingTypeC" name="verwarmingType" value="C" required><br>
				<label for="verwarmingTypeD">Andere</label>						<input type="radio" id="verwarmingTypeD" name="verwarmingType" value="D" required>
				<input type="text" name="verwarmingTypeAndere" class="long">
				<label for="verwarmingTypeX">Geen idee</label>					<input type="radio" id="verwarmingTypeX" name="verwarmingType" value="X" required><br>
				<br>
				<p>Verwarmingstoestel:</p>
				<label for="verwarmingstoestelA">Condensatieketel</label>		<input type="radio" id="verwarmingstoestelA" name="verwarmingstoestel" value="A" required><br>
				<label for="verwarmingstoestelB">Hoogrendementsketel</label>	<input type="radio" id="verwarmingstoestelB" name="verwarmingstoestel" value="B" required><br>
				<label for="verwarmingstoestelC">Elektrisch</label>				<input type="radio" id="verwarmingstoestelC" name="verwarmingstoestel" value="C" required><br>
				<label for="verwarmingstoestelD">Pelletketel</label>			<input type="radio" id="verwarmingstoestelD" name="verwarmingstoestel" value="D" required><br>
				<label for="verwarmingstoestelE">Warmtepomp</label>				<input type="radio" id="verwarmingstoestelE" name="verwarmingstoestel" value="E" required><br>
				<label for="verwarmingstoestelF">Andere</label>					<input type="radio" id="verwarmingstoestelF" name="verwarmingstoestel" value="F" required>
				<input type="text" name="verwarmingstoestelAndere" class="long">
				<label for="verwarmingstoestelX">Geen idee</label>				<input type="radio" id="verwarmingstoestelX" name="verwarmingstoestel" value="X" required><br>
				<br>
				<p>Ouderdom verwarmingstoestel:</p>
				<label for="ouderdomVerwarmingstoestel">Jaar</label>			<input type="text" id="ouderdomVerwarmingstoestel" name="ouderdomVerwarmingstoestel" required>
				<br>
				<p>Thermostaat:</p>
				<label for="thermostaatA">Radiatorthermostaat</label>			<input type="radio" id="thermostaatA" name="thermostaat" value="A" required><br>
				<label for="thermostaatB">Centrale thermostaat</label>			<input type="radio" id="thermostaatB" name="thermostaat" value="B" required><br>
				<label for="thermostaatC">Andere thermostaat</label>			<input type="radio" id="thermostaatC" name="thermostaat" value="C" required><br>
				<label for="thermostaatX">Geen idee</label>						<input type="radio" id="thermostaatX" name="thermostaat" value="X" required>
			</fieldset>
			<fieldset>
				<legend>Sanitair</legend>
				<p>Toilet:</p>
				<label for="toiletA">Gewoon toilet (9L)</label>				<input type="radio" id="toiletA" name="toilet" value="A" required><br>
				<label for="toiletB">Zuinig toilet (6L)</label>				<input type="radio" id="toiletB" name="toilet" value="B" required><br>
				<label for="toiletC">Gewoon toilet met spaartoets</label>	<input type="radio" id="toiletC" name="toilet" value="C" required><br>
				<label for="toiletD">Zuinig toilet met spaartoets</label>	<input type="radio" id="toiletD" name="toilet" value="D" required><br>
				<label for="toiletX">Geen idee</label>						<input type="radio" id="toiletX" name="toilet" value="X" required>
				<br>
				<p>Spaardouchekop:</p>
				<label for="spaardouchekopA">Ja</label>						<input type="radio" id="spaardouchekopA" name="spaardouchekop" value="A" required><br>
				<label for="spaardouchekopB">Nee</label>					<input type="radio" id="spaardouchekopB" name="spaardouchekop" value="B" required><br>
				<label for="spaardouchekopX">Geen idee</label>				<input type="radio" id="spaardouchekopX" name="spaardouchekop" value="X" required>
				<br>
				<p>Regenwaterrecuperatie:</p>
				<label for="regenwaterA">Ja</label>							<input type="radio" id="regenwaterA" name="regenwater" value="A" required><br>
				<label for="regenwaterB">Nee</label>						<input type="radio" id="regenwaterB" name="regenwater" value="B" required><br>
				<label for="regenwaterX">Geen idee</label>					<input type="radio" id="regenwaterX" name="regenwater" value="X" required>
				<br>
			</fieldset>
			<fieldset>
				<legend>Keuken</legend>
				<p>Type kookplaat:</p>
				<label for="kookplaatA">Elektrisch</label>		<input type="radio" id="kookplaatA" name="kookplaat" value="A" required><br>
				<label for="kookplaatB">Gas</label>				<input type="radio" id="kookplaatB" name="kookplaat" value="B" required><br>
				<label for="kookplaatC">Inductie</label>		<input type="radio" id="kookplaatC" name="kookplaat" value="C" required><br>
				<label for="kookplaatD">Andere</label>			<input type="radio" id="kookplaatD" name="kookplaat" value="D" required>
				<input type="text" name="kookplaatAndere" class="long">
				<br>
				<p>Apparaten:</p>
				<label for="apparatenA">Microgolfoven</label>	<input type="checkbox" id="apparatenA" name="apparaten[]" value="A"><br>
				<label for="apparatenB">Koffiemachine</label>	<input type="checkbox" id="apparatenB" name="apparaten[]" value="B"><br>
				<label for="apparatenC">Gewone oven</label>		<input type="checkbox" id="apparatenC" name="apparaten[]" value="C"><br>
				<label for="apparatenD">Broodrooster</label>	<input type="checkbox" id="apparatenD" name="apparaten[]" value="D"><br>
				<label for="apparatenE">Waterkoker</label>		<input type="checkbox" id="apparatenE" name="apparaten[]" value="E"><br>
				<label for="apparatenF">Andere</label>			<input type="checkbox" id="apparatenF" name="apparaten[]" value="F">
				<input type="text" name="apparatenAnder" class="long">
				<br>
				<p>Koelkast:</p>
				<label for="koelkastA">A+++</label>				<input type="checkbox" id="koelkastA" name="koelkast[]" value="A">
				<label for="#koelkastA">Aantal</label>			<input type="text" id="#koelkastA" name="koelkastAantalA3" value="0"><br>
				<label for="koelkastB">A++</label>				<input type="checkbox" id="koelkastB" name="koelkast[]" value="B">
				<label for="#koelkastB">Aantal</label>			<input type="text" id="#koelkastB" name="koelkastAantalA2" value="0"><br>
				<label for="koelkastC">A+</label>				<input type="checkbox" id="koelkastC" name="koelkast[]" value="C">
				<label for="#koelkastC">Aantal</label>			<input type="text" id="#koelkastC" name="koelkastAantalA1" value="0"><br>
				<label for="koelkastD">A</label>				<input type="checkbox" id="koelkastD" name="koelkast[]" value="D">
				<label for="#koelkastD">Aantal</label>			<input type="text" id="#koelkastD" name="koelkastAantalA" value="0"><br>
				<label for="koelkastE">B</label>				<input type="checkbox" id="koelkastE" name="koelkast[]" value="E">
				<label for="#koelkastE">Aantal</label>			<input type="text" id="#koelkastE" name="koelkastAantalB" value="0"><br>
				<label for="koelkastF">&lt; B</label>			<input type="checkbox" id="koelkastF" name="koelkast[]" value="F">
				<label for="#koelkastF">Aantal</label>			<input type="text" id="#koelkastF" name="koelkastAantalkB" value="0"><br>
				<label for="koelkastX">Geen idee</label>		<input type="checkbox" id="koelkastX" name="koelkast[]" value="X">
				<label for="#koelkastX">Aantal</label>			<input type="text" id="#koelkastX" name="koelkastAantalGeenIdee" value="0">
				<br>
				<p>Diepvries:</p>
				<label for="diepvriesA">A+++</label>			<input type="checkbox" id="diepvriesA" name="diepvries[]" value="A">
				<label for="#diepvriesA">Aantal</label>			<input type="text" id="#diepvriesA" name="diepvriesAantalA3" value="0"><br>
				<label for="diepvriesB">A++</label>				<input type="checkbox" id="diepvriesB" name="diepvries[]" value="B">
				<label for="#diepvriesB">Aantal</label>			<input type="text" id="#diepvriesB" name="diepvriesAantalA2" value="0"><br>
				<label for="diepvriesC">A+</label>				<input type="checkbox" id="diepvriesC" name="diepvries[]" value="C">
				<label for="#diepvriesC">Aantal</label>			<input type="text" id="#diepvriesC" name="diepvriesAantalA1" value="0"><br>
				<label for="diepvriesD">A</label>				<input type="checkbox" id="diepvriesD" name="diepvries[]" value="D">
				<label for="#diepvriesD">Aantal</label>			<input type="text" id="#diepvriesD" name="diepvriesAantalA" value="0"><br>
				<label for="diepvriesE">B</label>				<input type="checkbox" id="diepvriesE" name="diepvries[]" value="E">
				<label for="#diepvriesE">Aantal</label>			<input type="text" id="#diepvriesE" name="diepvriesAantalB" value="0"><br>
				<label for="diepvriesF">&lt; B</label>			<input type="checkbox" id="diepvriesF" name="diepvries[]" value="F">
				<label for="#diepvriesF">Aantal</label>			<input type="text" id="#diepvriesF" name="diepvriesAantalkB" value="0"><br>
				<label for="diepvriesX">Geen idee</label>		<input type="checkbox" id="diepvriesX" name="diepvries[]" value="X">
				<label for="#diepvriesX">Aantal</label>			<input type="text" id="#diepvriesX" name="diepvriesAantalGeenIdee" value="0">
				<br>
			</fieldset>
			<fieldset>
				<legend>Ramen</legend>
				<label for="ramenA">Enkel</label>				<input type="radio" id="ramenA" name="ramen" value="A" required><br>
				<label for="ramenB">Dubbel</label>				<input type="radio" id="ramenB" name="ramen" value="B" required><br>
				<label for="ramenC">Hoogrendementsglas</label>	<input type="radio" id="ramenC" name="ramen" value="C" required><br>
				<label for="ramenD">Driedubbel</label>			<input type="radio" id="ramenD" name="ramen" value="D" required><br>
				<label for="ramenE">Andere</label>				<input type="radio" id="ramenE" name="ramen" value="E" required>
				<input type="text" name="ramenAnder" class="long">
				<label for="ramenX">Geen idee</label>			<input type="radio" id="ramenX" name="ramen" value="X" required>
				<br>
			</fieldset>
			<fieldset>
				<legend>Verlichting</legend>
				<p>Type:</p>
				<label for="lichtenA">Gloeilamp</label>		<input type="checkbox" id="lichtenA" name="lichten[]" value="A"><br>
				<label for="lichtenB">Spaarlamp</label>		<input type="checkbox" id="lichtenB" name="lichten[]" value="B"><br>
				<label for="lichtenC">LED</label>			<input type="checkbox" id="lichtenC" name="lichten[]" value="C"><br>
				<label for="lichtenD">TL</label>			<input type="checkbox" id="lichtenD" name="lichten[]" value="D"><br>
				<label for="lichtenE">Andere</label>			<input type="checkbox" id="lichtenE" name="lichten[]" value="E">
				<input type="text" name="lichtenAnder" class="long">
				<label for="lichtenX">Geen idee</label>		<input type="checkbox" id="lichtenX" name="lichten[]" value="X"><br>
				<br>
				<p>Timer:</p>
				<label for="timerA">Ja</label>				<input type="radio" id="timerA" name="timer" value="A" required><br>
				<label for="timerB">Nee</label>				<input type="radio" id="timerB" name="timer" value="B" required><br>
				<label for="timerX">Geen idee</label>		<input type="radio" id="timerX" name="timer" value="X" required>
				<br>
				<p>Bewegingssensor:</p>
				<label for="sensorA">Ja</label>				<input type="radio" id="sensorA" name="sensor" value="A" required><br>
				<label for="sensorB">Nee</label>			<input type="radio" id="sensorB" name="sensor" value="B" required><br>
				<label for="sensorX">Geen idee</label>		<input type="radio" id="sensorX" name="sensor" value="X" required>
				<br>
			</fieldset>
			<fieldset>
				<legend>Ventilatie</legend>
				<p>Actieve ventilatie:</p>
				<label for="ventilatieA">Ja</label>			<input type="radio" id="ventilatieA" name="ventilatie" value="A" required><br>
				<label for="ventilatieB">Nee</label>		<input type="radio" id="ventilatieB" name="ventilatie" value="B" required><br>
				<label for="ventilatieX">Geen idee</label>	<input type="radio" id="ventilatieX" name="ventilatie" value="X" required>
				<br>
			</fieldset>
			<fieldset>
				<legend>Facturatiekosten</legend>
				<p>Aparte facturatie per student voor:</p>
				<label for="facturatieA">Elektriciteit</label>				<input type="checkbox" id="facturatieA" name="facturatie[]" value="A"><br>
				<label for="facturatieB">Verwarming</label>					<input type="checkbox" id="facturatieB" name="facturatie[]" value="B"><br>
				<label for="facturatieC">Water</label>						<input type="checkbox" id="facturatieC" name="facturatie[]" value="C"><br>
				<label for="facturatieD">Geen aparte facturatie</label>		<input type="checkbox" id="facturatieD" name="facturatie[]" value="D"><br>
				<label for="facturatieX">Geen idee</label>					<input type="checkbox" id="facturatieX" name="facturatie[]" value="X">
			</fieldset>
			<fieldset>
				<legend>Formulier verzenden</legend>
				<input type="submit" value="Indienen" class="button">
			</fieldset>
		</form>
	</div>
	<div class="footerWrapper">
	</div>
</body>
</html>