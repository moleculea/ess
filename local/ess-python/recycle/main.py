# -*- coding: utf-8 -*-
"""
Execution entry for initial page indexation
"""
import sys, pageindex, codecs, time, re
import xml.dom.minidom
pywdir = "/var/local/pywikipedia" # specify pywikipedia directory
sys.path.append( pywdir ) # expand sys.path to include pywikipedia directory
#print sys.path
import capture
# set variables list
# set bas hash directory (no slash at the end)
baseHashdir = "/var/www/ess/indexed"
# specify txt file that contains page list
listFilename = "/var/local/pywikipedia/filename3.txt"
# specify xml file that contains parameter filter
xmlFilename = "/var/local/ess-python/test1.xml"
pt = pageindex.PageTitle(listFilename)

def save(xmlDoc, pageTitle):
    filename = pageTitle.encode('utf-8')
    fileHashDir = pageindex.HashDirectory( baseHashdir ) # Hash directory
    hashpath = fileHashDir.hashdir()
    f = file( hashpath + filename + '.xml',"w" )
    writer = codecs.lookup('utf-8')[3](f)
    xmlDoc.writexml( writer, encoding='utf-8')
    writer.close()
    f.close()

def indexing():
    for pageElement in capture.capture(pt):
        try:
            pageText = pageElement[0]
            pageTitle = pageElement[1]
            wiki = pageindex.WikiToXML(pageText, pageTitle, xmlFilename)
            xmlDoc,paralist = wiki.wikitoxml()
            print xmlDoc.toxml().encode('utf-8') # print xmlDoc to XML format output, encode to utf-8 on Ubuntu
            #print paralist
            
            save(xmlDoc, pageTitle) 
        except Exception, e: # avoid cessation of the indexing sequence
            #print e
            print '\nIndexation continues.\n'
            continue
            
def main():
    startp = time.time() # start time
    indexing()
    endp = time.time() # end time
    print 'time used:' + str(endp - startp) # run time calculation
        
if __name__ == "__main__":
    main()
