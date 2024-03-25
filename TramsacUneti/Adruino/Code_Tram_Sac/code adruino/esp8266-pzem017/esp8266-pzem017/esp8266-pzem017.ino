
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <time.h>
#include <string.h>
#include <SoftwareSerial.h>                           /* include virtual Serial Port coding */
SoftwareSerial PZEMSerial; 
#include <ModbusMaster.h>                             // Load the (modified) library for modbus communication command codes. Kindly install at our website.
#define MAX485_RO  D7
#define MAX485_RE  D6
#define MAX485_DE  D5
#define MAX485_DI  D0 

// Replace with your network credentials
const char* ssid     = "HHVsever";
const char* password = "12345678";

static uint8_t pzemSlaveAddr = 0x01;                  // Declare the address of device (meter 1) in term of 8 bits. 
static uint8_t pzemSlaveAddr2 = 0x02;                 // Declare the address of device (meter 2) in term of 8 bits. 
static uint16_t NewshuntAddr = 0x0001;                // Declare your external shunt value for DC Meter. Default 0x0000 is 100A, replace to "0x0001" if using 50A shunt, 0x0002 is for 200A, 0x0003 is for 300A
static uint16_t NewshuntAddr2 = 0x0001;

        ModbusMaster node;                                    /* activate modbus master codes*/  
        ModbusMaster node2;
        float PZEMVoltage =0;                                 /* Declare value for DC voltage */
        float PZEMCurrent =0;                                 /* Declare value for DC current*/
        float PZEMPower =0;                                   /* Declare value for DC Power */
        float PZEMEnergy=0;                                   /* Declare value for DC Energy */
        
        float PZEMVoltage2 =0;                                 /* Declare value for DC voltage */
        float PZEMCurrent2 =0;                                 /* Declare value for DC current*/
        float PZEMPower2 =0;                                   /* Declare value for DC Power */
        float PZEMEnergy2 =0;
        unsigned long startMillisPZEM;                        /* start counting time for LCD Display */
        unsigned long currentMillisPZEM;                      /* current counting time for LCD Display */
        unsigned long startMilliSetShunt; 
        const unsigned long periodPZEM = 500;
        unsigned long startMillis1;                           // to count time during initial start up

// REPLACE with your Domain name and URL path or IP address with path
const char* serverName = "http://tramsacuneti0012.000webhostapp.com/esp-post-dataDC.php";
String apiKeyValue = "tPmAT5Ab3j7F9";
String sensorName = "Inverter";
String sensorLocation = "Tramsac1";
void preTransmission()                                                                                    /* transmission program when triggered*/
{
        /* 1- PZEM-017 DC Energy Meter */
        
        if(millis() - startMillis1 > 5000)                                                                // Wait for 5 seconds as ESP Serial cause start up code crash
        {
          digitalWrite(MAX485_RE, 1);                                                                     /* put RE Pin to high*/
          digitalWrite(MAX485_DE, 1);                                                                     /* put DE Pin to high*/
          delay(1);                                                                                       // When both RE and DE Pin are high, converter is allow to transmit communication
        }
}

void postTransmission()                                                                                   /* Reception program when triggered*/
{
        
        /* 1- PZEM-017 DC Energy Meter */
  
        if(millis() - startMillis1 > 5000)                                                                // Wait for 5 seconds as ESP Serial cause start up code crash
        {
          delay(3);                                                                                       // When both RE and DE Pin are low, converter is allow to receive communication
          digitalWrite(MAX485_RE, 0);                                                                     /* put RE Pin to low*/
          digitalWrite(MAX485_DE, 0);                                                                     /* put DE Pin to low*/
        }
}

void setShunt(uint8_t slaveAddr)                                                                          //Change the slave address of a node
{

        /* 1- PZEM-017 DC Energy Meter */
        
        static uint8_t SlaveParameter = 0x06;                                                             /* Write command code to PZEM */
        static uint16_t registerAddress = 0x0003;                                                         /* change shunt register address command code */
        
        uint16_t u16CRC = 0xFFFF;                                                                         /* declare CRC check 16 bits*/
        u16CRC = crc16_update(u16CRC, slaveAddr);                                                         // Calculate the crc16 over the 6bytes to be send
        u16CRC = crc16_update(u16CRC, SlaveParameter);
        u16CRC = crc16_update(u16CRC, highByte(registerAddress));
        u16CRC = crc16_update(u16CRC, lowByte(registerAddress));
        u16CRC = crc16_update(u16CRC, highByte(NewshuntAddr));
        u16CRC = crc16_update(u16CRC, lowByte(NewshuntAddr));
      
        preTransmission();                                                                                /* trigger transmission mode*/
      
        PZEMSerial.write(slaveAddr);                                                                      /* these whole process code sequence refer to manual*/
        PZEMSerial.write(SlaveParameter);
        PZEMSerial.write(highByte(registerAddress));
        PZEMSerial.write(lowByte(registerAddress));
        PZEMSerial.write(highByte(NewshuntAddr));
        PZEMSerial.write(lowByte(NewshuntAddr));
        PZEMSerial.write(lowByte(u16CRC));
        PZEMSerial.write(highByte(u16CRC));
        delay(10);
        postTransmission();                                                                               /* trigger reception mode*/
        delay(100);
        while (PZEMSerial.available())                                                                    /* while receiving signal from Serial3 from meter and converter */
          {   
          }
}

