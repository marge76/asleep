#!/usr/bin/env python2.7  
# tweet.py by Alex Eames http://raspi.tv/?p=5908    
import tweepy  
import sys  
    
# Consumer keys and access tokens, used for OAuth  
consumer_key = 'JEDr7LZ73orALfyuLHi3TEmJu'  
consumer_secret = 'HYrAHqrgTWpxjvUzjzsX8z9ETcXCwTU11Abqk6wcU4JLyaqGf7'  
access_token = '2877989129-vO2y4EQLveLbMVozScrir0Mgxy244EZpPZfQcUp'  
access_token_secret = 'TGeBbtZt5KBkXlHd7D11t0cJXwUQ8Qg73iE36jh2dLSmS'
      
# OAuth process, using the keys and tokens  
auth = tweepy.OAuthHandler(consumer_key, consumer_secret)  
auth.set_access_token(access_token, access_token_secret)  
       
# Creation of the actual interface, using authentication  
api = tweepy.API(auth)  
      
if len(sys.argv) >= 2:  
    tweet_text = sys.argv[1]  
      
else:
    tweet_text = "Join the World Rodent Race today! http://bit.ly/1BvSiD8 #hamster #wheel #race"  
      
if len(tweet_text) <= 140:  
    api.update_status(tweet_text)  
else:  
    print "tweet not sent. Too long. 140 chars Max."  #!/usr/bin/env python2.7