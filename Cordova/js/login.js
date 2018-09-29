var user = {
	username: "",
	password: ""
};


$('#user_name').keyup(function() {
	user.username = $('#user_name').val();
});

$('#password').keyup(function() {
	user.password = $('#password').val();
});

var loginform = document.getElementById('loginForm');
loginform.onsubmit = function(event) {
	event.preventDefault();
	login();
};

function login() {
	if (user.username != "" && user.password != "") {
		$.ajax({
			type: "POST",
			url: "http://" + deviceHost + "/methods.php",
			data: "login_data=" + JSON.stringify(user),
			crossDomain: true,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			success: function(data) {
				console.log("Data Sent Successfully!"); // just for testing
				console.log(data.trim().toUpperCase()); // just for testing
				if (!data.trim().toUpperCase().match(/ACCOUNT DOESN'T EXIST/i)) {
					window.location.href = "home.html";
				} else {
					var toastHTML = "<span style='color: white; width: 70%;'>Account Doesn't Exist</span><button style='color: grey; width: 30%;' class='btn-flat toast-action'>Close</button>";
					M.toast({
						html: toastHTML
					});

					$('.toast-action').click(function() {
						M.Toast.dismissAll();
					});
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				M.Toast.dismissAll();
				var toastHTML = "<span style='color: white; width: 70%;'>Connection to Server Failed!</span><button style='color: grey; width: 30%;' class='btn-flat toast-action'>Close</button>";
				M.toast({
					html: toastHTML
				});

				$('.toast-action').click(function() {
					M.Toast.dismissAll();
				});
			}
		});
	}
}