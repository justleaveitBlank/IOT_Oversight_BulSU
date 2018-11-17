var selecteddate = "now";
var uid = "6f63b28";
var options  = ["getweekly" , "getmonthly" , "getyearly"];
var d = new Date();
var weekly_avg = 0.0;
var weekly_sum = 0.0;
var weekly_price = 0.0;
var monthly_avg = 0.0;
var monthly_sum = 0.0;
var monthly_price = 0.0;
var yearly_avg = 0.0;
var yearly_sum = 0.0;
var yearly_price = 0.0;
var dayOfWeek = d.getDay()+1;

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
          weekchart.config.data.datasets[0].data = chartset;
          weekchart.config.data.labels = labelset;
  				weekchart.update();
        } else if(type==options[1]){
          monthchart.config.data.datasets[0].data = chartset;
          monthchart.config.data.labels = labelset;
  				monthchart.update();
        } else if(type==options[2]){
          yearchart.config.data.datasets[0].data = chartset;
          yearchart.config.data.labels = labelset;
  			  yearchart.update();
        }

        getSummary(chartset,type);
      }
    });
  }

  function getSummary(chartset,type){
    $.ajax({
        type: "POST",
        data: "getPrice",
        url: 'http://'+deviceHost+'/methods.php',
        crossDomain: true,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        success: function(data) {
          if(type==options[0]){
            weekly_sum=0.0;
            for (var l = 0; l < chartset.length; l++) {
              weekly_sum+=parseFloat(chartset[l])/1000;
            }
            weekly_avg=weekly_sum/chartset.length;
            weekly_price=weekly_sum*parseFloat(data.trim());
            $('#weekly_avg').text(weekly_avg+" kwhr");
            $('#weekly_sum').text(weekly_sum+" kwhr");
            $('#weekly_price').text("₱ "+weekly_price);
          } else if(type==options[1]){
            monthly_sum=0.0;
            for (var l = 0; l < chartset.length; l++) {
              monthly_sum+=parseFloat(chartset[l])/1000;
            }
            monthly_avg=monthly_sum/chartset.length;
            monthly_price=monthly_sum*parseFloat(data.trim());
            $('#monthly_avg').text(monthly_avg+" kwhr");
            $('#monthly_sum').text(monthly_sum+" kwhr");
            $('#monthly_price').text("₱ "+monthly_price);
          } else if(type==options[2]){
            yearly_sum=0.0;
            for (var l = 0; l < chartset.length; l++) {
              yearly_sum+=parseFloat(chartset[l])/1000;
            }
            yearly_avg=yearly_sum/chartset.length;
            yearly_price=yearly_sum*parseFloat(data.trim());
            $('#yearly_avg').text(yearly_avg+" kwhr");
            $('#yearly_sum').text(yearly_sum+" kwhr");
            $('#yearly_price').text("₱ "+yearly_price);
          }
        }
    });
  }

  function reloadCharts(){
    for (var i = 0; i < 3; i++) {
      getchartdata(options[i]);
    }
  }

  $('#DateReference').change(function(){
    var instance = M.Datepicker.getInstance($(this));
  });

  reloadCharts();
});
