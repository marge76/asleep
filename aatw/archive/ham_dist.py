import datetime
import serial
import sys

# Check for commandline argument. The first argument is the the name of the program.
if len(sys.argv) < 2:
  print "Usage: python %s [Out File]" % sys.argv[0]
  exit()

# Open the serial port we'll use the pins on and the file we'll write to.
ser = serial.Serial("/dev/ttyS1")

# Open the file we're going to write the results to.
f = open(sys.argv[1], 'a')

# Bring DTR to 1. This will be shorted to DSR when the switch is activated as the wheel turns.
ser.setDTR(1)

# The circumferance of the wheel.
circ = 0.000396 # miles
# Total distance traveled in this run of the program.
distance = 0.0

print "%s] Starting logging." % datetime.datetime.now()
start = datetime.datetime.now()

# This function a period of the wheel to a speed of the hamster.
def toSpeed(period):
  global circ
  seconds = period.days * 24 * 60 * 60 + period.seconds + period.microseconds / 1000000.
  return circ / (seconds / 60. / 60.)
  
# Waits for the DSR pin on the serial port to turn off. This indicates that the
# switch has turned off and the magnet is no longer over the switch.
def waitForPinOff():
  while ser.getDSR() == 1:
    1 # Don't do anything while we wait.

# Waits for the DSR pin on the serial port to turn on. This indicates that the
# switch has turned on and the magnet is current over the switch.
def waitForPinOn():
  while ser.getDSR() == 0:
    1 # Don't do anything while we wait.

# The main loop of the program.
while 1:
  waitForPinOn()
  
  # Calculate the speed.
  end = datetime.datetime.now()
  period = end - start
  start = end
  speed = toSpeed(period)
  # Increment the distance.
  distance = distance + circ
  
  waitForPinOff()
  
  # We'll calculate the time the switch was held on too so but this isn't too useful.
  hold = datetime.datetime.now() - start
 
  # If the switch bounces or the hamster doesn't make a full revolution then
  # it might seem like the hamster is running really fast. If the speed is
  # more than 4 mph then ignore it, because the hamster can't run that fast.
  if speed < 4.0:
    # Print out our speed and distance for this session.
    print "%s] Distance: %.4f miles Speed: %.2f mph" % (datetime.datetime.now(), distance, speed)
      
    # Log it to and flush the file so it actually gets written.
    f.write("%s\t%.2f\n" % (datetime.datetime.now().strftime("%D %T"), speed))
    f.flush()
    
