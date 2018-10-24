//#include <SerialESP8266wifi.h>
#include <deprecated.h>
#include <MFRC522.h>
#include <MFRC522Extended.h>
#include <require_cpp11.h>
#include <SoftwareSerial.h>

//Pin configuration for Internet of Things Project
//Arduino  Module Pin      
//------- --------------------
//A0    Proximity Pin OUT     
//A1    
//A2    
//A3    
//A4    
//A5    
//A6    
//A7    
//D2    Power Analyzer RX     
//D3    Power Analyzer TX
//D4    ESP8266/NodeMCU TX
//D5    ESP8266/NodeMCU RST
//D6    ESP8266/NodeMCU RX
//D7    Relay Pin IN
//D8    Buzzer Pin OUTPUT
//D9    MFRC522/RFID RST        
//D10   MFRC522/RFID SDA
//D11   MFRC522/RFID MOSI
//D12   MFRC522/RFID MISO
//D13   MFRC522/RFID SCK

//Pins are configured for Arduino Nano
#define proximityPin 0
#define relayPin 7
#define poweranalyzer_tx 2
#define poweranalyzer_rx 3
#define wifi_rx 6
#define wifi_rst 5
#define wifi_tx 4
#define mfrc522_RST 9
#define mfrc522_SDA 10
#define buzzerPin 8

//Sensitivity
#define proximity_threshold_upper 512
#define proximity_threshold_lower 40

//Configure RFID Interface
MFRC522 mfrc522(mfrc522_SDA, mfrc522_RST);

//Configure Software Serials to be used on Digital Pins
SoftwareSerial poweranalyzer(poweranalyzer_rx, poweranalyzer_tx);
SoftwareSerial wifiSerial(wifi_rx, wifi_tx);

//MFRC522 Variables
String UID_card = ""; // set to empty to avoid unexpected characters
String stringTemp = ""; // same here
byte readCard[4]; // MFRC522 has 4 bytes (8 Characters)

//Change this to Raspberry Pi configuration
//Wifi Variables
String wifiSSID = "iot_oversight";
String wifiPASS = "oversight";
String raspiIP = "192.168.2.119";
String raspiPORT = "80";

//WiFi Data to be Sent
String currentUID = "";

//boolean for switching between Serial ports
boolean powerAnalyzerTurn = true;
boolean setupcomplete = false;
boolean connectionError = false;
boolean notifStat = true;
boolean aDevice = false;
boolean relayIsOn = true;
boolean relayIsOff= true;

//Connect to Wifi at Start UP
//being called at the Setup phase
void ATconnectToWifi(){
  //relayOff();
  Serial.println("Connecting to Wifi using AT Commands");
  disconnectToHost();
  wifiSerial.println("AT+CWMODE=1");//set to STA mode (Station mode); 1 = Station mode, 2= Access Point, 3 = Both
  delay(200);
  // Set SSID and Password
  String CWJAPString = "AT+CWJAP=\"" + wifiSSID + "\",\"" +wifiPASS+"\"";
  Serial.println(CWJAPString);
  wifiSerial.println(CWJAPString);
  delay(7500);
  // Set multiple connections to ON
  wifiSerial.println("AT+CIPMUX=1"); 
  delay(200);
  // Start connection to Host
  String CIPSTARTString = "AT+CIPSTART=1,\"TCP\",\"" + raspiIP + "\"\," + raspiPORT;
  Serial.println(CIPSTARTString);
  wifiSerial.println(CIPSTARTString);
  delay(300);
  tone(buzzerPin,50,100);
  delay(150); 
  tone(buzzerPin,250,100);
  delay(150); 
  tone(buzzerPin,500,100);
  delay(150);
  tone(buzzerPin,750,100);
  delay(150);  
}

void disconnectToHost(){
  wifiSerial.println("AT+CIPCLOSE");
  delay(200);
}

void connectToHost(){
  String CIPSTARTString = "AT+CIPSTART=1,\"TCP\",\"" + raspiIP + "\"\," + raspiPORT;
  Serial.println(CIPSTARTString);
  wifiSerial.println(CIPSTARTString);
  delay(200);
}

