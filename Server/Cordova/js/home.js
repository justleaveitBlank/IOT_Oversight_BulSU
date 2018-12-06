var admin_code = "";
var xmlDoc = "";
//var chartlist = [];
var chartcounter = 0;
var priceperkwhr = 0;
var numNotifs = "0";
var tries = 0;
var numItems = 0;

function loadapps() {
	$.ajax({
		type: "GET",
		url: 'http://' + deviceHost + '/apps.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function (data) {
			$('#registered-apps').html(data);
			initiate_functions();
			checkapps();
		}
	});
}

function initiate_functions() {
	load_xml();
	//add_appschart();
	add_jqueries();
}

function load_xml() {
	$.ajax({
		url: 'http://'+deviceHost+'/pathtoOverpassXML.php',
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
		var appl_limit = $('.appl_limit[name="' + appl_id + '"]').val();
		var appl_type = $('.appl_type[name="' + appl_id + '"]').val();

		$('#d_ID').val(appl_id);
		$('#d_NAME').val(appl_name);
		$('#d_LIMIT').val(appl_limit);
		
		var ac = ['0','AIR CONDITIONER','BOX FAN','CEILING FAN','DESK FAN','EXHAUST FAN','ORBIT WALL FAN'];
		var com = ['0','LAPTOP COMPUTER','PERSONAL COMPUTER','PRINTER'];
		var ent = ['0','HOME THEATER SYSTEM (5 DVD/CD)','KARAOKE','PLASMA TV','PLAYSTATION','Projection TV','RADIO CASETTE RECORDER','STEREO','TV SET','VCD / DVD / MP3 PLAYER','VHS','XBOX'];
		var kit = ['0','BREAD TOASTER','COFFEE MAKER','FOOD PROCESSOR','FREEZER CHEST','FRYER','GRILLER','INDUCTION / IH COOKER','MEAT CHOPPER','MICROWAVE','OSTERIZER/BLENDER','OVEN TOASTER','POPCORN POPPER','REFRIGERATOR','RICE COOKER','SLOW COOKER','STOVE','TURBO BROILER','WATER DISPENSER','WATER HEATER'];
		var light = ['0','CFL','CHRISTMAS LIGHT','FLUORESCENT LAMP 48','INCANDESCENT BULB','RECHARGEABLE LIGHTS/FANS'];
		var other = ['0','CELLPHONE CHARGER','CLOTHES DRYER','FLAT IRON','FLOOR POLISHER','HAIR DRYER','SEWING MACHINE','VACUUM CLEANER','WASHING MACHINE','WATER DISPENSER','WATER HEATER'];
		console.log(appl_type);
		if(ac.indexOf(appl_type) != -1){
			$('#applianceCategHome').val(1);
			insertOptions(1);
			$('#applianceTypeHome').val(appl_type);
			
		} else if(com.indexOf(appl_type) != -1){
			$('#applianceCategHome').val(2);
			insertOptions(2);
			$('#applianceTypeHome').val(appl_type);
			
		} else if(ent.indexOf(appl_type) != -1){
			$('#applianceCategHome').val(3);
			insertOptions(3);
			$('#applianceTypeHome').val(appl_type);
			
		} else if(kit.indexOf(appl_type) != -1){
			$('#applianceCategHome').val(4);
			insertOptions(4);
			$('#applianceTypeHome').val(appl_type);
			
		} else if(light.indexOf(appl_type) != -1){
			$('#applianceCategHome').val(5);
			insertOptions(5);
			$('#applianceTypeHome').val(appl_type);
			
		} else if(other.indexOf(appl_type) != -1){
			$('#applianceCategHome').val(6);
			insertOptions(6);
			$('#applianceTypeHome').val(appl_type);
		} 
		
		
		$('#applianceTypeHome').formSelect();
		$('#applianceCategHome').formSelect();

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
				limit: parseFloat($('#d_LIMIT').val().trim()),
				type: $('#applianceTypeHome').find(":selected").attr('value')
			};
			$.ajax({
				type: "POST",
				data: "appl_updates=" + JSON.stringify(updates),
				url: 'http://' + deviceHost + '/methods.php',
				crossDomain: true,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				success: function (data) {
					console.log(data.trim());
					if(data.trim().match(/Success/i)){
						ToastMessage("Update Success");
					}
				}
			});
		}
	});

}

