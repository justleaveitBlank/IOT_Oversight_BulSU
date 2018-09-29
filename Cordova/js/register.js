var registrant = {
	firstname: "",
	lastname: "",
	username: "",
	email: "",
	contact: "",
	password: ""
};

var r_username_class = $('#r_username').attr('class');
var r_email_class = $('#r_email').attr('class');

$('#r_username').keyup(function() {
	if ($(this).val().trim() != "") {
		validate('username', $(this).val());
	}
});

$('#r_username').blur(function() {
	if ($(this).val().trim() != "") {
		validate('username', $(this).val());
	}
});

$('#r_email').keyup(function() {
	if ($(this).val().trim() != "") {
		validate('email', $('#r_email').val());
	}
});

$('#r_email').blur(function() {
	if ($(this).val().trim() != "") {
		validate('email', $('#r_email').val());
	}
});

var pass_class = $('#r_c_password').attr('class');
$('#r_password').keyup(function() {
	registrant.password = $(this).val();
	if ($(this).val() != "" && $('#r_c_password').val() != "") {
		check_pass(pass_class);
	}
});

var c_pass_class = $('#r_c_password').attr('class');
$('#r_c_password').keyup(function() {
	$(this).attr('class', pass_class);
	check_pass(c_pass_class);
});

$('#r_contact').keyup(function() {
	registrant.contact = $(this).val();
});

$('#r_firstName').keyup(function() {
	registrant.firstname = $(this).val();
});


$('#r_lastName').keyup(function() {
	registrant.lastname = $(this).val();
});

$('#submit_register').click(function() {
	register();
});

function check_pass(pass_class) {
	if ($('#r_password').val() != "") {
		if ($("#r_password").val() == $('#r_c_password').val()) {
			registrant.password = $("#r_password").val();
			$('#r_c_password').attr('class', pass_class + ' valid');
		} else {
			$('#r_c_password').attr('class', pass_class + ' invalid');
		}
	}
}

function register() {
	var firstname = "not";
	var lastname = "not";
	var username = "not";
	var email = "not";
	var contact = "not";
	var password = "not";
	var con_password = "not";
	if ($('#r_firstName').attr('class').includes(' valid')) firstname = 'okay';
	if ($('#r_lastName').attr('class').includes(' valid')) lastname = 'okay';
	if ($('#r_username').attr('class').includes(' valid')) username = 'okay';
	if ($('#r_email').attr('class').includes(' valid')) email = 'okay';
	if ($('#r_contact').attr('class').includes(' valid')) contact = 'okay';
	if ($('#r_password').attr('class').includes(' valid')) password = 'okay';
	if ($('#r_c_password').attr('class').includes(' valid')) con_password = 'okay';

	if (firstname == "okay" && lastname == "okay" && username == "okay" && email == "okay" && contact == "okay" && password == "okay" && con_password == "okay") {
		var params = "register_data=" + JSON.stringify(registrant);
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200) {
				console.log("Data Sent Successfully!"); // just for testing
				console.log((xhr.responseText).trim()); // just for testing
			}
		}
		xhr.open("POST", "http://"+deviceHost+"/methods.php", true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.send(params);
		return false;
	}
}


function validate(type, value) {
	var params = "validate=" + type + "&value=" + value;
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && xhr.status == 200) {
			console.log("Data Sent Successfully");
			console.log((xhr.responseText).trim());
			if (type == 'username') {
				if (xhr.responseText.trim() == 'valid') {
					registrant.username = value;
					$('#r_username').attr('class', r_username_class + ' valid');
				} else {
					$('#r_username').attr('class', r_username_class + ' invalid');
				}
			} else {
				if (xhr.responseText.trim() == 'valid') {
					registrant.email = value;
					$('#r_email').attr('class', r_username_class + ' valid');
				} else {
					$('#r_email').attr('class', r_username_class + ' invalid');
				}
			}
		}
	}
	xhr.open("POST", "http://"+deviceHost+"/methods.php", true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.send(params);
	return false;
}