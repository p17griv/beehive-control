#include <Wire.h>

#define LED 13
#define SWITCH 12
#define TLED 11

#define SENDFREQ 500

void setup()
{
  pinMode(LED, OUTPUT);
  pinMode(TLED, OUTPUT);
  pinMode(SWITCH, INPUT);
  
  Wire.begin(); // Join i2c bus
  
  Serial.begin(9600);
}

void receiveEvent(){
  
}

void loop()
{
  // If switch is on
  if(digitalRead(SWITCH)){
    Serial.print("fc23e,");
    for(int beehive=1;beehive<=4;beehive++){
      digitalWrite(LED, HIGH); // Turn on the LED indicatior

      Wire.requestFrom(beehive, 15); // Request 15 bytes from current beehive

      while(Wire.available()) { // Slave may send less than requested
        char c = Wire.read(); // Receive a byte as character
        if(c != EOF)
          Serial.print(c);
      }
      Serial.print(',');
    }
    // Blink transmission indicator
    digitalWrite(TLED, HIGH);
    delay(SENDFREQ/2);
    digitalWrite(TLED, LOW);
    delay(SENDFREQ/2);
    
    Serial.println(' ');
  }
  else
    digitalWrite(LED, LOW);
}