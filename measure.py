#!/usr/bin/env python

import peewee
from peewee import *
from smbus import SMBus
from ADCPi import ADCPi

adc = ADCPi(0x68, 0x69, 18)
adc.set_conversion_mode(0)
t = (adc.read_voltage(2)-0.5)*100
l = adc.read_voltage(1)*100
m = adc.read_voltage(3)*100
print 'Temp: '
print t
print 'Light: '
print l

dbase = MySQLDatabase('irrigation', host='localhost', user='irrigation', passwd='galgsteidh63f=')
dbase.connect()

class Measurement(peewee.Model):
      time = peewee.DateTimeField(default=peewee.datetime.datetime.now)
      temperature = peewee.FloatField()
      light = peewee.FloatField()
      moisture = peewee.FloatField()

      class Meta:
            database = dbase

dbase.create_tables([Measurement], safe=True)
measure = Measurement(temperature = t, light = l, moisture = m)
measure.save()
dbase.close()
