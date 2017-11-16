#!/usr/bin/env python

import RPi.GPIO as GPIO
import time
import sys
import peewee
from peewee import *


def irrigate(volume):
	GPIO.setwarnings(False)

	GPIO.setmode(GPIO.BCM)

	channel = 25
	GPIO.setup(channel, GPIO.OUT, initial=GPIO.LOW)

	delay = 0.4
	timeout = float(volume)/(0.68/5) + delay

	GPIO.output(channel, GPIO.HIGH)

	time.sleep(timeout)

	GPIO.output(channel, GPIO.LOW)


	dbase = MySQLDatabase('irrigation', host='localhost', user='irrigation', passwd='galgsteidh63f=')
	dbase.connect()

	class Irrigation(peewee.Model):
      		time = peewee.DateTimeField(default=peewee.datetime.datetime.now)
      		volume = peewee.FloatField()

      		class Meta:
            		database = dbase

	dbase.create_tables([Irrigation], safe=True)
	measure = Irrigation(volume = float(volume))
	measure.save()
	dbase.close()




def main():
	res = str(sys.argv[1])
	irrigate(res)

if (__name__ == "__main__"):
	main()











