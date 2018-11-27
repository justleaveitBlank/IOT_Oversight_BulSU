var settings = {
	socket: "",
	limit: "",
	deviceauthentication: "",
	price: 0.0,
	admin: ""
}
var admin_code = "";
var class_value = "";
var admin_password_class = "";
var inputprice = 0.0;
var inputpass = "";

function load_xml() {
	$.ajax({
		url: "http://" + deviceHost + "/xml/overpass.xml",
		dataType: "xml",
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function (data) {
			xmlDoc = data;
			admin_code = xmlDoc.getElementsByTagName('admin')[0].firstChild.data;
			settings.admin = admin_code;
		}
	});
}

function initiate_settings() {
	load_xml();
	var label = $('#row_setting').attr('name');
	var logged_username = $('#username').val();
	settings.socket = $('#h_socket').val();
	settings.limit = $('#h_limit').val();
	settings.deviceauthentication = $('#h_authentication').val();
	settings.price = $('#h_price').val() * 1;
	$('#price_label').text(label);

	$('#s_socket').change(function () {
		settings.socket = (this.checked) ? 'true' : 'false';
		updateSettings();
	});

	$('#s_limit').change(function () {
		settings.limit = (this.checked) ? 'true' : 'false';
		updateSettings();
	});

	$('#s_authenticate').change(function () {
		$('#confirm_admin').attr('href','#');
		$('#confirm_admin').removeClass('triggerForPrice');
		var instance = M.Modal.getInstance($('#adminConfirmChangeRate'));
		instance.open();
	});
	
	$('#price-button-admin').focus(function(){
		$('#confirm_admin').attr('href','#ChangeMultiplyerRate');
		$('#confirm_admin').addClass('triggerForPrice');
	});

	$('#s_price').change(function () {
		inputprice = (this.value) * 1.0;
	});

	$('#btn_price').click(function () {
		if ($("#s_price").val().trim() != "") {
			settings.price = inputprice;
			$(this).addClass('modal-close');
			$("#s_price").val("");
			$("#price_label").removeClass('active');
			updateSettings();
		} else {
			$(this).removeClass('modal-close');
			inc_error("Price Value Required!");
		}
	});

	$('#new_password').keyup(function () {
		inputpass = $(this).val().trim();
	});

	$('#cancel').click(function () {
		admin_pass_reset();
	});

	$('.cancel-btns').click(function () {
		$('.p-fields').each(function () {
			$(this).val("");
		});

		$('.f-label').each(function () {
			$(this).removeClass("active");
		});

		settings.admin = admin_code;
		inputpass = "";
	});

	
//------------------------------------- PASSWORD -----------------------------------------------------------

	$('input').on('input',function(){
		$(this).removeClass('valid');
		$(this).removeClass('invalid');
	});
	
	$("#txtOldPassword").on('input',function(){
	if(this.value == admin_code){
	  $(this).addClass('valid');
	} else {
	  $(this).addClass('invalid');
	}
  });

  $("#txtNewPassword").on('input',function(){
	$("#txtConfirmPassword").removeClass('valid');
	if(this.value==$("#txtConfirmPassword").val()){
	  $("#txtConfirmPassword").addClass('valid');
	  inputpass = $(this).val();
	}
  });

  $("#txtConfirmPassword").on('input',function(){
	if(this.value==$("#txtNewPassword").val()){
	  $("#txtConfirmPassword").addClass('valid');
	  inputpass = $(this).val();
	} else {
	  $("#txtConfirmPassword").addClass('invalid');
	}
  });

  $(".save-password").focus(function(){
	if($("#txtOldPassword").hasClass("valid") &&  $("#txtNewPassword").hasClass("valid") && $("#txtConfirmPassword").hasClass("valid")){
	  $(this).addClass("modal-close");
	  settings.admin = inputpass;
	  updateSettings();
	} else {
	  inc_error("Invalid / Missing Field");
	  $(this).removeClass("modal-close");
	}
  });
  
	admin_password_class = $('#admin_password').attr('class');
	$('#validation_status').attr('data-error', "This Field is Required");
	$('#confirm_admin').focus(function () {
		if($(this).hasClass('triggerForPrice')){
			if ($('#admin_password').val().trim() == admin_code) {
				admin_pass_reset();
			} else {
				inc_error("Invalid Pass!");
			}
		} else {
			$('#confirm_admin').removeClass('modal-trigger');
			if ($('#admin_password').val().trim() == admin_code) {
				admin_pass_reset();
				if(settings.deviceauthentication.match(/false/i)){
					$('.myCheckbox').prop('checked', true);
					settings.deviceauthentication = 'true';
					updateSettings();
				} else if(settings.deviceauthentication.match(/true/i)){
					$('.myCheckbox').prop('checked', false);
					settings.deviceauthentication = 'false';
					updateSettings();
				}
			} else {
				inc_error("Invalid Pass!");
			}
		}
	});

	class_value = $('#confirm_admin').attr('class');
	$('#admin_password').keyup(function () {
		$('#validation_status').attr('data-error', "This Field is Required");
		$('#admin_password').removeClass("invalid");

		if ($('#admin_password').val() != "") {
			if ($('#admin_password').val() == admin_code) {
				$('#confirm_admin').addClass('modal-trigger modal-close');
			} else {
				$('#confirm_admin').removeClass('modal-trigger modal-close');
				$('#validation_status').attr('data-error', "Wrong Password");
			}
		}
	});
	
	function admin_pass_reset() {
		$('#passlabel').removeClass("active");
		$('#admin_password').val("");
		$('#admin_password').attr('class', admin_password_class);
		$('#admin_password').removeClass("invalid");
	}

	function checkSettings() {
		$.ajax({
			type: "POST",
			data: "getSettings=1",
			url: 'http://' + deviceHost + '/changeSettings.php',
			crossDomain: true,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			success: function (data) {
				var db_settings = JSON.parse(data.trim());

				if (db_settings[0].socket == "true") {
					$('#s_socket').prop("checked", true);
				} else {
					$('#s_socket').prop("checked", false);
				}

				if (db_settings[0].limitation == "true") {
					$('#s_limit').prop("checked", true);
				} else {
					$('#s_limit').prop("checked", false);
				}

				if (db_settings[0].authentication == "true") {
					$('#s_authenticate').prop("checked", true);
				} else {
					$('#s_authenticate').prop("checked", false);
				}

				$('#price_label').text("Price (₱ " + db_settings[0].price + ")");

				settings.socket = db_settings[0].socket;
				settings.limit = db_settings[0].limitation;
				settings.deviceauthentication = db_settings[0].authentication;
				settings.price = db_settings[0].price;
				settings.admin = db_settings[0].admin;
				admin_code = db_settings[0].admin;

			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				inc_error(errorThrown);
			}
		});
	}

	setInterval(checkSettings, 500);
}

