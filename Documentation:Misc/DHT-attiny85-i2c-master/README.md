# Exposing a DHT22 sensor using an Attiny85 and i2c

I had a DHT-22 sensor laying around that I wanted to integrate in a bigger project.
While integrating the sensor the delay that the default DHT libraries caused became a blocking issue.

I tried a couple of supposidly "non_blocking" libraries but never got it to work in the whole project.

When looking for a solution I came up with the idea of using a smaller dedicated micro controller that could expose the DHT data.
I could have just waited on my order of BTE280 sensors, but I wanted to learn how to setup a basic i2c slave so reasons enough :).

In this repo I try to show how I combined all the information that I could find on the web and put it into one project (I just did the googling no credit goes to me).

All the code can be found in the src/main.cpp file.

## Being able to receive commands

One of the most important things was that the i2c slave should be able to get some commands from the master. This would make it possible for the master to select in my case humidity or temperature. You can also find an implementation for that in the code.

## TinyWireS limitations

The library that I found online that was well documented was the TinyWireS.
In comparison to the Arduino Wire library the TinyWireS lib is only able to write bytes. So all the results are converted to byte arrays that are sent to the master.

The master is then able to convert the bytes back to the original value (again in my case floats), I can make the master code available as well if anyone asks.

## Final note

Hope this helps some people in exposing sensors using an attiny(85) as a i2c slave. I added the main links that I used for setting up the code in the Credits.

Oh and of course the final result:

![alt text](/images/endresult.png "Title")


## Credits

Sending commands from the master to slave (and i2c in general) => http://www.gammon.com.au/i2c

TinyWireS library that I used => https://github.com/nadavmatalon/TinyWireS

DHT library => https://github.com/adafruit/DHT-sensor-library