var selecteddate = "now";
var uid = "6f63b28";
var options  = ["getdaily" , "getweekly" , "getmonthly" , "getyearly"];
var d = new Date();
var dayOfWeek = d.getDay()+1;

function getAppliances(type){
  $.ajax({
      type: "POST",
      data: "getconsumers="+selecteddate+"&type="+type,
      url: 'http://'+deviceHost+'/Stats.php',
      crossDomain: true,
      contentType: "application/x-www-form-urlencoded; charset=utf-8",
      success: function(data) {
        console.log(data.trim());
        var appliances = JSON.parse(data.trim());
        console.log(appliances);
      }
  });
}
getAppliances("MONTH");

$(function() {
  var instance = M.Datepicker.getInstance(document.getElementById("DateReference"));
  instance.options.autoClose= true;

  $('#DateReference').change(function(){
    var dateObj = new Date($(this).val());
    var month = dateObj.getMonth() + 1; //months from 1-12
    var day = dateObj.getDate();
    var year = dateObj.getFullYear();

    var newdate = year + "-" + month + "-" + day;
    selecteddate = newdate;
    dayOfWeek = dateObj.getDay() + 1;
    reloadCharts();
  });

  function getchartdata(type){
    $.ajax({
      type: "POST",
      data: type + "=" + selecteddate + "&ts=" + $.now() + "&uid=" + uid + "&Day=" + dayOfWeek,
      url: 'http://' + deviceHost + '/stats.php',
      crossDomain: true,
      contentType: "application/x-www-form-urlencoded; charset=utf-8",
      success: function (data) {
        console.log(data.trim());
        var result = data.trim().split("|");
        var labelset = JSON.parse(result[0].trim());
        var chartset = JSON.parse(result[1].trim());
        if(type==options[0]){
          dailychart.config.data.datasets[0].data = chartset;
          dailychart.config.data.labels = labelset;
  				dailychart.update();
        } else if(type==options[1]){
          weekchart.config.data.datasets[0].data = chartset;
          weekchart.config.data.labels = labelset;
  				weekchart.update();
        } else if(type==options[2]){
          monthchart.config.data.datasets[0].data = chartset;
          monthchart.config.data.labels = labelset;
  				monthchart.update();
        } else if(type==options[3]){
          yearchart.config.data.datasets[0].data = chartset;
          yearchart.config.data.labels = labelset;
  			  yearchart.update();
        }
      }
    });
  }

  function reloadCharts(){
    for (var i = 0; i < 4; i++) {
      getchartdata(options[i]);
    }
  }

  $('#DateReference').change(function(){
    var instance = M.Datepicker.getInstance($(this));
  });

  reloadCharts();


});
