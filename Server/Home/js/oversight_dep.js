var deviceHost = "";
var ip = window.localStorage.getItem('I-O-T');
if(ip){
	deviceHost = ip;
}else{
	window.location.href = "serverConfig.html";
}
