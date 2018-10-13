//const socket = io.connect('http://localhost:4000');

if ((window.location.href.match(/index/i)) || (window.location.href.indexOf("html") < 0)) {
	checkiflogged();
} else {
	checkifauthorized();
}

$('#SignOut').click(function() {
	UserLogOut();
});

function UserLogOut(){
	$.ajax({
		type: "POST",
		url: "http://" + deviceHost + "/methods.php",
		data: "signout=" + 1,
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			console.log("Signing Out!");
			window.location = 'index.html';
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			SendErrorMessage();
		}
	});
}

function checkifauthorized() {
	IdentifyUser("index");
}

function checkiflogged() {
	IdentifyUser("home");
}

function IdentifyUser(destination){
	$.ajax({
		type: "POST",
		url: "http://" + deviceHost + "/methods.php",
		data: "islogged=" + 1,
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			if (data.trim().match(/true/i)) {
				if(destination.match(/index/i)){
					console.log("Authorized");
				} else if (destination.match(/home/i)){
					window.location.href = "home.html";
				}
			} else {
				if(destination.match(/index/i)){
					window.location.href = "index.html";
				}
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			SendErrorMessage();
		}
	});
}

function SendErrorMessage(){
	$('.toast').hide();

	var toastHTML = "<span style='color: white; width: 70%; font-size: 1em;'>Something Went Wrong!</span><button style='color: grey; width: 30%;' class='btn-flat toast-action'>Close</button>";
	M.toast({
		html: toastHTML
	});

	$('.toast-action').click(function() {
		M.Toast.dismissAll();
	});
}
