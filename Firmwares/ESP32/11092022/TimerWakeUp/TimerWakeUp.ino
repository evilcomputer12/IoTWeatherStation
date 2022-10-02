// Your GPRS credentials (leave empty, if not needed)
const char apn[]      = "data.lycamobile.mk"; // APN (example: internet.vodafone.pt) use https://wiki.apnchanger.org
const char gprsUser[] = "lmmk"; // GPRS User
const char gprsPass[] = "plus"; // GPRS Password

// SIM card PIN (leave empty, if not defined)
const char simPIN[]   = ""; 

// Server details
// The server variable can be just a domain name or it can have a subdomain. It depends on the service you are using
const char server[] = "daffer.mk"; // domain name: example.com, maker.ifttt.com, etc
const char resource[] = "/post-data.php";         // resource path, for example: /post-data.php
const int  port = 80;                             // server port number

// Keep this API Key value to be compatible with the PHP code provided in the project page. 
// If you change the apiKeyValue value, the PHP file /post-data.php also needs to have the same key 
String apiKeyValue = "JUlzGXNaj7BJe";

// TTGO T-Call pins
#define UART_BAUD   9600


// Set serial for debug console (to Serial Monitor, default speed 115200)
#define SerialMon Serial
// Set serial for AT commands (to SIM800 module)
#define SerialAT Serial1

// Configure TinyGSM library
#define TINY_GSM_MODEM_SIM7000
#define TINY_GSM_RX_BUFFER 1024 // Set RX buffer to 1Kb

// Define the serial console for debug prints, if needed
//#define DUMP_AT_COMMANDS

#include <Wire.h>


// Define Slave I2C Address 
#define SLAVE_ADDR 0x64
#define TEMP_REGISTER 1
#define HUM_REGISTER 2
#define PM25_REGISTER 3
#define PM10_REGISTER 4

// Define Slave answer size
#define ANSWERSIZE_DHT 8
#define ANSWERSIZE_SDS 32

#define ARDUINO_POWER_PIN 33

#define PIN_ADC_BAT 35
#define PIN_ADC_SOLAR 36
#define ADC_BATTERY_LEVEL_SAMPLES 100

uint16_t v_bat = 0;
uint16_t v_solar = 0;




#include <TinyGsmClient.h>

#ifdef DUMP_AT_COMMANDS
  #include <StreamDebugger.h>
  StreamDebugger debugger(SerialAT, SerialMon);
  TinyGsm modem(debugger);
#else
  TinyGsm modem(SerialAT);
#endif

// TinyGSM Client for Internet connection
TinyGsmClient client(modem);

#define uS_TO_S_FACTOR 1000000ULL  /* Conversion factor for micro seconds to seconds */
#define TIME_TO_SLEEP  540        /* Time ESP32 will go to sleep (in seconds) */

RTC_DATA_ATTR int bootCount = 0;

/*
Method to print the reason by which ESP32
has been awaken from sleep
*/
void print_wakeup_reason(){
  esp_sleep_wakeup_cause_t wakeup_reason;

  wakeup_reason = esp_sleep_get_wakeup_cause();

  switch(wakeup_reason)
  {
    case ESP_SLEEP_WAKEUP_EXT0 : SerialMon.println("Wakeup caused by external signal using RTC_IO"); break;
    case ESP_SLEEP_WAKEUP_EXT1 : SerialMon.println("Wakeup caused by external signal using RTC_CNTL"); break;
    case ESP_SLEEP_WAKEUP_TIMER : SerialMon.println("Wakeup caused by timer"); break;
    case ESP_SLEEP_WAKEUP_TOUCHPAD : SerialMon.println("Wakeup caused by touchpad"); break;
    case ESP_SLEEP_WAKEUP_ULP : SerialMon.println("Wakeup caused by ULP program"); break;
    default : Serial.printf("Wakeup was not caused by deep sleep: %d\n",wakeup_reason); break;
    delay(500);
  }
}

void read_adc_bat(uint16_t *voltage)
{
  uint32_t in = 0;
  for (int i = 0; i < ADC_BATTERY_LEVEL_SAMPLES; i++)
  {
    in += (uint32_t)analogRead(PIN_ADC_BAT);
  }
  in = (int)in / ADC_BATTERY_LEVEL_SAMPLES;

  uint16_t bat_mv = ((float)in / 4096) * 3600 * 2;

  *voltage = bat_mv;
}