void setShunt2(uint8_t slaveAddr2)                                                                        //Change the slave address of a node
{

        /* 1- PZEM-017 DC Energy Meter */
        
        static uint8_t SlaveParameter2 = 0x06;                                                            /* Write command code to PZEM */
        static uint16_t registerAddress2 = 0x0003;                                                        /* change shunt register address command code */
        
        uint16_t u16CRC2 = 0xFFFF;                                                                        /* declare CRC check 16 bits*/
        u16CRC2 = crc16_update(u16CRC2, slaveAddr2);                                                      // Calculate the crc16 over the 6bytes to be send
        u16CRC2 = crc16_update(u16CRC2, SlaveParameter2);
        u16CRC2 = crc16_update(u16CRC2, highByte(registerAddress2));
        u16CRC2 = crc16_update(u16CRC2, lowByte(registerAddress2));
        u16CRC2 = crc16_update(u16CRC2, highByte(NewshuntAddr2));
        u16CRC2 = crc16_update(u16CRC2, lowByte(NewshuntAddr2));
      
        preTransmission();                                                                                /* trigger transmission mode*/
      
        PZEMSerial.write(slaveAddr2);                                                                     /* these whole process code sequence refer to manual*/
        PZEMSerial.write(SlaveParameter2);
        PZEMSerial.write(highByte(registerAddress2));
        PZEMSerial.write(lowByte(registerAddress2));
        PZEMSerial.write(highByte(NewshuntAddr2));
        PZEMSerial.write(lowByte(NewshuntAddr2));
        PZEMSerial.write(lowByte(u16CRC2));
        PZEMSerial.write(highByte(u16CRC2));
        delay(10);
        postTransmission();                                                                               /* trigger reception mode*/
        delay(100);
        while (PZEMSerial.available())                                                                    /* while receiving signal from Serial3 from meter and converter */
          {   
          }
}

