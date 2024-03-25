
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <time.h>
#include <string.h>
#include <Wire.h>
#include <PZEM004Tv30.h>
#define Tx 1     //--->Rx pzem
#define Rx 2     //--->Tx pzem
PZEM004Tv30 pzem(Rx, Tx);

// Replace with your network credentials
const char* ssid     = "HHVsever";
const char* password = "12345678";

float voltage=0;
float current=0;
float power=0;
float energy=0;
float frequency=0;
float pf=0;

int Vsensor = A0; // voltage sensor 
float correctionfactor = 0; 
float vout = 0.0; 
float vin = 0.0; 
float R1 = 30000;     
float R2 = 7500; 
int value = 0; 

// REPLACE with your Domain name and URL path or IP address with path
const char* serverName = "http://tramsacuneti0012.000webhostapp.com/esp-post-data.php";
String apiKeyValue = "tPmAT5Ab3j7F9";
String sensorName = "Inverter";
String sensorLocation = "Tramsac1";


void setup() {

  pinMode(Vsensor, INPUT); 
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  float vtot = 0.0;
  int loops = 10;
  for (int i=0; i < loops; i++) {
    vtot = vtot + analogRead(Vsensor);
  }
  value = vtot/loops;
  vout = (value * 3.3) / 1024.0; // 3.3V
  vin = vout / (R2/(R1+R2));
  vin = vin - correctionfactor; 
    Serial.print("Voltage: "); 
    Serial.print(vin, 4);
    Serial.println("V");
  readPzem();
  //Check WiFi connection status
  if(WiFi.status()== WL_CONNECTED){
    WiFiClient client;
    HTTPClient http;
    http.begin(client, serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    
    String httpRequestData = "api_key=" + apiKeyValue + "&sensor=" + sensorName
                          + "&location=" + sensorLocation + "&value1=" + String(vin)
                          + "&value2=" + String(voltage) + "&value3=" + String(current) 
                          + "&value4=" + String(power) + "&value5=" + String(energy) 
                          + "&value6=" + String(frequency) + "&value7=" + String(pf) + "";
    Serial.print("httpRequestData: ");
    Serial.println(httpRequestData);
    
    // Send HTTP POST request
    int httpResponseCode = http.POST(httpRequestData);

        
    if (httpResponseCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    // Free resources
    http.end();
  }
  else {
    Serial.println("WiFi Disconnected");
  }
  //Send an HTTP POST request every 30 seconds
  delay(3000);  
}
void readPzem(){
    voltage = pzem.voltage();
    if( !isnan(voltage) ){
        Serial.print("Voltage: "); Serial.print(voltage); Serial.println("V");
    }
    current = pzem.current();
    if( !isnan(current) ){
        Serial.print("Current: "); Serial.print(current); Serial.println("A");
    }
    power = pzem.power();
    if( !isnan(power) ){
        Serial.print("Power: "); Serial.print(power); Serial.println("W");
    }
    energy = pzem.energy();
    if( !isnan(energy) ){
        Serial.print("Energy: "); Serial.print(energy,3); Serial.println("kWh");
    } else {
        Serial.println("Error reading energy");
    }

    frequency = pzem.frequency();
    if( !isnan(frequency) ){
        Serial.print("Frequency: "); Serial.print(frequency, 1); Serial.println("Hz");
    }
    pf = pzem.pf();
    if( !isnan(pf) ){
        Serial.print("PF: "); Serial.println(pf);
    }
}