void read_adc_solar(uint16_t *voltage)
{
  uint32_t in = 0;
  for (int i = 0; i < ADC_BATTERY_LEVEL_SAMPLES; i++)
  {
    in += (uint32_t)analogRead(PIN_ADC_SOLAR);
  }
  in = (int)in / ADC_BATTERY_LEVEL_SAMPLES;

  uint16_t bat_mv = ((float)in / 4096) * 3600 * 2;

  *voltage = bat_mv;
}



void sendCommand (const byte cmd, const int responseSize)
{
  Wire.beginTransmission(SLAVE_ADDR);
  Wire.write(cmd);
  Wire.endTransmission();
  
  Wire.requestFrom(SLAVE_ADDR, responseSize);  
}  // end of sendCommand

union {
 uint8_t b_temp[4];
 float f_temp;
} temp_data;

union {
 uint8_t b_hum[4];
 float f_hum;
} hum_data;

union {
 uint8_t b_pm25[8];
 float f_pm25;
} pm25_data;

union {
 uint8_t b_pm10[8];
 float f_pm10;
} pm10_data;

float getTemp() {
  sendCommand(2, 4);
  while(Wire.available()){
    for(uint8_t i = 0; i < 4; i++){
      temp_data.b_temp[i] = Wire.read();
    }
  }
  return temp_data.f_temp;
}

float getHum() {
  sendCommand(3, 4);
  while(Wire.available()){
    for(uint8_t i = 0; i < 4; i++){
      hum_data.b_hum[i] = Wire.read();
    }
  }
  return hum_data.f_hum;
}

float getPm25() {
  sendCommand(4, 8);
  while(Wire.available()){
    for(uint8_t i = 0; i < 8; i++){
      pm25_data.b_pm25[i] = Wire.read();
    }
  }
  return pm25_data.f_pm25;
}

float getPm10() {
  sendCommand(5, 8);
  while(Wire.available()){
    for(uint8_t i = 0; i < 8; i++){
      pm10_data.b_pm10[i] = Wire.read();
    }
  }
  return pm10_data.f_pm10;
}



