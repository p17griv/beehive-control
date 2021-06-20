#include <Wire.h>

#define TEMP 0
#define WEIGHT 1

const float tareWeight = 1.5; // The weight of an empty beehive 

int fsrReading; // The analog reading from the FSR resistor divider
int fsrVoltage; // The analog reading converted to voltage
unsigned long fsrResistance; // The voltage converted to resistance
unsigned long fsrConductance; 
long fsrForce; // The resistance converted to force
float temperatureC, tempVoltage, grossWeight, netWeight;

void setup()
{
  Wire.begin(1); // Join i2c bus with address #1
  Wire.onRequest(requestEvent); // Register event
}

void loop()
{
  delay(100);
}

// Function that executes whenever data is requested by master
void requestEvent() {
  // Getting the voltage reading from the temperature sensor
  int reading = analogRead(TEMP);
 
  // Converting that reading to voltage
  tempVoltage = reading * 5.0;
  tempVoltage /= 1024.0; 

  // Converting voltage to temperature (C)
  // 10 mv per degree with 500 mV offset
  temperatureC = (tempVoltage - 0.5) * 100;
  
  // https://lastminuteengineers.com/fsr-arduino-tutorial/
  // Getting the voltage reading from the force sensor
  fsrReading = analogRead(WEIGHT);
  
  // Analog voltage reading ranges from about 0 to 1023 which maps to 0V to 5V (= 5000mV)
  fsrVoltage = map(fsrReading, 0, 1023, 0, 5000);
  
  if (fsrVoltage != 0){
    // The voltage = Vcc * R / (R + FSR) where R = 10K and Vcc = 5V
    // => FSR = ((Vcc - V) * R) / V
    fsrResistance = 5000 - fsrVoltage; // fsrVoltage is in millivolts so 5V = 5000mV
    fsrResistance *= 10000; // 10K resistor
    fsrResistance /= fsrVoltage;
 
    fsrConductance = 1000000; // Measure in micromhos 
    fsrConductance /= fsrResistance;
 
    // Use the two FSR guide graphs to approximate the force
    if (fsrConductance <= 1000) {
      fsrForce = fsrConductance / 80;
    } else {
      fsrForce = fsrConductance - 1000;
      fsrForce /= 30;
    }
    
    // Converting Newton to Kgf (mult 10 for realistic values)
    grossWeight = fsrForce * 0.1019716213 * 10;
    netWeight = grossWeight - tareWeight;
    if(netWeight<0)
      netWeight = 0.0;
  }
  
  String stringOne = String(temperatureC, 1);
  String stringTwo = String(netWeight, 1);
  String message = String("1:" + stringOne + ":" + stringTwo);
  char buf[15];
  message.toCharArray(buf, 15);
  Wire.write(buf);
}