function updateSettings() {
	var string = JSON.stringify(settings);
	var params = "settings=" + string;
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function () {
		if (xhr.readyState == 4 && xhr.status == 200) {
			console.log("Data Sent Successfully!");
			$('#confirm_admin').attr('class', class_value);
			$('#price_label').text("Price (₱ " + settings.price + ")");
			resetAllInputs();
			load_xml(); 
		}
	}
	xhr.open("POST", "http://" + deviceHost + "/changeSettings.php", true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.send(params);
	return false;
}

//-------------------------------------MINOR FUNCTIONS------------------------------------------------

	function inc_error(error) {
		$('.toast').hide();

		M.Toast.dismissAll();
		var toastHTML = "<span style='color: white; word-break: keep-all;  width: 70%; font-size:1em;'>"+error+"</span><button style='color: grey; width: 30%;' class='btn-flat toast-action'>Close</button>";
		M.toast({html: toastHTML});

		$('.toast-action').click(function(){
			 M.Toast.dismissAll();
		});
	}
	
	function resetAllInputs(){
		$('input').each(function(){
			$(this).val("");
			$(this).removeClass('valid');
			$(this).removeClass('invalid');
		});
		$('label').each(function(){
			$(this).removeClass('active');
		});
	}
//-------------------------------------------------------------------------------------------