String getID() {
  String nullString = "";
  if ( ! mfrc522.PICC_IsNewCardPresent()) { //If a new PICC placed to RFID reader continues
    return nullString;
  }
  if ( ! mfrc522.PICC_ReadCardSerial()) {   //Since a PICC placed get Serial and continue
    return nullString;
  }
  // There are Mifare PICCs which have 4 byte or 7 byte UID care if you use 7 byte PICC
  // I think we should assume every PICC as they have 4 byte UID
  // Until we support 7 byte PICCs
  UID_card = "";
  for (int i = 0; i < 4; i++) {  //
    readCard[i] = mfrc522.uid.uidByte[i];
    stringTemp = String(readCard[i],HEX);
    UID_card = UID_card + stringTemp;
  }
  mfrc522.PICC_HaltA(); // Stop reading
  return UID_card;
}

//send reset signal to Power Analyzer
void resetWattHour(){
  poweranalyzer.print("\002R\003"); //“\002”=STX, “\003”=ETX
  //Serial.println("Power Analyzer Watt-Hr Reset");
  powerAnalyzerTurn = true;
  notifStat = true; 
}

void poweranalyzerfunc(String UID){
  float watthr;
  float volt;
  float amp;
  float power;

  String voltString, ampString, powerString, watthrString;
  poweranalyzer.listen();
  if (poweranalyzer.available()>0) {
    
      if (poweranalyzer.find("Volt")){
        if (isPluggedin() == false){
          return;
        }
        volt = poweranalyzer.parseFloat();
        voltString = String (volt);
       //Serial.print("Voltage: ");
        //Serial.println(volt);
    }
      if (poweranalyzer.find("Amp")){
        if (isPluggedin() == false){
          return;
        }
        amp = poweranalyzer.parseFloat();
        ampString = String (amp);
        //Serial.print("Current: ");
        //Serial.println(amp);
    }
      if (poweranalyzer.find("Watt")){
        if (isPluggedin() == false){
          return;
        }
        power = poweranalyzer.parseFloat();
        powerString = String (power);
        //Serial.print("Power: ");
        //Serial.println(power);
    }
      if (poweranalyzer.find("Watt-Hr")){
        if (isPluggedin() == false){
          return;
        }
        watthr = poweranalyzer.parseFloat();
        watthrString = String (watthr);
        //Serial.print("Watt Hours: ");
        //Serial.println(watthr);
        
        //convert everything to string

        //Uncomment to Send power data only
        //String message = powersenddata(voltString,ampString,powerString,watthrString);

        //Send power data with UID
        String message = sendSignedPowerData(UID,voltString,ampString,powerString,watthrString);
        Serial.println(message);
    }
  }
}

void clearUIDMemory(){
  currentUID = "";
}

boolean isPluggedin(){
  if(proximitySensor() > proximity_threshold_upper){
    //Serial.println("No Appliance");
    relayOff();
    clearUIDMemory();
    return false;
  }
  else if (proximitySensor() < proximity_threshold_lower){
    //Serial.println("Appliance Plugged IN");
    return true;
  }
}

int proximitySensor(){
  int proximity_value = analogRead(proximityPin);
  return proximity_value;
}

void relayOn(){
  digitalWrite(relayPin, LOW);
  Serial.println("Relay ON");
}

void relayOff(){
  digitalWrite(relayPin, HIGH);
  //Serial.println("Relay OFF");
}

//Legacy Code for Debugging
String powersenddata(String volt, String amp, String power, String watthr){
  connectToHost();
  String message = volt + "||" + amp + "||" + power + "||" + watthr;
  String PHPmessage = "GET /powerdata.php?powerdata=" + message +" HTTP/1.1\r\nHost: " + raspiIP + ":" + raspiPORT+ "\r\n\r\n";
  String commandSend = "AT+CIPSEND=1," + String(PHPmessage.length());
  wifiSerial.println(commandSend); //Send to ID 1, length DATALENGTH
  delay(200);
  wifiSerial.println(PHPmessage); // Print Data
  delay(3000);
  return message;
}

