function checkPassword(input) {
	if (input.value != document.getElementById('password').value) {
		input.setCustomValidity('De wachtwoorden moeten overeen komen');
	} else {
		// input is valid -- reset the error message
		input.setCustomValidity('');
	}
}
function checkEmail(input) {
	if (input.value != document.getElementById('email').value) {
		input.setCustomValidity('De E-mailadressen moeten overeen komen');
	} else {
		// input is valid -- reset the error message
		input.setCustomValidity('');
	}
}