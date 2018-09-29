var deviceHost = "";
var ip = window.localStorage.getItem('I-O-T');
if(ip){
	deviceHost = ip;
}else{
  var xsam = prompt("Enter Ip Address: ");
  deviceHost = xsam;
  window.localStorage.setItem('I-O-T',deviceHost);
}