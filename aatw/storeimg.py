import MySQLdb
import sys
import base64
import cStringIO
import getopt
import os

def main(argv):
   global guid
   global petname
   try:
      opts, args = getopt.getopt(argv,"hg:p:",["guid=","petname="])
   except getopt.GetoptError:
      print 'storeimg.py -g <guid> -p <petname>'
      sys.exit(2)
   for opt, arg in opts:
      if opt == '-h':
         print 'storeimg.py -g <guid> -p <petname>'
         sys.exit()
      elif opt in ("-g", "--guid"):
         guid = arg
      elif opt in ("-p", "--petname"):
         petname = arg

def read_image():
    
    try:
	fin = open("/home/pi/aatw/image.jpg")
    except (OSError, IOError) as e:
	print ('!')
	sys.exit(2)

    img = fin.read()
    
    return img

if __name__ == "__main__":
   main(sys.argv[1:])

db = MySQLdb.connect("localhost","root","SQLPw0rd","aatw")

#with open("/home/pi/aatw/image.jpg", "rb") as image_file:
#    encoded_string = base64.b64encode(image_file.read())

db = MySQLdb.connect("localhost","root","SQLPw0rd","aatw")

data = read_image()

sql = "INSERT INTO aatw_gallery (GUID, petname, ext, data) VALUES('"+guid+"','"+petname+"','jpeg',%s)"

datarg = (data, )
#print sql
cursor=db.cursor()
cursor.execute(sql,datarg)
db.commit()
cursor.close()
db.close()

delimg = "rm /home/pi/aatw/image.jpg"
os.system(delimg)