function insertOptions(givenValue){
	if(givenValue == 1){
		htmlToAppend = '<option class="AIR-CONDITIONING black-text text-darken-2" value="AIR CONDITIONER">AIR CONDITIONER</option>'+
							'<option class="AIR-CONDITIONING black-text text-darken-2" value="BOX FAN">BOX FAN</option>'+
							'<option class="AIR-CONDITIONING black-text text-darken-2" value="CEILING FAN">CEILING FAN</option>'+
							'<option class="AIR-CONDITIONING black-text text-darken-2" value="DESK FAN">DESK FAN</option>'+
							'<option class="AIR-CONDITIONING black-text text-darken-2" value="EXHAUST FAN">EXHAUST FAN</option>'+
							'<option class="AIR-CONDITIONING black-text text-darken-2" value="ORBIT WALL FAN">ORBIT WALL FAN</option>';
	
	} else if(givenValue == 2){
		htmlToAppend = '<option class="COMPUTERS black-text text-darken-2" value="LAPTOP COMPUTER">LAPTOP COMPUTER</option>'+
							'<option class="COMPUTERS black-text text-darken-2" value="PERSONAL COMPUTER">PERSONAL COMPUTER</option>'+
							'<option class="COMPUTERS black-text text-darken-2" value="PRINTER">PRINTER</option>';
	} else if(givenValue == 3){
		htmlToAppend = '<option class="ENTERTAINMENT black-text text-darken-2" value="HOME THEATER SYSTEM (5 DVD/CD)">HOME THEATER SYSTEM</option>'+
							'<option class="ENTERTAINMENT black-text text-darken-2" value="KARAOKE">KARAOKE</option>'+
							'<option class="ENTERTAINMENT black-text text-darken-2" value="PLASMA TV">PLASMA TV</option>'+
							'<option class="ENTERTAINMENT black-text text-darken-2" value="PLAYSTATION">PLAYSTATION </option>'+
							'<option class="ENTERTAINMENT black-text text-darken-2" value="Projection TV">Projection TV</option>'+
							'<option class="ENTERTAINMENT black-text text-darken-2" value="RADIO CASETTE RECORDER">RADIO CASETTE RECORDER</option>'+
							'<option class="ENTERTAINMENT black-text text-darken-2" value="STEREO">STEREO</option>'+
							'<option class="ENTERTAINMENT black-text text-darken-2" value="TV SET">TV SET</option>'+
							'<option class="ENTERTAINMENT black-text text-darken-2" value="VCD / DVD / MP3 PLAYER">VCD / DVD / MP3 PLAYER</option>'+
							'<option class="ENTERTAINMENT black-text text-darken-2" value="VHS">VHS</option>'+
							'<option class="ENTERTAINMENT black-text text-darken-2" value="XBOX">XBOX</option>';
	
	} else if(givenValue == 4){
		htmlToAppend = '<option class="KITCHEN black-text text-darken-2" value="BREAD TOASTER">BREAD TOASTER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="COFFEE MAKER">COFFEE MAKER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="FOOD PROCESSOR">FOOD PROCESSOR</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="FREEZER CHEST">FREEZER CHEST</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="FRYER">FRYER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="GRILLER">GRILLER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="INDUCTION / IH COOKER">INDUCTION / IH COOKER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="MEAT CHOPPER">MEAT CHOPPER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="MICROWAVE">MICROWAVE</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="OSTERIZER/BLENDER">OSTERIZER/BLENDER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="OVEN TOASTER">OVEN TOASTER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="POPCORN POPPER">POPCORN POPPER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="REFRIGERATOR">REFRIGERATOR</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="RICE COOKER">RICE COOKER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="SLOW COOKER">SLOW COOKER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="STOVE">STOVE</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="TURBO BROILER">TURBO BROILER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="WATER DISPENSER">WATER DISPENSER</option>'+
							'<option class="KITCHEN black-text text-darken-2" value="WATER HEATER">WATER HEATER</option>';
	
	} else if(givenValue == 5){
		htmlToAppend = '<option class="LIGHTINGS black-text text-darken-2" value="CFL">CFL</option>'+
							'<option class="LIGHTINGS black-text text-darken-2" value="CHRISTMAS LIGHT">CHRISTMAS LIGHT</option>'+
							'<option class="LIGHTINGS black-text text-darken-2" value="FLUORESCENT LAMP 48">FLUORESCENT LAMP</option>'+
							'<option class="LIGHTINGS black-text text-darken-2" value="INCANDESCENT BULB">INCANDESCENT BULB</option>'+
							'<option class="LIGHTINGS black-text text-darken-2" value="RECHARGEABLE LIGHTS/FANS">RECHARGEABLE LIGHTS/FANS</option>';
	
	} else if(givenValue == 6){
		htmlToAppend = '<option class="OTHERS black-text text-darken-2" value="CELLPHONE CHARGER">CELLPHONE CHARGER</option>'+
							'<option class="OTHERS black-text text-darken-2" value="CLOTHES DRYER">CLOTHES DRYER</option>'+
							'<option class="OTHERS black-text text-darken-2" value="FLAT IRON">FLAT IRON</option>'+
							'<option class="OTHERS black-text text-darken-2" value="FLOOR POLISHER">FLOOR POLISHER</option>'+
							'<option class="OTHERS black-text text-darken-2" value="HAIR DRYER">HAIR DRYER</option>'+
							'<option class="OTHERS black-text text-darken-2" value="SEWING MACHINE">SEWING MACHINE</option>'+
							'<option class="OTHERS black-text text-darken-2" value="VACUUM CLEANER">VACUUM CLEANER</option>'+
							'<option class="OTHERS black-text text-darken-2" value="WASHING MACHINE">WASHING MACHINE</option>'+
							'<option class="OTHERS black-text text-darken-2" value="WATER DISPENSER">WATER DISPENSER</option>'+
							'<option class="OTHERS black-text text-darken-2" value="WATER HEATER">WATER HEATER</option>';
	}
	disabledOption = '<option class="grey-text text-darken-2" value="0" disabled selected>Choose appliance type</option>';
	$('#applianceTypeHome').html(disabledOption + htmlToAppend);
	$('#applianceTypeHome').prop("disabled", false);
	$('#applianceTypeHome').removeAttr("disabled");
	$('#applianceTypeHome').formSelect();
	$('select').formSelect();
}

