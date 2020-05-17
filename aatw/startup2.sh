cd /home/pi/GaianDB/lib
sudo cp derby.jar /usr/lib/jvm/java-6-openjdk-armhf/jre/lib/ext
sudo cp derbyclient.jar /usr/lib/jvm/java-6-openjdk-armhf/jre/lib/ext
sudo cp db2jcutdown.jar /usr/lib/jvm/java-6-openjdk-armhf/jre/lib/ext

cd /home/pi/PJBS/build
nohup java -cp "/home/pi/PJBS:/home/pi/PJBS/dist/PJBS.jar" pjbs.Server &

cd /home/pi/aatw
nohup java -cp ".:commons-io-1.2.jar:commons-cli-1.1.jar:rabbitmq-client.jar" Recvtogdb &

echo "now start 'sudo python rab_send.py -u <member> -p <petname> -d <diameter> -s <start_distance>'"
echo "or  'sudo python monitorq.py'"

echo "plus run 'sudo python picq.py'"


