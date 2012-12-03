# -*- coding: utf-8 -*-
"""
Python scripts of initial page indexation for USTB-ESS

"""
from xml.dom.minidom import parse # for XMLmakeList
from xml.dom.minidom import getDOMImplementation # for WikiToXML
import os, random, re

"""
XMLmakeList
This class uses minidom to parse a local XML file into a list, each element of which is a three-element( [ text, tagName, typeName ] )
"""
class XMLmakeList:
    def __init__(self, fileName):
        self.dom = parse(fileName)
    
    def getText(self, nodelist):
        rc = []
        for node in nodelist:
            if node.nodeType == node.TEXT_NODE:
                rc.append(node.data)
        return ''.join(rc)

    def makeList(self):
        parameters = []
        for node in self.dom.documentElement.childNodes:
            if node.nodeType == node.ELEMENT_NODE:
                parameter = [self.getText(node.childNodes), node.nodeName, node.getAttribute('type') ]
                parameters.append(parameter)
        return parameters

          
"""
WikiToXML
"""
class WikiToXML:
    def __init__(self, pageText, pageTitle, xmlFilename):
        object = XMLmakeList(xmlFilename) # initialize filter parameters
        parameters = object.makeList()
        self.parameters = parameters       
        self.pageText = pageText
        self.pageTitle = pageTitle
    def wikitoxml(self):  
        impl = getDOMImplementation()
        newdoc = impl.createDocument(None, "infobox", None)
        infobox = newdoc.documentElement
        paralist = []
        for parameter in self.parameters:
            #pattern = '\|%s=([^\|]+)' %parameter[0]
            pattern = '\|%s=(.*)\s*\|' %parameter[0]
            pat = re.compile( pattern ) 
            p_value = pat.findall( self.pageText )
            if len(p_value) > 0: # parameter has empty value: "|parameter1=|parameter2="
                if len(p_value[0]) == 0:
                    tagName = newdoc.createElement(parameter[1])
                    tagName.setAttribute('type',parameter[2])
                    tagText = newdoc.createTextNode('')
                    tagName.appendChild( tagText )
                    paragp = [parameter[1],'']
                    paralist.append(paragp)
                else:
                    tagName = newdoc.createElement(parameter[1])
                    tagName.setAttribute('type',parameter[2])
                    paravalue = p_value[0].strip()
                    tagText = newdoc.createTextNode( paravalue )
                    tagName.appendChild( tagText )
                    paragp = [parameter[1], paravalue]
                    paralist.append(paragp)
                infobox.appendChild( tagName )
            elif len(p_value) == 0: # parameter does not exist in page
                # Print warnings about inexistence of parameters in page (must encode to utf-8 on Ubuntu)
                print 'Parameter: ' + parameter[0].encode('utf-8') + ' does not exist in page:' + self.pageTitle.encode('utf-8') + '.'
                tagName = newdoc.createElement(parameter[1])
                tagName.setAttribute('type',parameter[2])
                tagText = newdoc.createTextNode('')
                tagName.appendChild( tagText )
                infobox.appendChild( tagName )
                paragp = [parameter[1],'']
                paralist.append(paragp)
        #print paralist
        return newdoc,paralist # paralist is a list to be imported directly to MySQL
        
"""
PageTitle
Yield single page titles captured from local file of page lists
"""        
class PageTitle:
    def __init__(self, listFilename):
        self.listFilename = listFilename
        listFile = open(listFilename, 'r')
        self.listRead = listFile.read()
        listFile.close()
        self.pattern = '#\[\[([^\]]+)]]'        
    def pageyield(self):
        pt = re.compile( self.pattern )
        pgls = pt.findall( self.listRead )
        for pageTitle in pgls:
            yield pageTitle.decode('utf-8')
        
        
"""
HashDirectory
16*16 two-layer hash directories
"""          
class HashDirectory:
    def __init__(self,basedir):
        self.basedir = basedir # set the base directory
        self.subdir1 = str(hex(random.randint(0,16)))[2:]
        self.subdir2 = str(hex(random.randint(0,16)))[2:]
        
    def  hashdir(self):
        hashpath = self.basedir + '/' + self.subdir1 + '/' + self.subdir2 + '/'    
        if not os.path.isdir( hashpath ):
            os.makedirs( hashpath )
        return hashpath
