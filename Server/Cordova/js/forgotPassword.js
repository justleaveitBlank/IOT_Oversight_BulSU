$("#email").on('input',function(){
  var re = /([A-Z0-9a-z_-][^@])+?@[^$#<>?]+?\.[\w]{2,4}/.test(this.value);
  if(!re) {
    $(".forgot-btn").removeClass("modal-close");
    $("#email").removeClass("valid");
    $("#email").addClass("invalid");
  } else {
    checkEmail(this.value);
  }
});

$(".forgot-btn").focus(function(){
  if($("#email").hasClass("valid")){
    sendInformation();
  } else {
    $(this).removeClass("modal-close");
    SendToast("Invalid / Missing Field");
  }
});

function SendToast(message){
  $('.toast').hide();

  var toastHTML = "<span style='color: white; word-break: keep-all;  width: 70%; font-size: 1em;'>" +message+ "</span><button style='color:gray; margin-left:.5rem; width: 30%;' class='btn-flat toast-action'>&#10006;</button>";
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
        console.log(data.trim());
        if(data.trim().match(/invalid/i)){
          $(".forgot-btn").addClass("modal-close");
          $("#email").removeClass("invalid");
          $("#email").addClass("valid");
        } else {
          $(".forgot-btn").removeClass("modal-close");
          $("#email").removeClass("valid");
          $("#email").addClass("invalid");
        }
      }
  });
}

function sendInformation(){
  if($('#email').hasClass("valid")){
    $.ajax({
        type: "POST",
        data: "forgotPassword="+$('#email').val()+"&ts="+$.now(),
        url: 'http://'+deviceHost+'/methods.php',
        crossDomain: true,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        success: function(data) {
          SendToast(data.trim());
        }
    });
  }
}
