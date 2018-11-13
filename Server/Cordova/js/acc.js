var username = window.localStorage.getItem('CurrentLogger');
var oldpassword = "",
    oldemail = "",
    oldcontact = "",
    fullname = "";

function GetUserData(){
  $.ajax({
      type: "POST",
      data: "getUserInfo="+username,
      url: 'http://'+deviceHost+'/methods.php',
      crossDomain: true,
      contentType: "application/x-www-form-urlencoded; charset=utf-8",
      success: function(data) {
        var userData = JSON.parse(data.trim());

        oldpassword = userData[0].password;
        oldemail = userData[0].email;
        oldcontact = userData[0].contact;
        var firstname = CapitalizeFirst(userData[0].firstname);
        var lastname = CapitalizeFirst(userData[0].lastname);
        fullname = firstname + " " + lastname;

        $("#user_name").val(username);
        $("#fullname").val(fullname);
        $("#password").val(oldpassword);
        $("#email").val(oldemail);
        $("#contactNumber").val(oldcontact);
      }
  });
}



function CapitalizeFirst(rawName){
  var nameArray = rawName.split(" ");
  var finalString = "";
  for (var i = 0; i < nameArray.length; i++) {
    if(i>0){
      finalString += " ";
    }
    string = nameArray[i];
    finalString += string.substring(0,1).toUpperCase() + string.substring(1,string.length).toLowerCase();
  }
  return finalString;
}

$(document).ready(function(){
  GetUserData();

  $(".input-forms").submit(function(e){
    e.preventDefault();
    $(this).find(".svbtn").trigger("focus");
  });

  $("input[type='password']").on('input',function(){
    $(this).removeClass('invalid');
    $(this).removeClass('valid');
  });

  //----------------------------------PASSWORD----------------------------------

  $("#txtOldPassword").on('input',function(){
    if(this.value == oldpassword){
      $(this).addClass('valid');
    } else {
      $(this).addClass('invalid');
    }
  });

  $("#txtNewPassword").on('input',function(){
    $("#txtConfirmPassword").removeClass('valid');
    if(this.value==$("#txtConfirmPassword").val()){
      $("#txtConfirmPassword").addClass('valid');
    }
  });

  $("#txtConfirmPassword").on('input',function(){
    if(this.value==$("#txtNewPassword").val()){
      $("#txtConfirmPassword").addClass('valid');
    } else {
      $("#txtConfirmPassword").addClass('invalid');
    }
  });

  $(".save-password").focus(function(){
    if($("#txtOldPassword").hasClass("valid") &&  $("#txtNewPassword").hasClass("valid") && $("#txtConfirmPassword").hasClass("valid")){
      $(this).addClass("modal-close");
      saveAccountChanges("password",$("#txtNewPassword").val());
    } else {
      SendToast("Invalid / Missing Field");
      $(this).removeClass("modal-close");
    }
  });

  //-----------------------------------EMAIL------------------------------------

  $("#txtUpdateEmail").on('input',function(){
    var re = /([A-Z0-9a-z_-][^@])+?@[^$#<>?]+?\.[\w]{2,4}/.test(this.value);
    if(!re) {
      $(".save-email").removeClass("modal-close");
      $("#txtUpdateEmail").removeClass("valid");
      $("#txtUpdateEmail").addClass("invalid");
    } else {
      checkEmail($(this).val());
    }

  });

  $(".save-email").focus(function(){
    if($("#txtUpdateEmail").hasClass("valid")){
      saveAccountChanges("email",$("#txtUpdateEmail").val());
    } else {
      $(this).removeClass("modal-close");
      SendToast("Invalid / Missing Field");
    }
  });

  //-----------------------------------PHONE------------------------------------

  $(".save-contact").mousedown(function(){
    if($("#txtUpdateContact").hasClass("valid")){
      $(this).addClass("modal-close");
      saveAccountChanges("contact",$("#txtUpdateContact").val());
    } else {
      $(this).removeClass("modal-close");
      SendToast("Invalid / Missing Field");
    }
  });

  //----------------------------------------------------------------------------

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

  function checkEmail(email){
    $.ajax({
        type: "POST",
        data: "validate=email&value="+email,
        url: 'http://'+deviceHost+'/methods.php',
        crossDomain: true,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        success: function(data) {
          if(!data.trim().match(/invalid/i)){
            $(".save-email").addClass("modal-close");
            $("#txtUpdateEmail").removeClass("invalid");
            $("#txtUpdateEmail").addClass("valid");
          } else {
            $(".save-email").removeClass("modal-close");
            $("#txtUpdateEmail").removeClass("valid");
            $("#txtUpdateEmail").addClass("invalid");
          }
        }
    });
  }

  function saveAccountChanges(column,value){
    var xmlParameter = {
      username : username,
      column : column,
      value : value
    };
    $.ajax({
        type: "POST",
        data: "ts="+$.now()+"&updateAccount="+JSON.stringify(xmlParameter),
        url: 'http://'+deviceHost+'/methods.php',
        crossDomain: true,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        success: function(data) {
          if(data.trim().match(/Success/i) && column.match(/password/i)){
            alert("Re-Log Needed!");
            UserLogOut();
          } else {
            SendToast("Status: " + data.trim());
            GetUserData();
          }
        }
    });
  }
});
