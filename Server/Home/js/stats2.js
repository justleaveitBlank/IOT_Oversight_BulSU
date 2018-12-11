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

  
var $Colors = {};
$Colors.names = {
	aqua: "#00ffff",
	azure: "#f0ffff",
	beige: "#f5f5dc",
	black: "#000000",
	blue: "#0000ff",
	brown: "#a52a2a",
	cyan: "#00ffff",
	darkblue: "#00008b",
	darkcyan: "#008b8b",
	darkgrey: "#a9a9a9",
	darkgreen: "#006400",
	darkkhaki: "#bdb76b",
	darkmagenta: "#8b008b",
	darkolivegreen: "#556b2f",
	darkorange: "#ff8c00",
	darkorchid: "#9932cc",
	darkred: "#8b0000",
	darksalmon: "#e9967a",
	darkviolet: "#9400d3",
	fuchsia: "#ff00ff",
	gold: "#ffd700",
	green: "#008000",
	indigo: "#4b0082",
	khaki: "#f0e68c",
	lightblue: "#add8e6",
	lightcyan: "#e0ffff",
	lightgreen: "#90ee90",
	lightgrey: "#d3d3d3",
	lightpink: "#ffb6c1",
	lightyellow: "#ffffe0",
	lime: "#00ff00",
	magenta: "#ff00ff",
	maroon: "#800000",
	navy: "#000080",
	olive: "#808000",
	orange: "#ffa500",
	pink: "#ffc0cb",
	purple: "#800080",
	red: "#ff0000",
	silver: "#c0c0c0",
	white: "#ffffff",
	yellow: "#ffff00"
};
var ArrayBody = "[";
for(var colName in $Colors.names){
	var colCode = $Colors.names[colName];
	ArrayBody += '"'+colCode+'",';
}
ArrayBody = ArrayBody.substring(0,(ArrayBody.length-1))+"]";

	console.log(ArrayBody);
  
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
			chartset = {
				"label":"1",
				"data":[0,1,3,2,0,1,7],
				"fill":false,"borderColor": Color[0],
				"lineTension":0.25
			};			
			weekchart.config.data.datasets.push(chartset);
			chartset = {
				"label":"2",
				"data":[9,2,3,4,1,2,3],
				"fill":false,"borderColor": Color[1],
				"lineTension":0.25
			};	
			weekchart.config.data.datasets.push(chartset);
			weekchart.config.data.labels = labelset;
			weekchart.update();
			console.log(weekchart);
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