function ToastMessage(message){
	$('.toast').hide();

	var toastHTML = "<span style='color: white; word-break: keep-all;  width: 70%; font-size: 1em;'>" +message+ "</span><button style='color: grey; width: 30%;' class='btn-flat toast-action'>Close</button>";
	M.toast({
		html: toastHTML
	});

	$('.toast-action').click(function() {
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
			if(data.trim().match(/Expired/i)){
				ToastMessage("Access Already Expired!");
			} else if(data.trim().match(/Overconsumed/i)){
				ToastMessage("Appliance has over exceeded Limit. Check Notifications!");
			}
		}
	});
}

function add_appschart() {
	var month = new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	var cur_date = new Date();
	var n = cur_date.getMonth();
	//var months = [];

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

/*function finalize_charts(dat_array, cur_chart, months, chartcounter, chartlist) {
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
}*/

function checkapps() {
	numItems = $('.appliance-info').length;
	var x = 0;
	$.ajax({
		type: "POST",
		data: "checkappchanges=" + numItems,
		url: 'http://' + deviceHost + '/methods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function (data) {
			console.log(data);
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
		data: "loadappinfo=1&ts="+$.now(),
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
				var apptype = appinfo[i].appl_type;
				var consump = appinfo[i].current_power_usage;
				var avg = appinfo[i].avg_watthr;
				var cost = parseFloat(appinfo[i].estimated_cost);
				var limit_value;
				var unit;
				var consump_out;
				var avg_out;
				//console.log(avg +" = " +avg/1000);
				
				if((consump/1000).toFixed(2) == 0.00){
					consump_out = (consump*1).toFixed(2)+" Wh";
				}
				else{
					consump_out = (consump/1000).toFixed(2)+" kWh";
				}
				
				
				if((avg/1000).toFixed(2) == 0.00){
					avg_out = (avg*1).toFixed(2) + " Wh";
				}
				else{
					avg_out = (avg/1000).toFixed(2) + " kWh";
				}
				
				if(appname.match(/Anonymous_Appliance/i) || appname.match(/Unregistered_Appliance/i)){
					limit_value = appinfo[i].time_limit_value;
					if(limit_value.match(/0000-00-00 00:00:00/i)){
						limit_value = "Indefinite Time";
					} else {
						var date = new Date(limit_value);
						var options = {year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric'};
						limit_value = date.toLocaleString('en-Us',options);
					}
					unit = "";
				} else {
					limit_value = appinfo[i].power_limit_value;
					original_limit_value = limit_value;
					if(limit_value == 0){
						limit_value = "Unlimited";
						unit = "";
					} else {
						unit = " kWh / mo";
					}

				}
				if(appinfo[i].has_power == 1){
					$('.switcher[name="' + appuid + '"]').attr("checked", "");
				} else {
					$('.switcher[name="' + appuid + '"]').prop("checked",false);
				}

				$('.applianceName[name="' + appuid + '"]').text(appname);
				$('.kwh[name="' + appuid + '"]').text((consump/1000).toFixed(3) + " whr / " + limit_value + unit);
				$('.switcher[name="' + appuid + '"]').attr('id', appuid);

				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(0).html("<div class='olabel'>UID</div><span><b>:</b></span> <div class='olabelc' style='font-weight: normal; font-size: inherit;'>" + appuid + "</div>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(1).html("<div class='olabel'>Name</div><span><b>:</b></span> <div class='olabelc' style='font-weight: normal; font-size: inherit;'>" + appname + "</div>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(2).html("<div class='olabel'>Type</div><span><b>:</b></span> <div class='olabelc' style='font-weight: normal; font-size: inherit;'>" + apptype + "</div>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(3).html("<div class='olabel'>Power Consumption</div><span><b>:</b></span> <div class='olabelc' style='font-weight: normal; font-size: inherit;'>" + consump_out +" </div>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(4).html("<div class='olabel'>Average Consumption</div><span><b>:</b></span> <div class='olabelc' style='font-weight: normal; font-size: inherit;'>" + avg_out + " / mo </div>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(5).html("<div class='olabel'>Rate / kWh</div><span><b>:</b></span> <div class='olabelc' style='font-weight: normal; font-size: inherit;'>₱ " + priceperkwhr.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+ "</div>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(6).html("<div class='olabel'>Estimated Cost</div><span><b>:</b></span> <div class='olabelc' style='font-weight: normal; font-size: inherit;'>₱ " + cost.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + " / mo</div>");
				$('.actualbody[name="' + appuid + '"]').find('.fullinfo').eq(7).html("<div class='olabel'>Limit</div><span><b>:</b></span> <div class='olabelc' style='font-weight: normal; font-size: inherit;'>" + limit_value + unit + "</span>");

				$('.appl_id[name="' + appuid + '"]').val(appuid);
				$('.appl_name[name="' + appuid + '"]').val(appname);
				$('.appl_consumption[name="' + appuid + '"]').val(consump);
				$('.appl_type[name="' + appuid + '"]').val(apptype);
				$('.appl_limit[name="' + appuid + '"]').val(original_limit_value);

				//chartlist[i].config.data.datasets[0].data[cur_month] = consump;
				//chartlist[i].update();
			}

			loadPrice();
		}
	});
}

function loadPrice(){
	$.ajax({
			type: "POST",
			data: "getPrice=1&ts="+$.now(),
			url: 'http://'+deviceHost+'/methods.php',
			crossDomain: true,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			success: function(data) {
				priceperkwhr = parseFloat(data.trim());
				checkapps();
			}
	});
}

function checkNoNotifs(){
	//cordova.plugins.notification.local.cancelAll(function() {console.log("done");	}, this);
	$.ajax({
		type: "POST",
		data: "ts="+$.now()+"&countAppNotifs=1",
		url: 'http://'+deviceHost+'/notifmethods.php',
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			numNotifs = data.trim();
			checkPlugged();
		}
	});
}
var jsonFormer = {};
function checkPlugged() {
	//$.getJSON('http://' + deviceHost + '/plugged.json', function (data) {
	$.getJSON('http://'+deviceHost+'/pathtoPluggedJSON.php', function (data) {
		//if((!isEqual(jsonFormer, data)) || (tries==1)){
		//	tries++;
			if(data.plugged!="0"){
				if (data.registered) {
					$("#noappnotice").hide();
					$('#unregisterednotice').hide();
					$(".appliance-info").each(function () {
						$(this).appendTo($("#registered-apps"));
						$(this).find('.switch').hide();
					});
					if ($("#plugged-apps").find($(".appliance-info[name='" + data.uid + "']")).length == 0) {
						$(".appliance-info[name='" + data.uid + "']").appendTo($("#plugged-apps"));
						$(".appliance-info[name='" + data.uid + "']").find('.switch').show();
					}
				} else {
					$(".appliance-info").each(function () {
						$(this).appendTo($("#registered-apps"));
						$(this).find('.switch').hide();
					});
					if(data.plugged=="1"){
						$('.additional-note').hide();
						$('.resolve-redirect').attr("href","notifications.html")
						if (numNotifs == "0"){
							$(".resolve-redirect").attr("disabled","");
							$(".resolve-redirect").text("IGNORED");
						} else {
							$(".resolve-redirect").removeAttr("disabled");
							$(".resolve-redirect").text("CHECK NOTIFICATION");
						}
					} else if(data.plugged=="4"){
						$('.additional-note').show();
						$(".resolve-redirect").removeAttr("disabled");
						$(".resolve-redirect").text("CHECK SETTINGS");
						$(".resolve-redirect").attr("href","settings.html");
					}
					var cardTitle = "";
					if(data.uid.match(/NO_UID/i)){
						cardTitle = "Anonymous_Appliance: ";
					} else {
						cardTitle = "Unregistered Appliance: ";
					}
					$('.CardMessage').text(cardTitle + data.uid);
					$("#noappnotice").hide();
					$('#unregisterednotice').show();
				}
			} else {
				$(".appliance-info").each(function () {
					$(this).appendTo($("#registered-apps"));
					$(this).find('.switch').hide();
				});
				$("#noappnotice").show();
				$('#unregisterednotice').hide();
			}
			jsonFormer = data;
	//	}
	});
}

function isEqual(jsonFormer,jsonToCompare){
	var obj1 = jsonFormer;
	var obj2 = jsonToCompare;

	var flag = true;

	if(Object.keys(obj1).length==Object.keys(obj2).length){
		for(key in obj1) { 
			if(obj1[key] == obj2[key]) {
				continue;
			}
			else {
				flag = false;
				break;
			}
		}
	} else {
		flag = false;
	}
	return flag;
}

checkNoNotifs();
checkapps();

setInterval(checkNoNotifs, 500);
setInterval(load_xml, 500);
