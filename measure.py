#!/usr/bin/env python

import peewee
from peewee import *

# Change to reading sensor values
t = 20
l = 20
m = 20

db = MySQLDatabase(host='localhost', user='irrigation', passwd='galgsteidh63f=', db='irrigation')
db.connect()

class Measurement(peewee.Model):
      time = peewee.DateTimeField(constraints=[SQL('DEFAULT CURRENT_TIMESTAMP')])
      temperature = peewee.FloatField()
      light = peewee.FloatField()
      moisture = peewee.FloatField()

      class Meta:
            database = db

db.create_tables([Measurement], safe=True)
measure = Measurement(temperature = t, light = l, moisture = m)
measure.save()
db.close()