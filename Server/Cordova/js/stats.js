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

var Colors = ["#460082","#f44336","#e91e63","#9c27b0","#673ab7","#3f51b5","#2196f3","#03a9f4","#00bcd4","#009688","#4caf50","#8bc34a","cddc39","#fdd835","#ffb300","#fb8c00","#ff5722","#795548","#757575","#546e7a","#263238","#212121","#3e2723","#dd2c00","#ff6d00","#ff6f00","#f57f17","#827717","#33691e","#1b5e20","#004d40","#006064","#01579b","#0d47a1","#1a237e","#311b92","#4a148c","880e4f","#b71c1c","#000000"];



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
  // --------------------- IF NO DATA AVAILABLE -------------------------
	Chart.plugins.register({
		afterDraw: function(chart) {
		if (chart.data.datasets.length === 0) {
			// No data is present
		  var ctx = chart.chart.ctx;
		  var width = chart.chart.width;
		  var height = chart.chart.height
		  chart.clear();
		  
		  ctx.save();
		  ctx.textAlign = 'center';
		  ctx.textBaseline = 'middle';
		  ctx.font = "16px normal 'Helvetica Nueue'";
		  ctx.fillText('No data to display', width / 2, height / 2);
		  ctx.restore();
		}
	  }
	});
	
//------------------------GET CHART DATA-------------------------

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
        var overallCharset = JSON.parse(result[1].trim());
		var singleAppChartset = JSON.parse(result[2].trim());
		var singleAppName = JSON.parse(result[3].trim());
        var colCount = 0;
		var chartset = {
				"label":"OVERALL",
				"data": overallCharset,
				"fill":false,
				"borderColor": Colors[colCount],
				"lineTension":0
		};
		
        if(type==options[0]){
			weekchart.destroy();
			weekchart = resetChartData("weeklyGraph");
			weekchart.config.data.labels = labelset;
			weekchart.config.data.datasets.push(chartset);
			weekchart.update();
			for(var count = 0; count<singleAppChartset.length; count++){
				for(var curUid in singleAppChartset[count]){
					var chartset = {
						"label": singleAppName[count],
						"data": singleAppChartset[count][curUid],
						"fill": false,
						"borderColor": Colors[++colCount],
						"lineTension":0
					};
					weekchart.config.data.datasets.push(chartset);
					weekchart.update();
				}
			}
        } else if(type==options[1]){
			monthchart.destroy();
			monthchart = resetChartData("monthlyGraph");
			monthchart.config.data.labels = labelset;
			monthchart.config.data.datasets.push(chartset);
			monthchart.update();
			for(var count = 0; count<singleAppChartset.length; count++){
				for(var curUid in singleAppChartset[count]){
					var chartset = {
						"label": singleAppName[count],
						"data": singleAppChartset[count][curUid],
						"fill": false,
						"borderColor": Colors[++colCount],
						"lineTension":0
					};
					monthchart.config.data.datasets.push(chartset);
					monthchart.update();
				}
			}
        } else if(type==options[2]){
			yearchart.destroy();
			yearchart = resetChartData("yearlyGraph");
			yearchart.config.data.datasets.push(chartset);
			yearchart.config.data.labels = labelset;
			yearchart.update();
			for(var count = 0; count<singleAppChartset.length; count++){
				for(var curUid in singleAppChartset[count]){
					var chartset = {
						"label": singleAppName[count],
						"data": singleAppChartset[count][curUid],
						"fill": false,
						"borderColor": Colors[++colCount],
						"lineTension":0
					};
					yearchart.config.data.datasets.push(chartset);
					yearchart.update();
				}
			}
        }

        getSummary(singleAppChartset,overallCharset,type);
      }
    });
  }
  
  // -------------------------- CLEAR CHART -------------------------------------------
	function resetChartData(canvasHolder){
		var chartHolder = new Chart(document.getElementById(canvasHolder),{
			"type":"line",
			"data":{
				"labels":[],
				"datasets":[]
			},
			"options":{
				tooltips: {
					enabled: true,
					mode: 'single',
					callbacks: {
						label: function(tooltipItems, data) { 
							return tooltipItems.yLabel + ' kWh';
						}
					}
				},
				animation: {
					duration: 1000, // general animation time
				},
				hover: {
					animationDuration: 1000, // duration of animations when hovering an item
				},
				responsiveAnimationDuration: 1000, // animation duration after a resize
			}

		});
		return chartHolder;
	}
	
