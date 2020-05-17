#!/usr/bin/python
import pika
import RPi.GPIO as GPIO
import os
import MySQLdb
import datetime
import sys, getopt
import datetime
import math
from math import pi

uname = ""
petname = ""
diameter = float(21.243434) #diameter -> circumference calculated sonn, then converted to metres
startdist = float(0.0)

def SetCircumference(mydia,mycirc):
    mycirc = mydia * pi
    return mycirc

def main(argv):
   global uname
   global petname
   global diameter
   global startdist
   if len(sys.argv) < 3 :
 	print 'whlmon.py -u <username> -p <petname> -d <diameter in cm> -s <start_distance>'
	sys.exit()
   try:
      opts, args = getopt.getopt(argv,"hu:p:d:s:",["uname=","petname=","dia=","dist="])
   except getopt.GetoptError:
      print 'whlmon.py -u <username> -p <petname> -d <diameter in cm> -s <start_distance>'
      sys.exit(2)
   for opt, arg in opts:
      if opt == '-h':
         print 'whlmon.py -u <username> -p <petname> -d <diameter in cm> -s <start_distance>'
         sys.exit()
      elif opt in ("-u", "--uname"):
         uname = arg
      elif opt in ("-p", "--petname"):
         petname = arg
      elif opt in ("-d", "--dia"):
         diameter = float(arg)
      elif opt in ("-s", "--dist"):
	 startdist = float(arg)

   print 'uname is ', uname
   print 'petname is ', petname
   print 'diameter is ', diameter
   print 'startdist is ', startdist

if __name__ == "__main__":
   main(sys.argv[1:])

db = MySQLdb.connect("localhost","root","SQLPw0rd","aatw")
select = "SELECT GUID FROM login WHERE L1='" + uname + "'"
print select
cur = db.cursor()
cur.execute(select)
row = cur.fetchone()
print row[0]

GPIO.setmode(GPIO.BOARD)
GPIO.setwarnings(False)
GPIO.setup(3,GPIO.IN)

print "Ready"
start = datetime.datetime.now()
if startdist == 0.0:
   send = "insert into runlog VALUES ('"+row[0]+"', '" + petname + "','" + start.strftime("%Y-%m-%d") + "',0,0)"
   print send
   connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
   channel = connection.channel()
   channel.queue_declare(queue='aatw_gaian')
   channel.basic_publish(exchange='',routing_key='aatw_gaian',body=send);
   connection.close()

connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
channel = connection.channel()
channel.queue_declare(queue='aatw_gaian')
send = "insert into runshour VALUES ('"+row[0]+"', '" + petname + "','" + start.strftime("%Y-%m-%d") + "',"+start.strftime("%H")+",0,0)"
print send
channel.basic_publish(exchange='',routing_key='aatw_gaian',body=send)
connection.close()
print "Start-up message on queue"

circ = float(21.232)
circ = SetCircumference(diameter,circ) #calculate circumference in cm (dia * pi)
circ = circ / 100 #convert to metres
print 'circumference is ', circ

distance = startdist
hourdistance = 0.0
dayspeed = 0.0
hourspeed = 0.0

def toSpeed(circ, seconds, prevtpspd, dbtab):
        metrecirc = circ
	newtpspd = metrecirc / seconds
	if newtpspd < 4.0:
		if newtpspd > prevtpspd:
			speedstr = str(round(newtpspd, 4))
			if dbtab == "runlog":
				send = "update runlog SET TOPSPEED="+speedstr+" WHERE GUID='"+row[0]+"' and PETNAME='" + petname + "' and DAY='" + start.strftime("%Y-%m-%d") + "'"
			if dbtab == "runshour":
	                        send = "update runshour SET TOPSPEED="+speedstr+" WHERE GUID='"+row[0]+"' and PETNAME='" + petname + "' and DAY='" + start.strftime("%Y-%m-%d") + "' and HR="+start.strftime("%H")
			print send
			connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
			channel = connection.channel()
			channel.queue_declare(queue='aatw_gaian')
			channel.basic_publish(exchange='',routing_key='aatw_gaian',body=send)
			print "Posted new top speed to queue"
			connection.close()
			return newtpspd
		else:
			return prevtpspd
	else:
		return prevtpspd

def callback(ch, method, properties, body):
        connection2.close()
	GPIO.cleanup()
        print " [x] Received %r" % (body,)
	os.remove('/home/pi/aatw/running.lck')
        exit(0);

connection2 = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
channel2 = connection2.channel()
channel2.queue_declare(queue='aatw_stop')

file = open("/home/pi/aatw/running.lck", "w")
file.write("Locking file\n")
file.close()

phto = 0

while True:
	channel2.basic_consume(callback,queue='aatw_stop',no_ack=True)
	if GPIO.input(3) == False:
		end = datetime.datetime.now()
		period = end - start
                seconds = period.days * 24 * 60 * 60 + period.seconds + period.microseconds / 1000000.
		if end.hour != start.hour:
			send = "insert into runshour VALUES ('"+row[0]+"', '" + petname + "','" + end.strftime("%Y-%m-%d") + "',"+end.strftime("%H")+",0,0)"
			print send
			connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
                        channel = connection.channel()
                        channel.queue_declare(queue='aatw_gaian')
                        channel.basic_publish(exchange='',routing_key='aatw_gaian',body=send)
			if end.day != start.day:
				dayspeed=0.0
				distance=0.0
				phto=0
				send = "insert into runlog VALUES ('"+row[0]+"', '" + petname + "','" + end.strftime("%Y-%m-%d") + "',0,0)"
				channel.basic_publish(exchange='',routing_key='aatw_gaian',body=send);
                        connection.close()
			print "Sent - new day and reset distance"
			start = end
			hourspeed = 0.0
			hourdistance = 0.0;
                else:
			dayspeed = toSpeed(circ,seconds,dayspeed,"runlog")
			hourspeed = toSpeed(circ,seconds,hourspeed,"runshour")
			start = end
			if dayspeed < 4.0:
				distance = distance + circ
				hourdistance = hourdistance + circ
				print "%s] Distance: %.4f m Speed: %.2f m/s" % (datetime.datetime.now(), distance, dayspeed)
				connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
				channel = connection.channel()
				channel.queue_declare(queue='aatw_gaian')
	                        diststr = str(round(distance, 4))
				hrdiststr = str(round(hourdistance, 4))
        	                send = "update runlog SET DISTANCE="+diststr+" WHERE GUID='"+row[0]+"' and PETNAME='" + petname + "' and DAY='" + start.strftime("%Y-%m-%d") + "'"
				print send
				channel.basic_publish(exchange='',routing_key='aatw_gaian',body=send)
                                send = "update runshour SET DISTANCE="+hrdiststr+" WHERE GUID='"+row[0]+"' and PETNAME='" + petname + "' and DAY='" + start.strftime("%Y-%m-%d") + "' and HR="+start.strftime("%H")
                                channel.basic_publish(exchange='',routing_key='aatw_gaian',body=send)
				phto = phto + 1
				connection.close()
				if phto == 3:
					connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
	                                channel = connection.channel()
        	                        channel.queue_declare(queue='aatw_pic')
                                	send = row[0]+","+ petname
	                                print send
        	                        channel.basic_publish(exchange='',routing_key='aatw_pic',body=send)
                	                print "Sent for photo"
                        	        connection.close();
				print "Sent - Update distance";
	                else:
				print "Not Sent - Speed too fast";  