String sendUIDtoServer(String UID){
  connectToHost();
  String PHPmessage = "GET /getUID.php?UID=" + UID +" HTTP/1.1\r\nHost: " + raspiIP + ":" + raspiPORT+ "\r\n\r\n";
  String commandSend = "AT+CIPSEND=1," + String(PHPmessage.length());
  wifiSerial.println(commandSend); //Send to ID 1, length DATALENGTH
  delay(200);
  wifiSerial.println(PHPmessage); // Print Data
  return UID;
}

//Legacy Code end

//Send power data together with current UID that uses the power socket
String sendSignedPowerData(String UID, String volt, String amp, String power, String watthr){
  connectToHost();
  String message = volt + "||" + amp + "||" + power + "||" + watthr;
  String PHPmessage = "GET /signedPowerData.php?UID=" + UID + "&unplugged=false&powerdata=" + message +"&notifStat=" + notifStat +"&aDevice=" + aDevice +" HTTP/1.1\r\nHost: " + raspiIP + ":" + raspiPORT+ "\r\n\r\n";
  String commandSend = "AT+CIPSEND=1," + String(PHPmessage.length());
  wifiSerial.println(commandSend); //Send to ID 1, length DATALENGTH
  delay(200);
  wifiSerial.println(PHPmessage); // Print Data

  String catMessage = UID + "||" + message + "||" + aDevice ;
  powerAnalyzerTurn = false;
  return catMessage;
}

//only useful for node connections

void noAppliancePlugged(){
  connectToHost();
  String message = "0||0||0||0";
  String PHPmessage = "GET /signedPowerData.php?UID=NO_UID&unplugged=true&powerdata=" + message +"&notifStat=" + notifStat +"&aDevice=" + aDevice +" HTTP/1.1\r\nHost: " + raspiIP + ":" + raspiPORT+ "\r\n\r\n";
  String commandSend = "AT+CIPSEND=1," + String(PHPmessage.length());
  wifiSerial.println(commandSend); //Send to ID 1, length DATALENGTH
  delay(200);
  wifiSerial.println(PHPmessage); // Print Data

  String catMessage = "NO_UID||1||" + message + "||" + aDevice ;
  Serial.println(catMessage);
  powerAnalyzerTurn = false;
  return catMessage;
}

void findJSON(){
  wifiSerial.listen();
  //Serial.println("wifiSerial is Listening");
  Serial.print(wifiSerial.available());
  while (wifiSerial.available() == 0 && powerAnalyzerTurn == false){
     Serial.print(wifiSerial.available());
      String c = wifiSerial.readString();
      Serial.print(c);
      powerAnalyzerTurn = true;
      Serial.print(powerAnalyzerTurn);
  }
}

void parseJSON(){
  int has_power;
  //Serial.println(notifStat);  
  //Serial.print("");
  wifiSerial.listen();
  //while (wifiSerial.available() > 0 && powerAnalyzerTurn == false){
    String c = wifiSerial.readString();
    Serial.println(c);
    if (c.indexOf("\"has\_power\"\: \"0\"") > 0){
      //Serial.println("has_power: 0");
      if(relayIsOff == true){
        relayIsOff = false;
        relayIsOn= true;
        tone(buzzerPin, 500, 100);
        delay(100);  
      }
      relayOff();
      powerAnalyzerTurn = true;
      //break;
    }
    if (c.indexOf("\"has\_power\"\: \"1\"") > 0){
      //Serial.println("has_power: 1");
       if(relayIsOn == true){
        relayIsOn = false;
        relayIsOff= true;
        tone(buzzerPin, 1000, 100);
        delay(100);  
      }
      relayOn();
      powerAnalyzerTurn = true;
      //break;
    }
    notifStat = false;
    if (c.indexOf("1,CLOSED") > 0 || c.indexOf("busy p") > 0 || c.indexOf("SEND FAIL") > 0 ){
      relayOff();
      Serial.println("Connection Lost, Reconnecting...");
      connectionError = true;
      if(connectionError == true){
         for (int i = 0; i < 1; i++) {
            tone(buzzerPin, 250, 250); //Middle C 
            delay(500);
            tone(buzzerPin, 500, 250); //E 
            delay(500);
            tone(buzzerPin, 1000, 250); //A 
            delay(500);
      }
      connectionError = false;
      setupcomplete= true;
      }
      ATconnectToWifi();
      //resetWattHour();
      powerAnalyzerTurn = true;
      //break;
      
    }
    /*else if(c.indexOf("ERROR") > 0){
      connectionError = true;
      if(connectionError == true){
         for (int i = 0; i < 2; i++) {
           delay(1000);
           relayOn();
           delay(1000);
           relayOff();
      }
      parseJSON();
      }
    }*/
  //} 
}

