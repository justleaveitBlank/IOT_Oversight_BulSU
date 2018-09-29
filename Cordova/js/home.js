var admin_code = "";
var xmlDoc = "";
var chartlist = [];
var chartcounter = 0;

function loadapps() {
	$.ajax({
		type: "GET",
		url: 'http://' + deviceHost + '/apps.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function (data) {
			$('#registered-apps').html(data);
			initiate_functions();
		}
	});
}

function initiate_functions() {
	load_xml();
	add_appschart();
	add_jqueries();
}

function load_xml() {
	$.ajax({
		url: "http://" + deviceHost + "/xml/overpass.xml",
		dataType: "xml",
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function (data) {
			xmlDoc = data;
			admin_code = xmlDoc.getElementsByTagName('admin')[0].firstChild.data;
		}
	});
}

function add_jqueries() {
	$('.switcher').change(function () {
		var app_uid = $(this).attr('id');
		if (this.checked) {
			app_on(app_uid);
		} else {
			app_off(app_uid);
		}

	});

	$('.d_update_btn').click(function () {
		var appl_id = $(this).closest('.row').find('.appl_id').val();
		var appl_name = $(this).closest('.row').find('.appl_name').val();
		var appl_consumption = $(this).closest('.row').find('.appl_consumption').val();
		var appl_limit = $(this).closest('.row').find('.appl_limit').val();

		$('#d_ID').val(appl_id);
		$('#d_NAME').val(appl_name);
		$('#d_LIMIT').val(appl_limit);

		$(".u_label").each(function (index) {
			$(this).addClass("active");
		});
	});

	$('#pass_accepter').mousedown(function () {
		if ($('#admin_password').val().trim() == admin_code) {
			$(this).addClass('modal-close');
			$(this).addClass('modal-trigger');
			$('#admin_password').val("");
		} else {
			$(this).removeClass('modal-close');
			$(this).removeClass('modal-trigger');
			ToastMessage("Invalid Pass");
		}
	});

	$('.u_field').blur(function () {
		if (($('#d_ID').hasClass('invalid')) || ($('#d_NAME').hasClass('invalid')) || ($('#d_LIMIT').hasClass('invalid'))) {
			$('#d_updater').removeClass('modal-close');
			ToastMessage("Check your Inputs");
		} else {
			$('#d_updater').addClass('modal-close');
		}
	})

	$('.modal-close').click(function () {
		$('.validate').each(function () {
			$(this).removeClass("valid");
			$(this).removeClass("invalid");
		});
	});

	$('#d_updater').click(function () {
		if ($(this).hasClass('modal-close')) {
			var updates = {
				id: $('#d_ID').val().trim(),
				name: $('#d_NAME').val().trim(),
				limit: parseFloat($('#d_LIMIT').val().trim())
			};

			$.ajax({
				type: "POST",
				data: "appl_updates=" + JSON.stringify(updates),
				url: 'http://' + deviceHost + '/methods.php',
				crossDomain: true,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				success: function (data) {
					console.log(data.trim());
				}
			});
		}
	});
}

function ToastMessage(message){
	M.Toast.dismissAll();
	var toastHTML = "<span style='color: white; width: 70%;'>"+message+"</span><button style='color: grey; width: 30%;' class='btn-flat toast-action'>Close</button>";
	M.toast({
		html: toastHTML
	});

	$('.toast-action').click(function () {
		M.Toast.dismissAll();
	});
}

function app_off(app_uid) {
	$.ajax({
		type: "POST",
		data: "off=" + app_uid,
		url: 'http://' + deviceHost + '/methods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function (data) {
			console.log(data.trim());
		}
	});
}

function app_on(app_uid) {
	$.ajax({
		type: "POST",
		data: "on=" + app_uid,
		url: 'http://' + deviceHost + '/methods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function (data) {
			console.log(data.trim());
		}
	});
}

function add_appschart() {
	var month = new Array();
	month[0] = "January";
	month[1] = "February";
	month[2] = "March";
	month[3] = "April";
	month[4] = "May";
	month[5] = "June";
	month[6] = "July";
	month[7] = "August";
	month[8] = "September";
	month[9] = "October";
	month[10] = "November";
	month[11] = "December";
	var cur_date = new Date();
	var n = cur_date.getMonth();
	var months = [];

	for (var i = 0; i <= n; i++) {
		months.push(month[i]);
	}

	var li = document.getElementsByClassName('chartHolder');
	chartcounter = 0;
	for (var i = 1; i <= li.length; i++) {
		var cur_chart = "chart" + i;
		var appl_uid = $('#' + cur_chart).closest('.id_holder').attr('id');
		(function (cur_chart, chartcounter, chartlist) {
			$.ajax({
				type: "POST",
				data: "getconsumptions=" + appl_uid + "&mon=" + n,
				url: 'http://' + deviceHost + '/methods.php',
				crossDomain: true,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				success: function (data) {
					//console.log(data.trim());
					finalize_charts(JSON.parse(data.trim()), cur_chart, months, chartcounter, chartlist);
				}
			});
		})(cur_chart, chartcounter, chartlist);
		chartcounter = i;
	}
}

