// THIS SECTION IS DIVIDED IN DIFFERENT SECTIONS, TO QUICKLY NAVIGATE SEARCH THE FOLLOWING KEYWORDS:
// FOR THE INITIAL DECLARATIONS AND INITIALIZATION : DECLARATIONS AND INITIALIZATION
// FOR THE GRAPHS : GRAPHICAL REPRESSENTATION SECTION
// FOR THE COMPUTATION AND APPLIANCE : STATISTICAL COMPUTATIONS SECTION
// FOR THE ADDITIONAL FUNCTIONS : ADDITIONAL IMPORTANT FUNCTIONS 
// FOR THE INITIATION : INITIATION

// ================================ DECLARATIONS AND INITIALIZATION ================================================
var current_price = 0.00;
var Colors = ["#460082","#f44336","#e91e63","#9c27b0","#673ab7","#3f51b5","#2196f3","#03a9f4","#00bcd4","#009688","#4caf50","#8bc34a","cddc39","#fdd835","#ffb300","#fb8c00","#ff5722","#795548","#757575","#546e7a","#263238","#212121","#3e2723","#dd2c00","#ff6d00","#ff6f00","#f57f17","#827717","#33691e","#1b5e20","#004d40","#006064","#01579b","#0d47a1","#1a237e","#311b92","#4a148c","880e4f","#b71c1c","#000000"];
var sort = "consumed DESC";
var options  = ["getweekly" , "getmonthly" , "getyearly"];
var selecteddate = "now";
var searchedWeekKey = "";
var searchedMonthKey = "";
var searchedYearKey = "";
var d = new Date();
var dayOfWeek = d.getDay()+1;
var weeklyID = [];
var monthlyID = [];
var yearlyID = [];
var yearOpt = [];
var monthOpt = [];
var weekOpt = [];

// ================================ STATISTICAL COMPUTATIONS SECTION ================================================

// ======================================= WEEKLY STATISTICS  ================================================

$('.weekSortOption').click(function(){
	sort = $(this).attr('name');
	console.log(sort);
	reloadWeekly();
	$('.weekly-sorter-trigger').html("<i class='material-icons' style='vertical-align: bottom;'>sort</i>Sort By : "+$(this).find('a').attr("name"));
});

function getWeeklyComputation(){
	$('.WeeklyAppliances').html("");
	$.ajax({
		type: "POST",
		data: "searched="+searchedWeekKey+"&sort="+sort+"&getComputeWeekly="+selecteddate+"&ts="+$.now(),
		url: 'http://'+deviceHost+'/stats.php',
		crossDomain: true,
		async : false,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			var singleAppChartset = JSON.parse(data.trim());
			
			for(var xcount = 0; xcount<singleAppChartset.length; xcount++){
				for(var curUid in singleAppChartset[xcount]){
					for(var ycount in singleAppChartset[xcount][curUid]){
						weeklyID[xcount] = curUid;
						produceApplianceCard(curUid,singleAppChartset[xcount][curUid][ycount].consumption,xcount,1,7);
						
						//for autocomplete textinput
						if(singleAppChartset[xcount][curUid][ycount].name.match(/Anonymous/i)){
							if(!weekOpt.includes("Anonymous")){
								weekOpt.push("Anonymous");
							}
						} else if(singleAppChartset[xcount][curUid][ycount].name.match(/Unregistered/i)){
							if(!weekOpt.includes("Unregistered")){
								weekOpt.push("Unregistered");
							}
						} else {
							if(!weekOpt.includes(singleAppChartset[xcount][curUid][ycount].name)){
								weekOpt.push(singleAppChartset[xcount][curUid][ycount].name);
							}
						}
						if(!weekOpt.includes(singleAppChartset[xcount][curUid][ycount].type)){
							weekOpt.push(singleAppChartset[xcount][curUid][ycount].type);
						}
						if(!weekOpt.includes(curUid)){
							weekOpt.push(curUid);
						}
						
					}
				}
			}
			
			var updatedData = [];
			for(var xkey in weekOpt){
				updatedData[weekOpt[xkey]] = null;
			}
			var instanceMonth = M.Autocomplete.getInstance($(".autoWeek"));
			instanceMonth.options.data = updatedData;
		}
	});
}

