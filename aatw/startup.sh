tightvncserver

vncserver :0 -geometry 1024x768 -depth 24
vncserver :1 -geometry 1024x768 -depth 24

cd /home/pi/GaianDB
nohup sudo ./launchGaianServer.sh &

echo "wait 3 minutes - then run startup2.sh"