void setup(){
  pinMode(ARDUINO_POWER_PIN, OUTPUT);
  digitalWrite(ARDUINO_POWER_PIN, HIGH);
  Wire.begin();
  SerialMon.begin(9600);
  delay(1000); //Take some time to open up the Serial Monitor

  //Increment boot number and print it every reboot
  ++bootCount;
  SerialMon.println("Boot number: " + String(bootCount));

  //Print the wakeup reason for ESP32
  print_wakeup_reason();

  pinMode(4, OUTPUT);
  digitalWrite(4, LOW);
  delay(1000);
  digitalWrite(4, HIGH);
  
  
  
  pinMode(PIN_ADC_BAT, INPUT);
  pinMode(PIN_ADC_SOLAR, INPUT);

  
  
  String name = modem.getModemName();
  delay(500);
  SerialMon.println("Modem Name: " + name);

  

  String modemInfo = modem.getModemInfo();
  delay(500);
  SerialMon.println("Modem Info: " + modemInfo);

  
  // Set GSM module baud rate and UART pins
  SerialAT.begin(UART_BAUD, SERIAL_8N1, 26, 27);
  delay(3000);

  // Restart SIM800 module, it takes quite some time
  // To skip it, call init() instead of restart()
  SerialMon.println("Initializing modem...");
  modem.restart();
  // use modem.init() if you don't need the complete restart

  // Unlock your SIM card with a PIN if needed
  if (strlen(simPIN) && modem.getSimStatus() != 3 ) {
    modem.simUnlock(simPIN);
  }


  int   year3    = 0;
  int   month3   = 0;
  int   day3     = 0;
  int   hour3    = 0;
  int   min3     = 0;
  int   sec3     = 0;
  float timezone = 0;
  for (int8_t i = 5; i; i--) {
    SerialMon.println("Requesting current network time");
    if (modem.getNetworkTime(&year3, &month3, &day3, &hour3, &min3, &sec3,
                             &timezone)) {
        SerialMon.println(hour3);
        
    }
  }    
  
  /*
  First we configure the wake up source
  We set our ESP32 to wake up every 5 seconds
  */
  if(hour3 >= 17){
     esp_sleep_enable_timer_wakeup(3600 * uS_TO_S_FACTOR);
  }else{
      esp_sleep_enable_timer_wakeup(TIME_TO_SLEEP * uS_TO_S_FACTOR);
  }
  
  SerialMon.println("Setup ESP32 to sleep for every " + String(TIME_TO_SLEEP) +
  " Seconds");
  
  read_adc_bat(&v_bat);
  read_adc_solar(&v_solar);
  SerialMon.println("Get sensor data");
  String temp = String(getTemp());
  String hum = String(getHum());
  String pm25 = String(getPm25());
  String pm10 = String(getPm10());
  String v_sol_str = String(v_solar);
  String v_bat_str = String(v_bat);
  SerialMon.println(temp+hum+pm25+pm10+v_sol_str+v_bat_str);
  
  /*
  Next we decide what all peripherals to shut down/keep on
  By default, ESP32 will automatically power down the peripherals
  not needed by the wakeup source, but if you want to be a poweruser
  this is for you. Read in detail at the API docs
  http://esp-idf.readthedocs.io/en/latest/api-reference/system/deep_sleep.html
  Left the line commented as an example of how to configure peripherals.
  The line below turns off all RTC peripherals in deep sleep.
  */
  //esp_deep_sleep_pd_config(ESP_PD_DOMAIN_RTC_PERIPH, ESP_PD_OPTION_OFF);
  //Serial.println("Configured all RTC Peripherals to be powered down in sleep");

  /*
  Now that we have setup a wake cause and if needed setup the
  peripherals state in deep sleep, we can now start going to
  deep sleep.
  In the case that no wake up sources were provided but deep
  sleep was started, it will sleep forever unless hardware
  reset occurs.
  */
  SerialMon.print("Connecting to APN: ");
  SerialMon.print(apn);
  if (!modem.gprsConnect(apn, gprsUser, gprsPass)) {
    SerialMon.println(" fail");
  }
  else {
    SerialMon.println(" OK");
    
    SerialMon.print("Connecting to ");
    SerialMon.print(server);
    if (!client.connect(server, port)) {
      SerialMon.println(" fail");
    }
    else {
      SerialMon.println(" OK");
    
      // Making an HTTP POST request
      SerialMon.println("Performing HTTP POST request...");
      // Prepare your HTTP POST request data (Temperature in Celsius degrees)
      // Prepare your HTTP POST request data (Temperature in Fahrenheit degrees)
      //String httpRequestData = "api_key=" + apiKeyValue + "&value1=" + String(1.8 * bme.readTemperature() + 32)
      //                       + "&value2=" + String(bme.readHumidity()) + "&value3=" + String(bme.readPressure()/100.0F) + "";
          
      // You can comment the httpRequestData variable above
      // then, use the httpRequestData variable below (for testing purposes without the BME280 sensor)
      //String httpRequestData = "api_key=tPmAT5Ab3j7F9&value1=24.75&value2=49.54&value3=1005.14";
      
      String httpRequestData = "api_key="+apiKeyValue+"&temp="+temp+"&hum="+hum+"&pm25="+pm25+"&pm10="+pm10+"&vbat="+v_bat_str+"&vsol="+v_sol_str;
      SerialMon.println(httpRequestData);
      //String httpRequestData = "api_key=JUlzGXNaj7BJe&temp=35&hum=50&pm25=8&pm10=10&vbat=4900&vsol=5500";
      client.print(String("POST ") + resource + " HTTP/1.1\r\n");
      client.print(String("Host: ") + server + "\r\n");
      client.println("Connection: close");
      client.println("Content-Type: application/x-www-form-urlencoded");
      client.print("Content-Length: ");
      client.println(httpRequestData.length());
      client.println();
      client.println(httpRequestData);

      unsigned long timeout = millis();
      while (client.connected() && millis() - timeout < 10000L) {
        // Print available data (HTTP response from server)
        while (client.available()) {
          char c = client.read();
          SerialMon.print(c);
          timeout = millis();
        }
      }
      SerialMon.println();
    
      // Close client and disconnect
      client.stop();
      SerialMon.println(F("Server disconnected"));
      modem.gprsDisconnect();
      SerialMon.println(F("GPRS disconnected"));
      delay(1000); 
    }
  }
  sendCommand(6, 4);
  digitalWrite(ARDUINO_POWER_PIN,LOW);
  delay(5000);
  SerialMon.println("Going to sleep now");
  SerialMon.flush();
  esp_deep_sleep_start();
  SerialMon.println("This will never be printed");
}

void loop(){
  //This is not going to be called
}
