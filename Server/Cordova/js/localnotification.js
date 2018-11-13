var no_notifs = window.localStorage.getItem('no_notifs');
var maxnotif = window.localStorage.getItem('max_notifs');
var diff = "";

function triggerConsumption(id, app_id) {
	cordova.plugins.notification.local.hasPermission(function(granted) {
		cordova.plugins.notification.local.schedule({
			id: 2,
			title: 'Appliance Almost At Limit',
			text: 'Appliance: ' + app_id + ' Almost At Limit',
			badge: 1
		});
	});
}

function triggerAnoApp(id, app_id) {
	cordova.plugins.notification.local.hasPermission(function(granted) {
		cordova.plugins.notification.local.schedule({
			id: 1,
			title: 'Anonymous Appliance Need Consumption',
			text: 'An Anonymous Appliance is currently plugged and needs consumption!',
			badge: 2
		});
	});
}

function triggerNewApp(id, app_id) {
	cordova.plugins.notification.local.hasPermission(function(granted) {
		cordova.plugins.notification.local.schedule({
			id: 1,
			title: 'Unregistered Appliance Need Consumption',
			text: 'A New Unregistered Appliance Id: ' + app_id + ' is currently plugged and needs consumption!',
			badge: 3
		});
	});
}


function identifyNotifs(num) {
	$.ajax({
		type: "POST",
		data: "getnewnotifs=" + num,
		url: 'http://' + deviceHost + '/notifmethods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			var latest_notif = JSON.parse(data.trim());
			console.log(data.trim());
			if(latest_notif.length == 0){
				ClearNotification();
			}
			for (var i = 0; i < latest_notif.length; i++) {
				if (latest_notif[i].type == 'newanoapp') {
					triggerAnoApp(latest_notif[i].id, latest_notif[i].app);
				} else if (latest_notif[i].type == 'newapp') {
					triggerNewApp(latest_notif[i].id, latest_notif[i].app);
				} else if (latest_notif[i].type == 'consumption') {
					triggerConsumption(latest_notif[i].id, latest_notif[i].app);
				}
			}
		}
	});
}

function checkLocal() {
	$.ajax({
		type: "POST",
		data: "countnotifs="+maxnotif+"&notifs="+no_notifs,
		url: 'http://'+deviceHost+'/notifmethods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			if (data.trim().match(/RELOAD/i)) {
				var res = data.trim().split("|");
				if(res[0]!=no_notifs){
					identifyNotifs(res[0]);
					no_notifs = res[0];
					window.localStorage.setItem('no_notifs',no_notifs);
					maxnotif = res[1];
					window.localStorage.setItem('max_notifs',maxnotif);
				}
			}
		}
	});
}
