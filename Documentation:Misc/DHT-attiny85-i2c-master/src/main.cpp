#define __AVR_ATtiny84__



#include <Arduino.h>
#include "TinyWireS.h"
#include <DHT.h>
#include <Adafruit_ADXL343.h>

// dht vars
const unsigned int dhtpin = 1;
#define DHTTYPE DHT22 // DHT 22 (AM2302)
DHT dht(dhtpin, DHTTYPE);

unsigned long previousDhtSensorPolling = 0;
unsigned long dhtSensorPollingInterval = 2000;
float temperature = 0.0;
float humidity = 0.0;

// i2c vars
const byte SLAVE_ADDR = 0x64;
const byte NUM_BYTES = 4;
// command example found on http://www.gammon.com.au/i2c
enum {
    CMD_ID = 1,
    CMD_READ_TEMP  = 2,
    CMD_READ_HUMIDITY = 3,
    CMD_READ_ALL = 4
  };
char command;

void requestEvent() {
    switch (command)
     {
      case CMD_ID:  
        TinyWireS.write (0x64); 
        break;   // send our ID 
      case CMD_READ_TEMP: {
        byte *tempByte = (byte *)&temperature;
        for (byte i=0; i<NUM_BYTES; i++) {
            TinyWireS.write(tempByte[i]);
        }
        break;
      }
      case CMD_READ_HUMIDITY: {
        byte *humidByte = (byte *)&humidity;
        for (byte i=0; i<NUM_BYTES; i++) {
            TinyWireS.write(humidByte[i]);
        }
        break;
        }
      case CMD_READ_ALL: {
        byte *humidByte = (byte *)&humidity;
        byte *tempByte = (byte *)&temperature;
        for (byte i=0; i<NUM_BYTES; i++) {
            TinyWireS.write(humidByte[i]);
        }
        for (byte i=0; i<NUM_BYTES; i++) {
            TinyWireS.write(tempByte[i]);
        }
        break;
        }
     }
}



void receiveEvent (uint8_t howMany)
{
  command = TinyWireS.read();  // remember command for when we get request
} // end of receiveEvent

void setup() {
  // setup i2c
  command = 0;
  TinyWireS.begin(SLAVE_ADDR);
  TinyWireS.onReceive (receiveEvent);
  TinyWireS.onRequest(requestEvent);
  // setup dht.begin();
  dht.begin();
}

void loop() {
  unsigned long currentMillis = millis();
  if(currentMillis - previousDhtSensorPolling >= dhtSensorPollingInterval) {
    previousDhtSensorPolling = currentMillis;
    temperature = dht.readTemperature();
    humidity = dht.readHumidity();
    if (isnan(humidity) || isnan(temperature)) {
      return;
    }
  }
}