// ======================================= MONTHLY STATISTICS  ================================================

$('.monthSortOption').click(function(){
	sort = $(this).attr('name');
	console.log(sort);
	reloadMonthly();
	$('.monthly-sorter-trigger').html("<i class='material-icons' style='vertical-align: bottom;'>sort</i>Sort By : "+$(this).find('a').attr("name"));
});

function getMonthlyComputation(){
	$('.MonthlyAppliances').html("");
	$.ajax({
		type: "POST",
		data: "searched="+searchedMonthKey+"&sort="+sort+"&getComputeMonthly="+selecteddate+"&ts="+$.now(),
		url: 'http://'+deviceHost+'/stats.php',
		crossDomain: true,
		async : false,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			var singleAppChartset = JSON.parse(data.trim());
			
			for(var xcount = 0; xcount<singleAppChartset.length; xcount++){
				for(var curUid in singleAppChartset[xcount]){
					for(var ycount in singleAppChartset[xcount][curUid]){
						monthlyID[xcount] = curUid;
						produceApplianceCard(curUid,singleAppChartset[xcount][curUid][ycount].consumption,xcount,2,30);
						
						//for autocomplete textinput
						if(singleAppChartset[xcount][curUid][ycount].name.match(/Anonymous/i)){
							if(!monthOpt.includes("Anonymous")){
								monthOpt.push("Anonymous");
							}
						} else if(singleAppChartset[xcount][curUid][ycount].name.match(/Unregistered/i)){
							if(!monthOpt.includes("Unregistered")){
								monthOpt.push("Unregistered");
							}
						} else {
							if(!monthOpt.includes(singleAppChartset[xcount][curUid][ycount].name)){
								monthOpt.push(singleAppChartset[xcount][curUid][ycount].name);
							}
						}
						if(!monthOpt.includes(singleAppChartset[xcount][curUid][ycount].type)){
							monthOpt.push(singleAppChartset[xcount][curUid][ycount].type);
						}
						if(!monthOpt.includes(curUid)){
							monthOpt.push(curUid);
						}
					}
				}
			}
			var updatedData = [];
			for(var xkey in monthOpt){
				updatedData[monthOpt[xkey]] = null;
			}
			var instanceMonth = M.Autocomplete.getInstance($(".autoMonth"));
			instanceMonth.options.data = updatedData;
			
		}
	});
}

// ======================================= YEARLY STATISTICS  ================================================

$('.yearSortOption').click(function(){
	sort = $(this).attr('name');
	console.log(sort);
	reloadYearly();
	$('.yearly-sorter-trigger').html("<i class='material-icons' style='vertical-align: bottom;'>sort</i>Sort By : "+$(this).find('a').attr("name"));
});

function getYearlyComputation(){
	$('.YearlyAppliances').html("");
	$.ajax({
		type: "POST",
		data: "searched="+searchedYearKey+"&sort="+sort+"&getComputeYearly="+selecteddate+"&ts="+$.now(),
		url: 'http://'+deviceHost+'/stats.php',
		crossDomain: true,
		async : false,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			var singleAppChartset = JSON.parse(data.trim());
			
			for(var xcount = 0; xcount<singleAppChartset.length; xcount++){
				for(var curUid in singleAppChartset[xcount]){
					for(var ycount in singleAppChartset[xcount][curUid]){
						yearlyID[xcount] = curUid;
						produceApplianceCard(curUid,singleAppChartset[xcount][curUid][ycount].consumption,xcount,3,12);
						
						//for autocomplete textinput
						if(singleAppChartset[xcount][curUid][ycount].name.match(/Anonymous/i)){
							if(!yearOpt.includes("Anonymous")){
								yearOpt.push("Anonymous");
							}
						} else if(singleAppChartset[xcount][curUid][ycount].name.match(/Unregistered/i)){
							if(!yearOpt.includes("Unregistered")){
								yearOpt.push("Unregistered");
							}
						} else {
							if(!yearOpt.includes(singleAppChartset[xcount][curUid][ycount].name)){
								yearOpt.push(singleAppChartset[xcount][curUid][ycount].name);
							}
						}
						if(!yearOpt.includes(singleAppChartset[xcount][curUid][ycount].type)){
							yearOpt.push(singleAppChartset[xcount][curUid][ycount].type);
						}
						if(!yearOpt.includes(curUid)){
							yearOpt.push(curUid);
						}
					}
				}
			}
			
			var updatedData = [];
			for(var xkey in yearOpt){
				updatedData[yearOpt[xkey]] = null;
			}
			var instanceYear = M.Autocomplete.getInstance($(".autoYear"));
			instanceYear.options.data = updatedData;
		}
	});
}

