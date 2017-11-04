#!/usr/bin/env python

import RPi.GPIO as GPIO
import time
import sys

GPIO.setwarnings(False)

GPIO.setmode(GPIO.BCM)

channel = 25
GPIO.setup(channel, GPIO.OUT, initial=GPIO.LOW)

#res = raw_input('Enter desired water volume: ')
res = str(sys.argv[1])

delay = 0.4
timeout = float(res)/(0.68/5) + delay

GPIO.output(channel, GPIO.HIGH)

time.sleep(timeout)

GPIO.output(channel, GPIO.LOW)