//-----------------------------------OVERALL AND SINGLE SUMMARY----------------------------------------------------

  function getSummary(singleAppChartset,overallCharset,type){
    $.ajax({
        type: "POST",
        data: "getPrice=1",
        url: 'http://'+deviceHost+'/methods.php',
        crossDomain: true,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        success: function(data) {
			
		//---------------------OVERALL SUMMARY--------------------------
            var current_price = parseFloat(data.trim());
			var weekly_Sum_out;
			var weekly_Avg_out;
			var mothly_Sum_out;
			var monthly_Avg_out;
			var yearly_Sum_out;
			var yearly_Avg_out;
			if(type==options[0]){
			  
			weekly_sum=0.0;
            for (var l = 0; l < overallCharset.length; l++) {
              weekly_sum+=parseFloat(overallCharset[l]);
            }
			
			//console.log("weekly_sum = "+weekly_sum/1000);
			if((weekly_sum/1000).toFixed(2) == 0.00){
				weekly_Sum_out = weekly_sum.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" Wh";
			}
			else{
				weekly_Sum_out = (weekly_sum/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
			}
			 
			weekly_avg=weekly_sum/overallCharset.length;
			if((weekly_avg/1000).toFixed(2) == 0.00){
				weekly_Avg_out = weekly_avg.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" Wh";
			}
			else{;
				weekly_Avg_out = (weekly_avg/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
			}
           
            weekly_price=(weekly_sum/1000)*parseFloat(data.trim());
            $('#weekly_avg').text(weekly_Avg_out);
            $('#weekly_sum').text(weekly_Sum_out);
            $('#weekly_price').text("₱ "+weekly_price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
			
          } else if(type==options[1]){
			
            monthly_sum=0.0;
            for (var l = 0; l < overallCharset.length; l++) {
              monthly_sum+=parseFloat(overallCharset[l]);
            }
			
			//console.log("monthly_sum = "+monthly_sum/1000);
			if((monthly_sum/1000).toFixed(2) == 0.00){
				monthly_Sum_out = monthly_sum.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" Wh";
			}
			else{
				monthly_Sum_out = (monthly_sum/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
			}
			
            monthly_avg=monthly_sum/overallCharset.length;
			if((monthly_avg/1000).toFixed(2) == 0.00){
				monthly_Avg_out = monthly_avg.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" Wh";
			}
			else{;
				monthly_Avg_out = (monthly_avg/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
			}
			
            monthly_price=(monthly_sum/1000)*parseFloat(data.trim());
            $('#monthly_avg').text(monthly_Avg_out);
            $('#monthly_sum').text(monthly_Sum_out);
            $('#monthly_price').text("₱ "+monthly_price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
			
          } else if(type==options[2]){
			
            yearly_sum=0.0;
            for (var l = 0; l < overallCharset.length; l++) {
              yearly_sum+=parseFloat(overallCharset[l])/1000;
            }
			
			//console.log("yearly_sum = "+yearly_sum/1000);
			if((yearly_sum/1000).toFixed(2) == 0.00){
				yearly_Sum_out = yearly_sum.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" Wh";
			}
			else{
				yearly_Sum_out = (yearly_sum/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
			}
			
            yearly_avg=yearly_sum/overallCharset.length;
			if((yearly_avg/1000).toFixed(2) == 0.00){
				yearly_Avg_out = yearly_avg.toFixed(2)+" Wh";
			}
			else{;
				yearly_Avg_out = (yearly_avg/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
			}
			
            yearly_price=(yearly_sum/1000)*parseFloat(data.trim());
			
            $('#yearly_avg').text(yearly_Avg_out);
            $('#yearly_sum').text(yearly_Sum_out);
            $('#yearly_price').text("₱ "+yearly_price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
			
          }
		 //-----------------SINGLE APP SUMMARY----------------------
		  getSingleAppSummary(singleAppChartset,type,current_price)
        }
    });
  }
  
  //---------------------------------SINGLE APP (EXTENSION) SUMMARY--------------------------------------
  
  function getSingleAppSummary(singleAppChartset,type,current_price){
	
	for(var c = 0; c<singleAppChartset.length; c++){
		for(var curUid in singleAppChartset[c]){
			var currentUid = curUid;
			var currentChartset = singleAppChartset[c][curUid];
			var colCode = Colors[c+1];
			$.ajax({
				type: "POST",
				data: "ts="+$.now()+"&color="+colCode+"&applianceSummary="+currentUid+"&price="+current_price+"&chartSet="+JSON.stringify(currentChartset),
				url: 'http://'+deviceHost+'/Stats.php',
				crossDomain: true,
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				success: function(data) {
				  if(type==options[0]){
					$('.WeeklyAppliances').append(data);
					
				  } else if(type==options[1]){
					$('.MonthlyAppliances').append(data);
					
					
				  } else if(type==options[2]){
					$('.YearlyAppliances').append(data);
					
				  }
				}
			});
		}
	}
  }
  
  
  //---------------------------------LOAD AND RELOAD CHART--------------------------------------

  function reloadCharts(){
    for (var i = 0; i < 3; i++) {
      getchartdata(options[i]);
    }
	$('.WeeklyAppliances').html("");
	$('.MonthlyAppliances').html("");
	$('.YearlyAppliances').html("");
  }

  $('#DateReference').change(function(){
    var instance = M.Datepicker.getInstance($(this));
  });

  reloadCharts();
});
