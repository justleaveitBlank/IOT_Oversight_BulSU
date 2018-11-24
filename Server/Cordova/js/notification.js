try {
	var flag = 0;
	var no_notifs = window.localStorage.getItem('no_notifs');
	var maxnotif = window.localStorage.getItem('max_notifs');
	var notif_id = 0;
	var timearray = [0, 5, 15, 30, 60, 360, 720, 1440];
	var selected_time = 69;
	var triggered_notif = 0;
	var diff = 0;
	var hourvalue = "";

	$('#Restrictions').change(function() {
		var value = $(this).find(":selected").attr('value');
		hourvalue = ($(this).find(":selected").text().match(/No Restrictions/i)) ? "Unlimited Time" : $(this).find(":selected").text();
		selected_time = timearray[parseInt(value)];
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
						if(data.trim().match(/success/i)){
							SendToastMessage("Limit Updated!");
							$(".row[name='"+notif_id+"']").remove();
						}
					}
				});
			}
		}
	});

	$('#allow_app_btn').mousedown(function() {
		if (selected_time != 69) {
			$(this).addClass('modal-close');
				SendToastMessage("Appliance Granted for " +hourvalue+ "!");
		} else {
			$(this).removeClass('modal-close');
			SendToastMessage("Please Select a Restriction to Allow Appliance Access!");
		}
	});

	$('#allow_app_btn').mouseup(function() {
		if (selected_time != 69) {
			var app_id = $(this).attr('name');
			/*var timeLimit = dateAdd($.now(), "minute" ,selected_time);
			var dateLimit = formatDate(timeLimit);
			if( selected_time == 0 ){
				dateLimit = formatDate(new Date("0000-00-00 00:00:00"));
			} else {
				dateLimit = formatDate(timeLimit);
			}*/
			$.ajax({
				type: "POST",
				data: "allowapp=" +app_id+ "&notif=" +notif_id+ "&timelimit=" +selected_time,
				url: 'http://'+deviceHost+'/notifmethods.php',
				crossDomain: true,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				success: function(data) {
					console.log(data.trim());
					if(data.trim().match(/success/i)){
						throwOnResolved(app_id,"allow");
					}
				}
			});
		}
	});

	function ActivateButtons() {
		$('.ignore').click(function() {
			var notif_id = $(this).attr('id');
			var app = $(this).attr('name');
			ignorenotif(notif_id,app);
		});

		$('.register-trigger').click(function() {
			var app_id = $(this).attr('id');
			notif_id = $(this).closest('.card').attr('id');
			$('#d_id').val(app_id);
			$('#d_id_label').addClass('active');
		});

		$('.consumption_btn').click(function() {
			var app_id = $(this).attr('id');
			var app_name = $(this).closest('card-action').attr('name');
			notif_id = $(this).closest('.consumption_limit').attr('id');
			var consumption_limit = $(this).closest('.consumption_limit').attr('name');
			var consumption_value = $(this).closest('.consumption_value').attr('name');
			if(parseFloat(consumption_limit) > (parseFloat(consumption_value)/1000)){
				$('#d_limit').attr("min",consumption_limit);
			} else {
				$('#d_limit').attr("min",(parseFloat(consumption_value)/1000));
			}

			$('#accept_limit').attr('name', app_id);

			$('#updateLimit').find('em').eq(0).text(app_id);
			$('#updateLimit').find('em').eq(1).text(app_name);
			$('#updateLimit').find('em').eq(2).text(consumption_value + " whr");
			$('#updateLimit').find('em').eq(3).text(consumption_limit + " kwhr");	
		});

		$('.allow-trigger').click(function() {
			var app_id = $(this).attr('id');
			notif_id = $(this).closest('.card').attr('id');
			var minimum = $(this).closest('.card').attr('name');
			$('#allow_app_btn').attr('name', app_id);
		});
	}

	function dateAdd(date, interval, units) {
	  var ret = new Date(date); //don't change original date
	  var checkRollover = function() { if(ret.getDate() != date.getDate()) ret.setDate(0);};
	  switch(interval.toLowerCase()) {
	    case 'year'   :  ret.setFullYear(ret.getFullYear() + units); checkRollover();  break;
	    case 'quarter':  ret.setMonth(ret.getMonth() + 3*units); checkRollover();  break;
	    case 'month'  :  ret.setMonth(ret.getMonth() + units); checkRollover();  break;
	    case 'week'   :  ret.setDate(ret.getDate() + 7*units);  break;
	    case 'day'    :  ret.setDate(ret.getDate() + units);  break;
	    case 'hour'   :  ret.setTime(ret.getTime() + units*3600000);  break;
	    case 'minute' :  ret.setTime(ret.getTime() + units*60000);  break;
	    case 'second' :  ret.setTime(ret.getTime() + units*1000);  break;
	    default       :  ret = undefined;  break;
	  }
	  return ret;
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function formatDate(date){
		var ret = date.getFullYear()+ "-" +(date.getMonth()+1)+ "-" +date.getDate()+ " " +addZero(date.getHours())+ ":" +addZero(date.getMinutes())+ ":" +addZero(date.getSeconds());
		return ret;
	}

	function ignorenotif(id,app) {
		$.ajax({
			type: "POST",
			data: "ignorenotif=" + id,
			url: 'http://'+deviceHost+'/notifmethods.php',
			crossDomain: true,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			success: function(data) {
				if(data.trim().match(/success/i)){
					SendToastMessage("Notification Removed!");
					throwOnResolved(app,"ignore");
				}
			}
		});
	}

	function throwOnResolved(id,from){
		var arePluggedDevices = 0;
		if(from.match(/allow/i)){
			var arePluggedDevices = 3;
			ns="false";
		} else if (from.match(/ignore/i)) {
			var arePluggedDevices = 2;
			ns="false";
		} else {
			var arePluggedDevices = 1;
			ns="true";
		}


		$.ajax({
			type: "GET",
			url: "http://" + deviceHost + "/signedPowerData.php?ts="+$.now()+"&UID="+id+"&powerdata=ae113a20||224.20||0.01||0.00||0.00&notifStat="+ns+"&aDevice="+arePluggedDevices+"&unplugged=false",
			crossDomain: true,
			dataType: "text",
			success: function (data) {
				console.log(data);
				$(".row[name='"+notif_id+"']").fadeOut();
				$(".row[name='"+notif_id+"']").remove();
				// console.log(data.trim());
				// console.log("http://" + deviceHost + "/signedPowerData.php?UID="+id+"&powerdata=ae113a20||224.20||0.01||0.00||0.00&notifStat="+ns+"&aDevice="+arePluggedDevices);
			}
		});
	}

	function SendToastMessage(Toasttext){
		$('.toast').hide();

		M.Toast.dismissAll();
		var toastHTML = "<span style='color: white; word-break: keep-all;  width: 70%; font-size:1em;'>"+Toasttext+"</span><button style='color: grey; width: 30%;' class='btn-flat toast-action'>Close</button>";
		M.toast({html: toastHTML});

		$('.toast-action').click(function(){
			 M.Toast.dismissAll();
		});
	}

	function checknotifs() {
		//cordova.plugins.notification.local.cancelAll(function() {console.log("done");	}, this);
		$.ajax({
		type: "POST",
		data: "ts="+$.now()+"&countnotifs="+maxnotif+"&notifs="+no_notifs,
		url: 'http://'+deviceHost+'/notifmethods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			console.log(data.trim());
			if(no_notifs>0){
				$('#nonotifnotice').hide();
			}
			var res = data.trim().split("|");
			if (data.trim().match(/RELOAD/i) || flag==0) {
				$('#nonotifnotice').hide();
				no_notifs = res[0];
				window.localStorage.setItem('no_notifs',no_notifs);
				maxnotif = res[1];
				window.localStorage.setItem('max_notifs',maxnotif);
				loadnotifs();
				if(data.trim().match(/RELOAD/i) || flag>0){
					//identifyNotifs(res[0]);
				}
			} else if (res[0] == 0) {
				no_notifs = res[0];
				$('#nonotifnotice').show();
			}
			flag++;
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
				loadresolvednotifs();
				ActivateButtons();
			}
		});
	}
	
	function loadresolvednotifs(){
		$.ajax({
			type: "POST",
			data: "loadresolvednotifs=1",
			url: 'http://'+deviceHost+'/notifmethods.php',
			crossDomain: true,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			success: function(data) {
				if(data.trim()==""){
					$('#noresolvednotifnotice').show();
				} else {
					$('#noresolvednotifnotice').hide();
					$('#resolvednotifholder').html(data);
				}
				
			}
		});
	}

} catch (e) {

}
