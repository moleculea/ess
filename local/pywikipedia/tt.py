# -*- coding: utf-8 -*-
import os,re
listFile = open ('filename2.txt', 'r')
def pageyield():
    r1 = listFile.read()
    print r1
    pattern = '#\[\[([^\]]+)]]'
    #replace = u'\1'
    p = re.compile( pattern )
    p1 = p.findall( r1 )
    for m in p1:
        yield m.decode('utf-8')
        
#for w in pageyield():
    #print w
listFile.close()


