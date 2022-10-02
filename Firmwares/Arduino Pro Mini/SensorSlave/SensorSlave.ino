#include <Wire.h>

const byte SlaveDeviceId = 96;
// char temp[8]; //2 int, 2 dec, 1 point, and \0
// char hum[8];  
// char pm25[32];
// char pm10[32];

//character array for json string assigment
// String msg;
// char* msg_cpy;

char flag;


//library to read the temp/humid sensor
#include <Adafruit_Sensor.h>
#include <DHT.h>
#include <DHT_U.h>

//dht22 temperature/humidity sensor pin definitions

#define DHTPIN 5     // what pin the DHT is connected to
// Uncomment whatever type you're using!
//#define DHTTYPE DHT11   // DHT 11 
#define DHTTYPE DHT22   // DHT 22  (AM2302)

#define DHT22_powerPin 10


//sds011 particle matter/dust sensor pin definitions
#define SDS011_powerPin 2

#define SDS011_pm25PULSE 3

#define SDS011_pm10PULSE 4

DHT dht(DHTPIN, DHTTYPE);
// SdsDustSensor sds(SDS011_rxPin, SDS011_txPin);

void setup() {
  Serial.begin(9600); // begin serial comm for debugging
  pinMode(DHT22_powerPin, OUTPUT);  // set power pin as output
  digitalWrite(DHT22_powerPin, HIGH); // turn on the sensor
  pinMode(SDS011_powerPin, OUTPUT);  // set power pin as output
  digitalWrite(SDS011_powerPin, HIGH); // turn on the sensor
  dht.begin(); // initialize dht22 sensor
  pinMode(SDS011_pm25PULSE, INPUT); // set pm25 pin on the sds011 sensor as input, to read value 
  pinMode(SDS011_pm10PULSE, INPUT); // set pm10 pin on the sds011 sensor as input, to read value
  Wire.begin(SlaveDeviceId); //beggin slave i2c communication
  Wire.onReceive(receiveEvent); // wait for master, and send data
  Wire.onRequest(requestCallback); // register event
}
void loop(){
  
  digitalWrite(DHT22_powerPin, HIGH);
  digitalWrite(SDS011_powerPin, HIGH);

  // //get the temp/humid, pm25/pm10 into chars to format
  // ftoa(temp,readTemp());
  // ftoa(hum,readHumidity());
  // ftoa(pm25,readPm25());
  // ftoa(pm10,readPm10());
  // if(temp !="" && hum !="" && pm25 !="" && pm10 !="")
  // ftoa(temp,readTemp());
  // ftoa(hum,readHumidity());
  // ftoa(pm25,readPm25());
  // ftoa(pm10,readPm10());
  
  //Compile a json style delimited string to send to the log
  // sprintf(msg,"t=%s&h=%s&p2=%s&p1=%s",readTemp(), readHumidity(), readPm25(), readPm10());
  // msg = "t="+readTemp()+"&h="+readHumidity()+"&p2="+readPm25()+"&p1="+readPm10();
  // msg_cpy = msg.c_str();
  delay(5000); 
}

void receiveEvent(int howMany) {
  while (1 < Wire.available()) { // loop through all but the last
    flag = Wire.read(); // receive byte as a character
    Serial.print(flag);         // print the character
  }
}

void requestCallback(){
    if(flag == '1') {
      Wire.write(readTemp().c_str());
    }
    // if(flag == 1) {
    //   Wire.write(readHumidity().c_str());
    // }
    // if(flag == 3) {
    //   Wire.write(readPm25().c_str());
    // }
    // if(flag == 4) {
    //   Wire.write(readPm25().c_str());
    // }
    // else {
    //   return;
    // }
}

// return temp value
String readTemp(){
  float t = dht.readTemperature();
  String t_str = String(t);
  if((!isnan(t)) && (t > -40.000 && t < 125.000)) { //check if not nan value of the sensor and within these margins
    return t_str; //return value for temperature
  }else {
    //sensor connection problem and error
    //restart sensor here
    Serial.println("DHT22 Sensor Error");
    digitalWrite(DHT22_powerPin, LOW);
    delay(2000);
    digitalWrite(DHT22_powerPin, HIGH);   
    return;
  }
}

// return relative humidity value
String readHumidity(){
  double h = dht.readHumidity();
  String h_str = String(h);  
  if((!isnan(h)) && (h > 0 && h < 100)) { //check if not nan value of the sensor and within these margins
    return h_str; //return value for relative humidity
  }else {
    //sensor connection problem and error
    //restart sensor here
    Serial.println("DHT22 Sensor Error");
    digitalWrite(DHT22_powerPin, LOW);
    delay(2000);
    digitalWrite(DHT22_powerPin, HIGH);
    return;
  }
}


// return pm25 value
String readPm25() {
  double pm25 = pulseIn(SDS011_pm25PULSE, HIGH, 1500000) / 1000 - 2; // get sensor value as pwm pulse and convert the value in ug/m^3;
  String pm25_str = String(pm25);
  if (((pm25 > 0) && (pm25 != 0))) { // check if it is in the right range, and no sensor comm error
    return pm25_str;
  }
  else {
    //sensor connection problem and error
    //restart sensor here
    Serial.println("SDS011 Sensor Error");
    digitalWrite(SDS011_powerPin, LOW);
    delay(2000);
    digitalWrite(SDS011_powerPin, HIGH);
    return;
  }
}

// return pm10 value
String readPm10() {
  float pm10 = pulseIn(SDS011_pm10PULSE, HIGH, 1500000) / 1000 - 2; // get sensor value as pwm pulse and convert the value in ug/m^3;
  String pm10_str = String(pm10); 
  if (((pm10 > 0) && (pm10 != 0))) {  // check if it is in the right range, and no sensor comm error
    return pm10_str;
  }
  else {
    //sensor connection problem and error
    //restart sensor here
    Serial.println("SDS011 Sensor Error");
    digitalWrite(SDS011_powerPin, LOW);
    delay(2000);
    digitalWrite(SDS011_powerPin, HIGH);
    return;
  }

}


// int ftoa(char *a, float f)  //translates floating point readings into strings
// {
//   int left=int(f);
//   float decimal = f-left;
//   int right = decimal *100; //2 decimal points
//   if (right > 10) {  //if the decimal has two places already. Otherwise
//     sprintf(a, "%d.%d",left,right);
//   } else { 
//     sprintf(a, "%d.0%d",left,right); //pad with a leading 0
//   }  
// }
