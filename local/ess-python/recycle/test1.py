# -*- coding: utf-8 -*-
import os,locale,sys,urllib
command = u'/usr/bin/python /var/local/pywikipedia/replace.py "{{{" "{{{ " -savenew:/var/local/pywikipedia/infoboxlist.txt -always -lang:en -verbose'.encode('utf-8')
#s = u' -cat:信息框模板'.encode('utf-8')
#if isinstance(s, unicode):
    #print "Yes"
#locale.setlocale(locale.LC_ALL,'zh_CN.UTF-8')
#a = locale.getdefaultlocale()
#b = locale.getlocale()
#print a
#print b

#s1 = "\"" + "u\ue14f\u6f60\u4668\u216a\u7f67".encode('utf-8') + "\""
#s1 =""
locale.setlocale(locale.LC_ALL, 'zh_CN.UTF-8')
print sys.getfilesystemencoding()
print locale.CODESET
print locale.nl_langinfo(locale.CODESET)
print locale.getdefaultlocale()
print locale.getlocale()
#print u"\ue14f\u6f60\u4668\u216a\u7f67".encode('utf-8')
#unicode('ä¸*å›½', sys.getfilesystemencoding()) 
#os.system(command+s1)
print urllib.unquote("%E4%BF%A1%E6%81%AF%E6%A1%86%E6%A8%A1%E6%9D%BF")