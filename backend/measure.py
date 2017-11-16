#!/usr/bin/env python

from irrigation import irrigate
from datetime import timedelta,datetime
from peewee import *
from smbus import SMBus
from ADCPi import ADCPi
from picamera import PiCamera
import RPi.GPIO as GPIO
import time
import math
import sys
import numpy as np

dbase = MySQLDatabase('irrigation', host='localhost', user='irrigation', passwd='')
dbase.connect()

class Settings(peewee.Model):
      imgcrop_x = peewee.FloatField()
      imgcrop_y = peewee.FloatField()
      imgcrop_w = peewee.FloatField()
      imgcrop_h = peewee.FloatField()
      volume = peewee.FloatField()
      trigger_m = peewee.FloatField()
      trigger_t = peewee.FloatField()
 
      class Meta:
            database = dbase


class Irrigation(peewee.Model):
      time = peewee.DateTimeField(default=peewee.datetime.datetime.now)
      volume = peewee.FloatField()

      class Meta:
            database = dbase

# For measuring chlorophyll using camera
x = Settings.get().imgcrop_x
y = Settings.get().imgcrop_y 
w = Settings.get().imgcrop_w 
h = Settings.get().imgcrop_h 

# For automatic irrigation by moisture level detection
trigger_m = Settings.get().trigger_m
trigger_t = Settings.get().trigger_t
volume = Settings.get().volume

last_irrigation = Irrigation.select().order_by(Irrigation.id.desc()).get().time

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)

channel = 24
GPIO.setup(channel, GPIO.OUT, initial=GPIO.LOW)


GPIO.output(channel, GPIO.HIGH)

time.sleep(4)

adc = ADCPi(0x68, 0x69, 18)
adc.set_conversion_mode(0)
t = (adc.read_voltage(2)-0.5)*100
l = adc.read_voltage(1)*100
m = adc.read_voltage(4)*4000-40

GPIO.output(channel, GPIO.LOW)

width = 640
height = 480
y1 = math.floor(height*y)
y2 = math.floor(height*y) + math.floor(height*h)
x1 = math.floor(width*x)
x2 = math.floor(width*x) + math.floor(width*w)
camera = PiCamera()
camera.resolution = (width, height)
output = np.empty((height*width*3), dtype=np.uint8)
camera.capture(output, 'rgb')
output = output.reshape((height, width, 3))
c = np.average(output[y1:y2,x1:x2,:])/10

class Measurement(peewee.Model):
      time = peewee.DateTimeField(default=peewee.datetime.datetime.now)
      temperature = peewee.FloatField()
      light = peewee.FloatField()
      moisture = peewee.FloatField()
      chlorophyll = peewee.FloatField()

      class Meta:
            database = dbase

dbase.create_tables([Measurement], safe=True)
measure = Measurement(temperature = t, light = l, moisture = m, chlorophyll = c)
measure.save()
dbase.close()


if (m < trigger_m and datetime.now() > (last_irrigation + timedelta(hours=trigger_t))):
      irrigate(volume)