// ============================== OVERALL STATUS FOR WEEKLY, MONTHLY AND YEARLY  =====================================

function getOverallSummary(singleAppChartset,overallCharset,type) {
    $.ajax({
        type: "POST",
        data: "getPrice=1",
        url: 'http://'+deviceHost+'/methods.php',
        crossDomain: true,
		async : false,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        success: function(data) {
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
				if((weekly_sum/1000).toFixed(2) == 0.00) {
					weekly_Sum_out = weekly_sum.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" Wh";
				}
				else{
					weekly_Sum_out = (weekly_sum/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
				}
				 
				weekly_avg=weekly_sum/7;
				if((weekly_avg/1000).toFixed(2) == 0.00){
					weekly_Avg_out = weekly_avg.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" Wh";
				}
				else{;
					weekly_Avg_out = (weekly_avg/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
				}

				weekly_price=(weekly_sum/1000)*current_price;
				$('#weekly_avg').text(weekly_Avg_out);
				$('#weekly_sum').text(weekly_Sum_out);
				$('#weekly_price').text("₱ "+weekly_price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

			} else if(type==options[1]) {

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
				var divisor = 0;
				if(selecteddate == "now"){
					var d= new Date();
					var dDay = new Date(d.getYear(),d.getMonth()+1,0)
					divisor = dDay.getDate();
				} else {
					var d= new Date(selecteddate);
					var dDay = new Date(d.getYear(),d.getMonth()+1,0)
					divisor = dDay.getDate();
				}
				
				monthly_avg=monthly_sum/divisor;
				if((monthly_avg/1000).toFixed(2) == 0.00){
					monthly_Avg_out = monthly_avg.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" Wh";
				}
				else{;
					monthly_Avg_out = (monthly_avg/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
				}

				monthly_price=(monthly_sum/1000)*current_price;
				$('#monthly_avg').text(monthly_Avg_out);
				$('#monthly_sum').text(monthly_Sum_out);
				$('#monthly_price').text("₱ "+monthly_price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

			} else if(type==options[2]) {
				yearly_sum=0.0;
				for (var l = 0; l < overallCharset.length; l++) {
				  yearly_sum+=parseFloat(overallCharset[l]);
				}

				//console.log("yearly_sum = "+yearly_sum/1000);
				if((yearly_sum/1000).toFixed(2) == 0.00){
					yearly_Sum_out = yearly_sum.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" Wh";
				}
				else{
					yearly_Sum_out = (yearly_sum/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
				}

				yearly_avg=yearly_sum/12;
				if((yearly_avg/1000).toFixed(2) == 0.00){
					yearly_Avg_out = yearly_avg.toFixed(2)+" Wh";
				}
				else{;
					yearly_Avg_out = (yearly_avg/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+" kWh";
				}

				yearly_price = (yearly_sum/1000)*current_price;
				
				$('#yearly_avg').text(yearly_Avg_out);
				$('#yearly_sum').text(yearly_Sum_out);
				$('#yearly_price').text("₱ "+yearly_price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

			}
        }
    });
}

// ================================ GRAPHICAL REPRESSENTATION SECTION =============================================

function getchartdata(type){
	$.ajax({
		type: "POST",
		data: type + "=" + selecteddate + "&ts=" + $.now() + "&Day=" + dayOfWeek,
		url: 'http://' + deviceHost + '/stats.php',
		crossDomain: true,
		async : false,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function (data) {
			// console.log(data);
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
			
			console.log(data);

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
							"borderColor": Colors[weeklyID.indexOf(curUid)+1],
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
							"borderColor": Colors[monthlyID.indexOf(curUid)+1],
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
							"borderColor": Colors[yearlyID.indexOf(curUid)+1],
							"lineTension":0
						};
						yearchart.config.data.datasets.push(chartset);
						yearchart.update();
					}
				}
			}

			getOverallSummary(singleAppChartset,overallCharset,type);
		}
	});
}

// ================================ ADDITIONAL IMPORTANT FUNCTIONS ================================================

// reloading all chart and data
function reloadAll(){
	yearOpt = [];
	monthOpt = [];
	weekOpt = [];
	
	reloadWeekly();
	reloadMonthly();
	reloadYearly();
}

function reloadWeekly(){
	getWeeklyComputation();
	getchartdata(options[0]);
}

function reloadMonthly(){
	getMonthlyComputation();
	getchartdata(options[1]);
}

function reloadYearly(){
	getYearlyComputation();
	getchartdata(options[2]);
}

// search

$(".btn-search-week").click(function(){
	var instance = M.Autocomplete.getInstance($(".autoWeek"));
	searchedWeekKey = $("#searchForWeek").val();
	reloadWeekly();
});

$(".btn-search-month").click(function(){
	var instance = M.Autocomplete.getInstance($(".autoMonth"));
	searchedMonthKey = $("#searchForMonth").val();
	reloadMonthly();
});

$(".btn-search-year").click(function(){
	var instance = M.Autocomplete.getInstance($(".autoYear"));
	searchedYearKey = $("#searchForYear").val();
	reloadYearly();
});


// picking date
$('#DateReference').change(function(){
    var dateObj = new Date($(this).val());
    var month = dateObj.getMonth() + 1; //add 1 to make months from 1-12
    var day = dateObj.getDate();
    var year = dateObj.getFullYear();

    var newdate = year + "-" + month + "-" + day;
    selecteddate = newdate;
    dayOfWeek = dateObj.getDay() + 1;
    reloadAll();
});

// IF NO DATA IS AVAILABLE
Chart.plugins.register({
	afterDraw: function(chart) {
		if (chart.data.datasets.length === 0) {
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


// asynchronously fetching the price

function getPrice(){
	$.ajax({
        type: "POST",
        data: "getPrice=1",
        url: 'http://'+deviceHost+'/methods.php',
        crossDomain: true,
		async: false,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        success: function(data) {
			current_price = parseFloat(data.trim()).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
			reloadAll();
		}
	});
}


// Add appliance infos below the graphs NOTE: dirty Fix async:false REASON: sorting asynchronously produce different order

function produceApplianceCard(currentUid,total,count,type,divisor){
	var colCode = Colors[count+1];
	$.ajax({
		type: "POST",
		data: "ts="+$.now()+"&color="+colCode+"&divisor="+divisor+"&applianceSummary="+currentUid+"&price="+current_price+"&total="+total,
		url: 'http://'+deviceHost+'/stats.php',
		async: false,
		crossDomain: true,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			if(type==1){
				$('.WeeklyAppliances').append(data);

			} else if(type==2){
				$('.MonthlyAppliances').append(data);


			} else if(type==3){
				$('.YearlyAppliances').append(data);

			}
		}
	});
}

// CLEAR CHART
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
						return ((tooltipItems.yLabel)/1000).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' kWh';
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

// ================================ INITIATION ================================================

$("document").ready(function(){
	getPrice();
});