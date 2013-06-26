function validation() {
	'use strict';
	var email = document.getElementById('login-box-name').value,
		pass = document.getElementById('login-box-password').value,
		area = document.getElementById('msg-area');

	if (email == '') {
		$(area).html('<div class="alert alert-error">Email is require!</div>');
		return false;
	} else if (pass == '') {
		$(area).html('<div class="alert alert-error">Password is require!</div>');
		return false;
	} else if (! emailValid(email)) {
		$(area).html('<div class="alert alert-error">Email is invalid!</div>');
		return false;
	}

	return true;
}

// Email validation
// Original from http://stackoverflow.com/questions/46155/validate-email-address-in-javascript
function emailValid(email) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

$('#login-box-submit').click(function(e) {

	if (validation()) {
		return true;
	} else {
		return false;
	}

});
