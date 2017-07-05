#!/usr/bin/env python


from picamera import PiCamera

camera = PiCamera()
camera.resolution = (640, 480)
camera.capture('/home/pi/cam.jpg')
