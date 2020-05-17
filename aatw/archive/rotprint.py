import time
import RPi.GPIO as GPIO
import os

GPIO.setmode(GPIO.BOARD)

GPIO.setwarnings(False)

GPIO.setup(3,GPIO.IN)

while True:
	if GPIO.input(3) == False:
		print 'detected'
		time.sleep(1);