void setup() {
  startMillis1 =millis();
  Serial.begin(115200);
  PZEMSerial.begin(9600, SWSERIAL_8N2, MAX485_RO, MAX485_DI);
  startMilliSetShunt = millis();
  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
   /* 1- PZEM-017 DC Energy Meter */
        
        startMillisPZEM = millis();                           /* Start counting time for run code */
        pinMode(MAX485_RE, OUTPUT);                           /* Define RE Pin as Signal Output for RS485 converter. Output pin means Arduino command the pin signal to go high or low so that signal is received by the converter*/
        pinMode(MAX485_DE, OUTPUT);                           /* Define DE Pin as Signal Output for RS485 converter. Output pin means Arduino command the pin signal to go high or low so that signal is received by the converter*/
        digitalWrite(MAX485_RE, 0);                           /* Arduino create output signal for pin RE as LOW (no output)*/
        digitalWrite(MAX485_DE, 0);                           /* Arduino create output signal for pin DE as LOW (no output)*/
                                                              // both pins no output means the converter is in communication signal receiving mode
        node.preTransmission(preTransmission);                // Callbacks allow us to configure the RS485 transceiver correctly
        node.postTransmission(postTransmission);
        node2.preTransmission(preTransmission);                // Callbacks allow us to configure the RS485 transceiver correctly
        node2.postTransmission(postTransmission);                   
        delay(1000);       

}
void loop() {
    currentMillisPZEM = millis();
    if (millis()- startMilliSetShunt == 10000) 
        {setShunt(0x01);}
    if (millis()-startMilliSetShunt == 15000)                          
        {setShunt2(0x02);}
        node.begin(pzemSlaveAddr, PZEMSerial);                                                          /* Define and start the Modbus RTU communication. Communication to specific slave address and which Serial port */
    if (currentMillisPZEM - startMillisPZEM >= periodPZEM)                                          /* for every x seconds, run the codes below*/
          {
          uint8_t result;                                                                                 /* Declare variable "result" as 8 bits */   
          result = node.readInputRegisters(0x0000, 6);                                                    /* read the 9 registers (information) of the PZEM-014 / 016 starting 0x0000 (voltage information) kindly refer to manual)*/
          if (result == node.ku8MBSuccess)                                                                /* If there is a response */
            {
              uint32_t tempdouble = 0x00000000;                                                           /* Declare variable "tempdouble" as 32 bits with initial value is 0 */ 
              PZEMVoltage = node.getResponseBuffer(0x0000) / 100.0;                                       /* get the 16bit value for the voltage value, divide it by 100 (as per manual)- 0x0000 to 0x0008 are the register address of the measurement value*/
              PZEMCurrent = node.getResponseBuffer(0x0001) / 100.0;                                       /* get the 16bit value for the current value, divide it by 100 (as per manual) */
              tempdouble =  (node.getResponseBuffer(0x0003) << 16) + node.getResponseBuffer(0x0002);      /* get the power value. Power value is consists of 2 parts (2 digits of 16 bits in front and 2 digits of 16 bits at the back) and combine them to an unsigned 32bit */
              PZEMPower = tempdouble / 10.0;                                                              /* Divide the value by 10 to get actual power value (as per manual) */
              tempdouble =  (node.getResponseBuffer(0x0005) << 16) + node.getResponseBuffer(0x0004);      /* get the energy value. Energy value is consists of 2 parts (2 digits of 16 bits in front and 2 digits of 16 bits at the back) and combine them to an unsigned 32bit */
              PZEMEnergy = tempdouble;                                                                    
            }
          if (pzemSlaveAddr==5)                                                                           /* just for checking purpose to see whether can read modbus*/
              {}
          else
              {}
              startMillisPZEM = currentMillisPZEM ; 
          } 
         node2.begin(pzemSlaveAddr2, PZEMSerial);
          if (currentMillisPZEM - startMillisPZEM >= periodPZEM)                                            /* for every x seconds, run the codes below*/
          {
             uint8_t result2;                                                                                 /* Declare variable "result" as 8 bits */   
          result2 = node2.readInputRegisters(0x0000, 6);                                                    /* read the 9 registers (information) of the PZEM-014 / 016 starting 0x0000 (voltage information) kindly refer to manual)*/
            if (result2 == node2.ku8MBSuccess)                                                                /* If there is a response */
            {
              uint32_t tempdouble2 = 0x00000000;                                                           /* Declare variable "tempdouble" as 32 bits with initial value is 0 */ 
              PZEMVoltage2 = node2.getResponseBuffer(0x0000) / 100.0;                                       /* get the 16bit value for the voltage value, divide it by 100 (as per manual)- 0x0000 to 0x0008 are the register address of the measurement value*/
              PZEMCurrent2 = node2.getResponseBuffer(0x0001) / 100.0;                                       /* get the 16bit value for the current value, divide it by 100 (as per manual) */
              tempdouble2 =  (node2.getResponseBuffer(0x0003) << 16) + node2.getResponseBuffer(0x0002);      /* get the power value. Power value is consists of 2 parts (2 digits of 16 bits in front and 2 digits of 16 bits at the back) and combine them to an unsigned 32bit */
              PZEMPower2 = tempdouble2 / 10.0;                                                              /* Divide the value by 10 to get actual power value (as per manual) */
              tempdouble2 =  (node2.getResponseBuffer(0x0005) << 16) + node2.getResponseBuffer(0x0004);      /* get the energy value. Energy value is consists of 2 parts (2 digits of 16 bits in front and 2 digits of 16 bits at the back) and combine them to an unsigned 32bit */
              PZEMEnergy2 = tempdouble2;                                                                    
            }
              if (pzemSlaveAddr==5)                                                                       /* just for checking purpose to see whether can read modbus*/
              {}
              else
              {}
              startMillisPZEM = currentMillisPZEM ;        
            }
      
                   
  //Check WiFi connection status
  if(WiFi.status()== WL_CONNECTED){
    WiFiClient client;
    HTTPClient http;
    http.begin(client, serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    
    String httpRequestData = "api_key=" + apiKeyValue + "&sensor=" + sensorName
                          + "&location=" + sensorLocation + "&value1=" + String(PZEMVoltage)
                          + "&value2=" + String(PZEMCurrent) + "&value3=" + String(PZEMPower) 
                          + "&value4=" + String(PZEMEnergy) + "&value5=" + String(PZEMVoltage2) 
                          + "&value6=" + String(PZEMCurrent2) + "&value7=" + String(PZEMPower2) + "&value8=" + String(PZEMEnergy2) +"";
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
