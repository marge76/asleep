#!/usr/bin/env python
import pika
import os
connection = pika.BlockingConnection(pika.ConnectionParameters(host='localhost'))
channel = connection.channel()
channel.queue_declare(queue='aatw_start')
print ' [*] Waiting for messages. To exit press CTRL+C'
def callback(ch, method, properties, body):
    #print " [x] Received %r" % (body,)
    myparams = body.split(",")
    monstart = "python /home/pi/aatw/whlmon.py -u "+myparams[0]+" -p "+myparams[1]+" -d "+myparams[2]+" -s "+myparams[3]
    print (monstart)
    os.system(monstart);
channel.basic_consume(callback,queue='aatw_start',no_ack=True)
channel.start_consuming()
