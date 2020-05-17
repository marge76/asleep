#!/usr/bin/env python
import pika
import os
connection = pika.BlockingConnection(pika.ConnectionParameters(host='localhost'))
channel = connection.channel()
channel.queue_declare(queue='aatw_pic')
print ' [*] Waiting for messages. To exit press CTRL+C'
def callback(ch, method, properties, body):
    #print " [x] Received %r" % (body,)
    myparams = body.split(",")
    os.system("fswebcam -r 640x480 /home/pi/aatw/image.jpg")
    storepic = "python /home/pi/aatw/storeimg.py -g "+myparams[0]+" -p "+myparams[1]
    print (storepic)
    os.system(storepic);
channel.basic_consume(callback,queue='aatw_pic',no_ack=True)
channel.start_consuming()
