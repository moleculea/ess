#!/usr/bin/python
# -*- coding: utf-8  -*-
#execfile('replace.py "(\|\s*成员数\s*)=(.+)(\s*(\}\}|\|.+=))" "\1=300\3" -page:"%E5%8C%97%E4%BA%AC%E7%A7%91%E6%8A%80%E5%A4%A7%E5%AD%A6%E5%AD%A6%E7%94%9F%E7%A7%91%E5%AD%A6%E6%8A%80%E6%9C%AF%E5%8D%8F%E4%BC%9A" -regex ')

#execfile('replace.py')

import sys  
import chardet
import codecs
import os
import urllib
import wikipedia
def main():  
        print 'main' 
        #for a in sys.argv:
            #print a.decode('utf-8')
            #print chardet.detect(a)
            #print a
            #f = codecs.open("/home/anshichao/replace_arg.txt","a","utf-8")
            #f.write(a)
            #f.close()
        
        sys.argv[0] =""
        arg = sys.argv[1]
        args = arg.split(";;")
        parameter = args[0]
        
        print "paramter:",parameter
        
        #im = "s=u'%s'"%parameter
        #print im
        #exec(im)
        print "ENCODED:",urllib.unquote("%E6%88%90%E5%91%98%E6%95%B0")
        #print s.encode('utf-8')
        #parameter = s
        #print im 
        
        value = args[1]
        pg_name = args[2]
        
        #parameter = urllib.unquote(parameter)
        
        pattern = "\"(\|\s*%s\s*)=(.+)(\s*(\}\}|\|.+=))\" \"\\1=%s\\3\""%(parameter,value)
        print "pattern:",pattern
        #a = urllib.quote(pattern);
        #print "QUOTE", a
        #print "UNQUOTE", urllib.unquote(a)
        #cmd = "python /var/local/pywikipedia/replace.py %s -regex -page:\"%s\" -always "%(pattern,pg_name)
        cmd = "python /var/local/pywikipedia/replace.py %s -regex -page:\"%s\" -always "%(pattern,pg_name)
        print cmd
        #f = file("/home/anshichao/repl_cron","w")
        #f.write(cmd)
        #f.close()
        #print cmd
        #os.system(cmd)

        
if __name__ == '__main__':  
        main()  