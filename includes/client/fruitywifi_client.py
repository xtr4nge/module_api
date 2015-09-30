#!/usr/bin/python

import os, sys, getopt
import urllib2
import json
import requests
from requests import session

requests.packages.urllib3.disable_warnings() # DISABLE SSL CHECK WARNINGS

gVersion = "1.0"
server = "https://127.0.0.1:8443";
token = "e5dab9a69988dd65e578041416773149ea57a054"

def usage():
    print "\nFruityWiFi API " + gVersion + " by @xtr4nge"
    
    print "Usage: ./client <options>\n"
    print "Options:"
    print "-x <command>, --execute=<commnd>      exec the command passed as parameter."
    print "-t <token>,   --token=<token>         authentication token."
    print "-s <server>,  --server=<server>       FruityWiFi server [http{s}://ip:port]."
    print "-h                                    Print this help message."
    print ""
    print "FruityWiFi: http://www.fruitywifi.com"
    print ""

def parseOptions(argv):
    
    v_execute = ""
    v_token = token
    v_server = server
    
    try:                                
        opts, args = getopt.getopt(argv, "hx:t:s:", 
                                   ["help","execute=","token=","server="])
        
        for opt, arg in opts:
            if opt in ("-h", "--help"):
                usage()
                sys.exit()
            elif opt in ("-x", "--execute"):
                v_execute = arg
            elif opt in ("-t", "--token"):
                v_token = arg
            elif opt in ("-s", "--server"):
                v_server = arg
                
        return (v_execute, v_token, v_server)
                    
    except getopt.GetoptError:
        usage()
        sys.exit(2)

(execute, token, server) = parseOptions(sys.argv[1:])

class webclient:

    def __init__(self, server, token):
        
        self.global_webserver = server
        self.path = "/modules/api/includes/ws_action.php"
        self.s = requests.session()
        self.token = token

    def login(self):

        payload = {
            'action': 'login',
            'token': self.token
        }
        
        self.s = requests.session()
        self.s.get(self.global_webserver, verify=False) # DISABLE SSL CHECK
        self.s.post(self.global_webserver + '/login.php', data=payload)

    def loginCheck(self):
                
        response = self.s.get(self.global_webserver + '/login_check.php')
        #print response.headers
        #print ":" + response.text
        
        if response.text != "":
            self.login()
        
        if response.text != "":
            print "Ah, Ah, Ah! You didn't say the magic word!"
            sys.exit()
        
        return True
        
    def submitPost(self, data):
        response = self.s.post(self.global_webserver + data)
        #print response.headers
        #print "debug: " + response.text
        return response.json
    
        if response.text == "":
            return True
        else:
            return False
    
    def submitGet(self, data):
        response = self.s.get(self.global_webserver + self.path + "?" + data)
        #print response.headers
        #print "debug: " + response.text
        #print response.json

        return response
        
try: 
    w = webclient(server, token)
    w.login()
    w.loginCheck()
    
    if execute != "":
        out =  w.submitGet("api=" + str(execute))
        print out.json()
    
except requests.exceptions.ConnectionError:
    print "check server connection [http{s}://ip:port]"
except SystemExit:
    print "Bye."
except:
    pass
    print "damn error: " + str(sys.exc_info()[0])