function finalize_charts(dat_array, cur_chart, months, chartcounter, chartlist) {
	var graph = new Chart($('#' + cur_chart), {
		"type": "line",
		"data": {
			"labels": months,
			"datasets": [{
				"label": "Wattage: ",
				"data": dat_array,
				"fill": false,
				"borderColor": "rgb(75, 192, 192)",
				"lineTension": 0.1
			}]
		},
		"options": {
			animation: {
				duration: 1000, // general animation time
			},
			hover: {
				animationDuration: 1000, // duration of animations when hovering an item
			},
			responsiveAnimationDuration: 1000, // animation duration after a resize
		}
	});
	chartlist.push(graph);
}

function checkapps() {
	var numItems = $('.appliance-info').length;
	var x = 0;
	$.ajax({
		type: "POST",
		data: "checkappchanges=" + numItems,
		url: 'http://' + deviceHost + '/methods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function (data) {
			if (data.trim().match(/Reload/i)) {
				loadapps();
			} else {
				loadinfos();
			}
		}
	});
}

function loadinfos() {
	$.ajax({
		type: "POST",
		data: "loadappinfo=1",
		url: 'http://' + deviceHost + '/methods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function (data) {
			var appinfo = JSON.parse(data.trim());
			var cur_date = new Date();
			var cur_month = cur_date.getMonth();

			for (var i = 0; i < appinfo.length; i++) {
				var appuid = appinfo[i].uid;
				var appname = appinfo[i].appl_name;
				var consump = appinfo[i].current_power_usage;
				var avg = appinfo[i].avg_watthr;
				var cost = appinfo[i].estimated_cost;
				var limit_value = appinfo[i].power_limit_value;
				var checked = (appinfo[i].has_power == 1) ? $('.switcher[name="' + appuid + '"]').attr("checked", "") : $('.switcher[name="' + appuid + '"]').removeAttr("checked");

				$('.applianceName[name="' + appuid + '"]').text(appname);
				$('.kwh[name="' + appuid + '"]').text(consump + " kwh / " + limit_value + " kwh");
				$('.switcher[name="' + appuid + '"]').attr('id', appuid);

				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(0).html("UID: <span style='font-weight: normal; font-size: inherit;'>" + appuid + "</span>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(1).html("Name: <span style='font-weight: normal; font-size: inherit;'>" + appname + "</span>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(2).html("Consumption: <span style='font-weight: normal; font-size: inherit;'>" + consump + " kwh" + "</span>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(3).html("Kilowatt per hour: <span style='font-weight: normal; font-size: inherit;'>" + avg + "</span>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(4).html("Price per KWhr: <span style='font-weight: normal; font-size: inherit;'>" + 0 + "</span>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(5).html("Estimated Price: <span style='font-weight: normal; font-size: inherit;'>" + cost + "</span>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(6).html("Limit: <span style='font-weight: normal; font-size: inherit;'>" + limit_value + "</span>");

				$('.appl_id[name="' + appuid + '"]').val(appuid);
				$('.appl_name[name="' + appuid + '"]').val(appname);
				$('.appl_consumption[name="' + appuid + '"]').val(consump);
				$('.appl_limit[name="' + appuid + '"]').val(limit_value);

				chartlist[i].config.data.datasets[0].data[cur_month] = consump;
				chartlist[i].update();
			}
		}
	});
}

function checkPlugged() {
	$.getJSON('http://' + deviceHost + '/plugged.json', function (data) {
		//------------------------SAVE TO LOCALSTORAGE ----------------------------
		window.localStorage.setItem('pluggedDevices',JSON.stringify(data));
		//-------------------------------------------------------------------------
		var pluggedDevices = 	window.localStorage.getItem('pluggedDevices');
		var data_array = JSON.parse(pluggedDevices);
		$.each(data_array, function (key, val) {
			if (key == "uid" && val == "") {
				$(".appliance-info").each(function () {
					$(this).appendTo($("#registered-apps"));
					$(this).find('.switch').hide();
				});
				$("#noappnotice").show();
			} else if (key == "uid" && val != "") {
				$("#noappnotice").hide();
				$(".appliance-info").each(function () {
					$(this).appendTo($("#registered-apps"));
					$(this).find('.switch').hide();
				});
				if ($("#plugged-apps").find($(".appliance-info[name='" + val + "']")).length == 0) {
					$(".appliance-info[name='" + val + "']").appendTo($("#plugged-apps"));
					$(".appliance-info[name='" + val + "']").find('.switch').show();
				}
				var DefaultData = {
					"uid": "",
					"registered": false
				};
				var resetData = JSON.stringify(DefaultData);
				window.localStorage.setItem('pluggedDevices',JSON.stringify(resetData));
			}
		});
	});
}
checkPlugged();
setInterval(checkPlugged, 2000);

loadapps();
setInterval(checkapps, 500);
setInterval(load_xml, 500);
