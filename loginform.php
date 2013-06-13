<? include('header.php'); ?>
Log In
<? include('header2.php'); ?>
<form name="login" id="login" action="login_db.php" method="post" class='centered text-center'>
	<fieldset style="padding-top:10px;">
		<!-- <label style="margin-top:3px;">E-mailadres</label>	 -->
        <input type="email" name="email" placeholder="E-mail" required><br>
		<!-- <label style="margin-top:3px;">Wachtwoord</label>	 -->
        <input type="password" name="pass" placeholder="Wachtwoord" required>
		<p><a href="register.php">Don't have an account? Register here</a></p>
		<input class="btn btn-success" type="submit" value="Log in">
	</fieldset>
</form>
</div>
<div class="footerWrapper">
</div>
