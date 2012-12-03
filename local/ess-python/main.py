# -*- coding: utf-8 -*-
"""
Execution entry for initial page indexation
"""
# Import Python modules
import sys, codecs, time, re
import xml.dom.minidom
import getopt
from importDTPM import * # importDTPM() referenced in indexing()

# Specify pywikipedia directory
pywdir = "/var/local/pywikipedia" 

# Specify base hash directory (Web accessible)
baseHashdir = '/var/www/ess/indexed'

# Expand sys.path to include pywikipedia directory
sys.path.append(pywdir) 
# Import ESS modules
import pageindex
import capture
import wikipedia

"""
save():
Save V file at a hashed directory
"""
def save(xmlDoc, pageTitle):
    filename = pageTitle.encode('utf-8')
    fileHashDir = pageindex.HashDirectory(baseHashdir) # Hash directory
    hashpath = fileHashDir.hashdir()
    print hashpath
    f = file( hashpath + filename + '.xml',"w" )
    writer = codecs.lookup('utf-8')[3](f)
    xmlDoc.writexml( writer, encoding='utf-8')
    writer.close()
    f.close()
    return hashpath
"""
indexing():
Index a page fetched from listFile at a single time
Automatically avoid termination of indexing due to exception
"""
def indexing(filter, pFile):  
    # Get listFile name from pFile, difference of them being only in extension name
    listFile = re.sub('\.xml','.txt',pFile)
    
    # Get listFile name from pFile
    indexname = re.findall(r'([^/]+).xml',pFile)
    indexname = indexname[0]
    
    # Fetch a page title from listFile one at a time
    pt = pageindex.PageTitle(listFile)
    
    # cat is for fetching distinct categories for pages within this index
    cat = []
    for pageElement in capture.capture(pt, filter):
        try:
            pageText = pageElement[0]
            pageTitle = pageElement[1]
            wiki = pageindex.WikiToXML(pageText, pageTitle, pFile, cat)
            xmlDoc,paralist,cat = wiki.wikitoxml()
            print xmlDoc.toxml().encode('utf-8') # Print xmlDoc to XML format output
            #print paralist
            
            # Save V file into hashed directory
            hashpath = save(xmlDoc, pageTitle)
            # Import DTPM parameters (paralist) into MySQL
            page = wikipedia.Page(None,title=pageTitle)
            importDTPM(paralist, indexname, hashpath, pageTitle, page)

        except (Exception), e: # Avoid cessation of the indexing sequence
            #print e
            #raise
            print '\nIndexation continues.\n'
            continue
        
    # Save distinct category field
    indexdir = re.sub('%s.xml'%(indexname),'',pFile)
    catInfo = ";"
    catInfo += ";".join(cat)
    catInfo += ";"
    catFile = indexdir + "cat.txt"
    f = open(catFile, "w")
    f.write(catInfo.encode('utf-8'))
    f.close()
"""
indexLog() exports metadata (e.g. execution time) of index to an external file
"""  
def indexLog(startp, endp,pFile):
    indexname = re.findall(r'([^/]+).xml',pFile)
    indexname = indexname[0]
    indexdir = re.sub('%s.xml'%(indexname),'',pFile)
    logFile = indexdir + "log.txt"
    f = open(logFile,"a")
    timeused = round((endp - startp),2)
    timeused = str(timeused) + " seconds"
    timestamp = time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(time.time()))
    print logFile
    logInfo = """
####
#[INDEX_NAME]:%s
#[INDEX_TIMESTAMP]:%s
#[TIME_USED]:%s

"""%(indexname,timestamp,timeused)
    f.write(logInfo)
    f.close()
"""
main():
Main function where the process starts
Automatically calculate the execution time for the whole indexation process
Arguments are:
 -p pFile
 -f
 
"""  
def main():
    try:
        opts, args = getopt.getopt(sys.argv[1:], "fp:",["filter","pfile"])
    except getopt.error, msg:
        print msg
        print "for help use --help"
        sys.exit(2)
    filter = False
    pfile = ""
    for o, a in opts:
        if o in ("-f", "--filter"):
            filter = True
        if o in ("-p", "--pfile"):
            pFile = a
    startp = time.time() # start time
    indexing(filter, pFile)
    endp = time.time() # end time
    indexLog(startp, endp, pFile)
    #print 'time used:' + str(endp - startp) # run time calculation
        
if __name__ == "__main__":
    main()
