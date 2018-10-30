try {
	var user = {
		username: "",
		password: ""
	};


	$('#user_name').on('input',function() {
		user.username = $('#user_name').val();
	});

	$('#password').on('input',function() {
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
					if (data.trim().toUpperCase().match(/Success/i)) {
						window.localStorage.setItem('CurrentLogger',user.username);
						window.location.href = "home.html";

					} else {
						SendToast("Account Doesn't Exist");
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					SendToast("Connection Failed!");
				}
			});
		}
	}

	function SendToast(message){
		$('.toast').hide();

		var toastHTML = "<span style='color: white; word-break: keep-all;  width: 70%; font-size: 1em;'>" +message+ "</span><button style='color: grey; width: 30%;' class='btn-flat toast-action'>Close</button>";
		M.toast({
			html: toastHTML
		});

		$('.toast-action').click(function() {
			M.Toast.dismissAll();
		});
	}

} catch (e) {
	console.error("SHIT "+ e);
}