void setup() {
  // Power socket initial Setup
  // Different Serial Processes needs to have different baud rate to be recognized
  poweranalyzer.begin(9600);
  Serial.begin(19200);
  wifiSerial.begin(4800);
  

  Serial.println("Serial baudrate: SET");
  //Configure to listen to devices
  wifiSerial.listen();
  poweranalyzer.listen();
  Serial.println("Serial listen set to ON");
  
  poweranalyzer.print("\002M4\003"); //“\002”=STX, “\003”=ETX
  Serial.println("Power Analyzer set to MODE 4");

  //configure pins for Digital output and analog input
  pinMode(relayPin, OUTPUT);
  pinMode(proximityPin, INPUT);
  pinMode(buzzerPin,OUTPUT);

  relayOff();
  Serial.println("Relay and Proximity Pins SET");
  
  //begin SPI interface for MFRC522
  SPI.begin();
  mfrc522.PCD_Init(); //Initialize MFRC522 Hardware
  //mfrc522.PCD_SetAntennaGain(mfrc522.RxGain_max);

  Serial.println("RFID Initialized");
  
  //begin wifi interface for ESP8266/NodeMCU
  ATconnectToWifi();
  delay(1000);
  Serial.println("Setup is complete!");
}

void loop() {
 // put your main code here, to run repeatedly:
 
  if(setupcomplete == false){
      tone(buzzerPin, 1000, 100);
      delay(200);
      setupcomplete= true;
  }
  
  while(isPluggedin()){ 
    //delay(200);
    //Serial.println("Appliance Plugged IN");
    //delay(200);
    aDevice=true;
    if(currentUID == ""){
      currentUID = getID();     
    }
    
    if(currentUID != ""){
      // check if UID is allowed to have power
      //Serial.println("Sending to Server: " + pluggedAppliance);
      //sendUIDtoServer(pluggedAppliance);
      notifStat=true;
      Serial.println(notifStat); 
      while(isPluggedin()){
        delay(200);
        Serial.println("UID FOUND");
        //relayOn();
        //send signed powerdata
        if(powerAnalyzerTurn == true){
          //delay(200);
          Serial.print("Power Analyzer Data\r\n");
          //delay(200);
          //Serial.println(notifStat); 
          poweranalyzerfunc(currentUID);
        }
        else {
          //delay(200);
          //findJSON();
          Serial.print("Data Send To Server\r\n");
          //delay(200);
          if(notifStat== true){
            tone(buzzerPin, 1000, 100);
            delay(100);
          }
          parseJSON();
          //delay(100);
        }
     }
     
    }
    else {
      delay(200);
      Serial.println("No UID FOUND!");
      //noUIDFoundNotif();
      //currentUID = "NO_UID";
      //while(isPluggedin()){
        if(powerAnalyzerTurn == true){
          //delay(200);
          Serial.print("Power Analyzer Data\r\n");
          //delay(200);
          poweranalyzerfunc("NO_UID");
        } 
        else {
          //delay(200);
          //findJSON();
          Serial.print("Data Send To Server\r\n");
          //delay(200);
          if(notifStat== true){
            tone(buzzerPin, 1000, 100);
            delay(100);
            tone(buzzerPin, 500, 100);
            delay(100);
          }
          parseJSON();
        }
        
      //}
    }
  }
  if(isPluggedin()== false){
     //Serial.println("Appliance Unplugged");
     
     if(notifStat== false){
        tone(buzzerPin, 500, 100);
        delay(100);
        tone(buzzerPin, 1000, 100);
        delay(100);
      }
      if(aDevice == true){
        aDevice=false;
        noAppliancePlugged();
      }
      relayOff();
      resetWattHour();
  }
}

