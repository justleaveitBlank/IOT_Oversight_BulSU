var no_notifs = 0;
var notif_id = 0;
var timearray = [0, 5, 15, 30, 60, 360, 720];
var selected_time = 69;
var triggered_notif = 0;
var maxnotif = 0;
var diff = 0;
var hourvalue = 0;

$('#Restrictions').change(function() {
	var value = $(this).find(":selected").attr('value');
	hourvalue = ($(this).find(":selected").text().match(/No Restrictions/i)) ? "Unlimited Time" : $(this).find(":selected").text();
	selected_time = timearray[parseInt(value)];
});

$('.consumption_btn').click(function() {
	var app_id = $(this).attr('id');
	notif_id = $(this).closest('.card').attr('id');
	$('#accept_limit').attr('name', app_id);
});

$('#d_limit').blur(function() {
	if ($(this).val().trim() == "0") {
		$(this).removeClass("invalid");
		$(this).addClass("valid");
	}
	if (!$(this).hasClass('invalid')) {
		$('#accept_limit').addClass('modal-close');
	} else {
		$('#accept_limit').removeClass('modal-close');
	}
});

$('#accept_limit').mouseup(function() {
	var newlimit = $('#d_limit').val().trim();
	if (newlimit != "") {
		if (!$('#d_limit').hasClass('invalid')) {
			$.ajax({
				type: "POST",
				data: "updatelimit=" + newlimit + "&notif=" + notif_id,
				url: 'http://'+deviceHost+'/notifmethods.php',
				crossDomain: true,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				success: function(data) {
					console.log(data.trim());
					if(data.trim().match(/success/i)){
						M.Toast.dismissAll();
						var toastHTML = "<span style='color: white; width: 70%;'>Limit Updated!</span><button style='color: grey; width: 30%;' class='btn-flat toast-action'>Close</button>";
						M.toast({
							html: toastHTML
						});

						$('.toast-action').click(function() {
							M.Toast.dismissAll();
						});
					}
				}
			});
		}
	}
});

$('#allow_app_btn').mousedown(function() {
	if (selected_time != 69) {
		$(this).addClass('modal-close');
		//if(data.trim().match(/success/i)){
			SendToastMessage("Appliance Granted for " +hourvalue+ "!");
		//}
	} else {
		$(this).removeClass('modal-close');
		SendToastMessage("Please Select a Restriction to Allow Appliance Access!");
	}
});

$('#allow_app_btn').mouseup(function() {
	if (selected_time != 69) {
		var app_id = $(this).attr('name');
		$.ajax({
			type: "POST",
			data: "allowapp=" + app_id + "&notif=" + notif_id,
			url: 'http://'+deviceHost+'/notifmethods.php',
			crossDomain: true,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			success: function(data) {
				console.log(data.trim());
				if(data.trim().match(/success/i)){
					throwOnResolved(app_id);
				}
			}
		});
	}
});


function ActivateButtons() {
	$('.ignore').click(function() {
		var notif_id = $(this).attr('id');
		var app = $(this).attr('name');
		$(this).closest('.row').fadeOut(function() {
			$(this).remove();
			ignorenotif(notif_id,app);
		});
	});

	$('.register-trigger').click(function() {
		var app_id = $(this).attr('id');
		notif_id = $(this).closest('.card').attr('id');
		$('#d_id').val(app_id);
		$('#d_id_label').addClass('active');
	});

	$('.consumption_btn').click(function() {
		var app_id = $(this).attr('id');
		notif_id = $(this).closest('.card').attr('id');
		var minimum = $(this).closest('.card').attr('name');
		$('#d_limit').attr('min', parseFloat(minimum));
		$('#accept_limit').attr('name', app_id);

		for (var i = 0; i < 4; i++) {
			$('#updateLimit').find('em').eq(i).text($(this).closest('.card').find('em').eq(i).text());
		}
	});

	$('.allow-trigger').click(function() {
		var app_id = $(this).attr('id');
		notif_id = $(this).closest('.card').attr('id');
		var minimum = $(this).closest('.card').attr('name');
		$('#allow_app_btn').attr('name', app_id);
	});
}

function ignorenotif(id,app) {
	$.ajax({
		type: "POST",
		data: "ignorenotif=" + id,
		url: 'http://'+deviceHost+'/notifmethods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			console.log(data);
			if(data.trim().match(/success/i)){
				SendToastMessage("Notification Removed!");
				throwOnResolved(app);
			}
		}
	});
}

function throwOnResolved(id){
	var arePluggedDevices = 2,
			ns="false";
	$.ajax({
		type: "GET",
		url: "http://" + deviceHost + "/signedPowerData.php?UID="+id+"&powerdata=ae113a20||224.20||0.01||0.00||0.00&notifStat="+ns+"&aDevice="+arePluggedDevices,
		crossDomain: true,
		dataType: "text",
		success: function (data) {
			console.log(data.trim());
			// console.log("http://" + deviceHost + "/signedPowerData.php?UID="+id+"&powerdata=ae113a20||224.20||0.01||0.00||0.00&notifStat="+ns+"&aDevice="+arePluggedDevices);
		}
	});
}

function SendToastMessage(Toasttext){
	M.Toast.dismissAll();
	var toastHTML = "<span style='color: white; width: 70%; font-size:1em;'>"+Toasttext+"</span><button style='color: grey; width: 30%;' class='btn-flat toast-action'>Close</button>";
	M.toast({html: toastHTML});

	$('.toast-action').click(function(){
		 M.Toast.dismissAll();
	});
}

function checknotifs() {
	//cordova.plugins.notification.local.cancelAll(function() {console.log("done");	}, this);

	$.ajax({
		type: "POST",
		data: "countnotifs="+maxnotif+"&notifs="+no_notifs,
		url: 'http://'+deviceHost+'/notifmethods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			console.log(data.trim());
			if(no_notifs>0){
				$('#nonotifnotice').hide();
			}
			var res = data.trim().split("|");
			if (data.trim().match(/RELOAD/i)) {
				$('#nonotifnotice').hide();
				if(res[0]>no_notifs){
					diff = res[0] - no_notifs;
					no_notifs = res[0];
					maxnotif = res[1];
				}
				loadnotifs();
			} else if (res[0] == 0) {
				$('#nonotifnotice').show();
			}
		}
	});
}

function loadnotifs() {
	$.ajax({
		type: "POST",
		data: "loadnotifs=1",
		url: 'http://'+deviceHost+'/notifmethods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			$('#notifholder').html(data);
			ActivateButtons();
		}
	});
}
