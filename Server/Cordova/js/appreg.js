var appl = {
	id: '',
	name: '',
	type: '',
	limit: 0.0,
	notif : 0,
	valid: 'no'
}
var id = "";
var name = "";
var type = "";
var limit = 'unlimited';
var name_validity = "";
var regclass = $('#regsub').attr('class');
var nameclass = $('#d_name').attr('class');
var limitclass = $('#d_r_limit').attr('class');

$('#d_name').bind('keyup', function() {
	validate_fields();
});

$("#d_r_limit").bind('keyup mouseup', function() {
	reloadvalues();
});

$("#chk_limit").click(function() {
	if (this.checked) {
		limit = "hasnolimit";
		$('#d_r_limit').removeAttr('disabled');
		$('#d_r_limit').attr('class',limitclass);
	} else {
		limit = "unlimited";
		$("#d_r_limit").prop('disabled', true);
		$('#d_r_limit').attr('class',limitclass);
	}
	reloadvalues();
});

$("#regsub").mousedown(function() {
	reloadvalues();
	validate_fields();
});

$("#regsub").click(function() {
	register_app();
});

function reloadvalues() {
	id = $('#d_id').val().trim();
	var array_type = [
		"",
		"General Appliances",
		"Aircon",
		"Refrigerator",
		"Electric Stove",
		"Fan",
		"Television"
	];
	var selected_type = array_type[parseInt($('#applianceType').find(":selected").attr('value'))];
	type = selected_type;
	name = $('#d_name').val().trim();

	if (limit == 'hasnolimit' && $('#d_r_limit').val().trim() == "") {
		limit = 'hasnolimit';
	} else if (limit == 'hasnolimit' && $('#d_r_limit').val().trim() != "") {
		limit = 'haslimit';
	} else if ($('#chk_limit').checked === false) {
		limit = 'unlimited';
	}
//-------------------------------------VALUES ASSIGN----------------------------------------------------

	appl.id = id;
	appl.name = name;
	appl.limit = (limit == 'unlimited') ? 0.0 : parseFloat($('#d_r_limit').val().trim());
	appl.notif = notif_id;
	appl.type = type;

//------------------------------------------------------------------------------------------------------

	if ((id == '') || (name == '') || (limit == 'hasnolimit') || $('#d_name').hasClass('invalid') || !($('#d_name').hasClass('valid')) || (type=="")) {
		appl.valid = 'no';
		$('#regsub').attr('class', regclass);
	} else {
		appl.valid = 'yes';
		$('#regsub').attr('class', 'modal-close ' + regclass);
	}
}

function validate_fields() {
	name_validity = "";
	if ($('#d_name').val().trim() != "") {
		$.ajax({
			type: "POST",
			data: "valappreg=" + JSON.stringify(appl),
			url: 'http://'+deviceHost+'/regappmethods.php',
			crossDomain: true,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			success: function(data) {
				console.log(data.trim());
				if (data.trim() == 'Invalid') {
					name_validity = "invalid";
				} else if (data.trim() == "Valid") {
					name_validity = "valid";
				}
				show_validity();
			}
		});
	}
}

function show_validity() {
	if (name_validity == "invalid") {
		$('#d_name').attr('class', 'invalid ' + nameclass);
	} else if (name_validity == "valid") {
		$('#d_name').attr('class', 'valid ' + nameclass);
	} else {
		$('#d_name').attr('class', nameclass);
	}
}

function register_app() {
	if(appl.valid == 'yes'){
		$.ajax({
			type: "POST",
			data: "reg=" + JSON.stringify(appl),
			url: 'http://'+deviceHost+'/regappmethods.php',
			crossDomain: true,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			success: function(data) {
				console.log();
				if(data.trim().match(/success/i)){
					SendToast("Appliance registered!");
					throwOnResolved(id,"registered");
				}
			}
		});
	} else {
		SendToast("Invalid / Missing Inputs");
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
