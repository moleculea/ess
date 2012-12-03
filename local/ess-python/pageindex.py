# -*- coding: utf-8 -*-
"""
Python scripts of initial page indexation for USTB-ESS

"""
from xml.dom.minidom import parse # for XMLmakeList
from xml.dom.minidom import getDOMImplementation # for WikiToXML
import os, random, re
import dtpm.cdate as cdate
import dtpm.wtext as wtext
import dtpm.cmt as cmt
import dtpm.unit as unit
import dtpm.img as img

# Destination host name
destHost = "wiki.ibeike.com" 

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
                parameter = [self.getText(node.childNodes), node.nodeName, node.getAttribute('type'), node.getAttribute('digit') ]
                parameters.append(parameter)
        return parameters

          
"""
WikiToXML
"""
class WikiToXML:
    def __init__(self, pageText, pageTitle, xmlFilename, cat):
        object = XMLmakeList(xmlFilename) # initialize DTPM P file
        parameters = object.makeList()
        self.parameters = parameters       
        self.pageText = pageText
        self.pageTitle = pageTitle
        self.cat = cat
    def wikitoxml(self):  
        impl = getDOMImplementation()
        newdoc = impl.createDocument(None, "values", None) # infobox => values
        infobox = newdoc.documentElement
        paralist = []
        cat = self.cat
        for parameter in self.parameters:
            if parameter[2] != "CAT": # Common parameter manipulation (except "CAT")
                #pattern = '\|%s=([^\|]+)' %parameter[0]            
                pattern = '\|%s=(.*)\s*\|' %parameter[0] # parameter[0] is parameter (DTPM)
                pat = re.compile( pattern ) 
                p_value = pat.findall( self.pageText )
                if len(p_value) > 0: 
                    if len(p_value[0]) == 0: # parameter has empty value: "|parameter1=|parameter2="
                        tagName = newdoc.createElement(parameter[1])
                        tagName.setAttribute('type',parameter[2])
                        if parameter[3]: # if digit is set
                            tagName.setAttribute('digit',parameter[3])
                        tagText = newdoc.createTextNode('')
                        tagName.appendChild( tagText )
                        paragp = [parameter[1],'']
                        paralist.append(paragp)
                    else:
                        value = p_value[0].strip() # strip() removes whitespaces
                        if parameter[3]: # if digit is set
                            digit = parameter[3]
                            data = DTPM(parameter[2],value,digit) # use class DTPM to process values
                        else:
                            data = DTPM(parameter[2],value)
                        paravalue = data.proc() # pass processed value to paravalue
                        tagName = newdoc.createElement(parameter[1]) # parameter[1]: DTPM field name
                        tagName.setAttribute('type',parameter[2]) # parameter[2]: DTPM type
                        if parameter[3]:
                            tagName.setAttribute('digit',parameter[3])
                        #print paravalue
                        tagText = newdoc.createTextNode( paravalue ) # paravalue: value (DTPM)
                        tagName.appendChild( tagText )
                        paragp = [parameter[1], paravalue]
                        paralist.append(paragp)
                    #infobox.appendChild( tagName )
                elif len(p_value) == 0: # parameter does not exist in page
                    print 'Parameter: ' + parameter[0].encode('utf-8') + ' does not exist in page:' + self.pageTitle.encode('utf-8') + '.'
                    tagName = newdoc.createElement(parameter[1])
                    tagName.setAttribute('type',parameter[2])
                    if parameter[3]:
                        tagName.setAttribute('digit',parameter[3])              
                    tagText = newdoc.createTextNode('')
                    tagName.appendChild( tagText )
                    paragp = [parameter[1],'']
                    paralist.append(paragp)
            else: # CAT manipulation
                catPattern = u'\[\[(Category:|分类:)([^\]]+)\]\]' # category pattern
                catpat = re.compile( catPattern ) 
                c_value = catpat.findall( self.pageText )
                if len(c_value) > 0: # if there is [[Category: ]] in the page
                    tagName = newdoc.createElement(parameter[1]) # parameter[1]: DTPM field name
                    catText = ";"
                    
                    #print c_value
                    for each_cat in c_value:
                        catText += each_cat[1] + ";" # each_cat: each Category:CATEGORY_NAME; each_cat[1] is CATEGORY_NAME
                        # Fetch distinct CAT field
                        if each_cat[1] not in cat:
                            cat.append(each_cat[1])
                    #print catText
                    tagName.setAttribute('type',parameter[2]) # parameter[2]: DTPM type (CAT)
                    tagText = newdoc.createTextNode( catText )
                    tagName.appendChild( tagText )
                    paragp = [parameter[1],catText]
                    paralist.append(paragp)

                else: # if not, create empty text node
                    tagName = newdoc.createElement(parameter[1])
                    tagName.setAttribute('type',parameter[2]) # parameter[2]: DTPM type (CAT)
                    tagText = newdoc.createTextNode('')
                    tagName.appendChild( tagText )
                    paragp = [parameter[1],'']
            infobox.appendChild( tagName )    
        #print paralist
        return newdoc, paralist,cat # paralist is a list to be imported directly to MySQL
        
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
        self.subdir1 = str(hex(random.randint(0,15)))[2:]
        self.subdir2 = str(hex(random.randint(0,15)))[2:]
        
    def  hashdir(self):
        hashpath = self.basedir +'/' + self.subdir1 + '/' + self.subdir2 + '/'    
        if not os.path.isdir( hashpath ):
            os.makedirs( hashpath )
        return hashpath
"""
DTPM
DTPM engine for value manipulation, along with WikiToXML
"""     
class DTPM:
    def __init__(self,type,value,digit = ""):
        self.DTPM_type = ('NAME','DATE','WTEXT','NUM','UNIT','CMT','IMG','CAT') 
        # initialize DTPM types into a tuple
        # 0 => NAME
        # 1 => DATE
        # 2 => WTEXT
        # 3 => NUM
        # 4 => UNIT
        # 5 => CMT
        # 6 => IMG
        # 7 => CAT
        self.type = type
        self.value = value
        self.digit = digit
        
    # avoidMultiple() avoids a parameter mistakenly retrieved multiple values 
    # (e.g. 'value1|parameter2=[[value2]]|...')
    def avoidMultiple(self,value):
        value = self.value.strip()
        # If "|" exists in value
        if re.search(u"\|",value):
            
            # If "[" or "]" exists in value
            if re.search(u"[\[\]\{\}]",value):
                #print value
                if value.startswith("|"):
                    # Remove starting "|"s
                    value = re.sub(u"^\|+","",value)
                if value.endswith("|"):
                    # Remove starting "|"s
                    value = re.sub(u"\|+$","",value)                
                # For '[[value|abc]] and {{value|abc}}' scenario
                # e.g. '[[File:Image.jpg|200px]]'
                # if not [[value1]]|[[value1]]
                #if not re.search(u"[\]\}]+[^\]\}\[\{]+\|+[^\]\}\[\{]+[\[\{]+",value):
                    #return value
                # If "=" exists
                if re.search("=",value):
                    # For 'value1|parameter2=[[value2]]|...' scenario
                    if value.startswith("["):
                        # e.g. '[[value1]]|parameter=...'
                        am_value = re.findall(u"([^\]]+[\]]+)\|.+=",value)  
                    elif value.startswith("{"):
                        # e.g. '{{value1}}|parameter=...'
                        am_value = re.findall(u"([^\}]+[\}]+)\|.+=",value)
                    else:
                        # e.g. 'value1|parameter=...'
                        am_value = re.findall(u"([^\|]+)\|.+=",value)        
                    return am_value[0]
                else:
                    return value
            # For others
            # e.g. 'value|parameter1=...'
            else:
                am_value = re.findall(u"[^\|]+",value)
                return am_value[0]
        else:
            return value
        
    def unknownFilter(self,value):  
        unknownStrings = (u"?",u"？",u"未知") # tuple of strings indicating unknown 
        if value in unknownStrings or len(value)==0: # if value is 'unknown' string or empty
            return True
        else:
            return False
        
    def proc(self): # process function
        DTPM_type = self.DTPM_type
        value = self.value
        value = self.avoidMultiple(self.value) # Avoid multiple values retrieval
        #print value
        type = self.type
        
        # type = NAME
        if type == DTPM_type[0]: 
            if self.unknownFilter(value):
                value = ""
            else:
                rmindex = value.find(u"<") # removal index of '<'('&lt;')
                if rmindex > 0:
                    value = value[:rmindex].strip()
                    # value is a substring before '<' appearance   
                
        #  type = DATE                 
        elif type == DTPM_type[1]: 
            if self.unknownFilter(value):
                value = ""
            else:
                # yyyy年mm月dd日
                cpat = re.compile(u"年|月|日")
                if cpat.search(value):
                    value = cdate.date_proc(value) # call cddate.date_proc()
                # yyyy-mm-dd or yyyy.mm.dd or yyyy/mm/dd
                else:
                    dateparts = re.findall(u"\d+", value)
                    if len(dateparts)== 3:
                        year = dateparts[0]
                        month = dateparts[1]
                        day = dateparts[2]
                    elif len(dateparts) == 2:
                        year = dateparts[0]
                        month = dateparts[1]
                        day = "00"
                    elif len(dateparts) == 1:
                        year = dateparts[0]
                        month = "00"
                        day = "00"
                    else:
                        year = "0000"
                        month = "00"
                        day = "00"
                    year = cdate.year_proc(year)
                    month = cdate.month_proc(month)
                    day = cdate.day_proc(day)
                    date_value = "%s-%s-%s"%(year,month,day)
                    value = date_value
    
        #  type = WTEXT        
        elif type == DTPM_type[2]:  
            if self.unknownFilter(value):
                value = ""
            else:
                value = wtext.wtext_proc(value)
            #return value
            
        #  type = NUM 
        elif type == DTPM_type[3]: 
            if self.unknownFilter(value):
                value = ""
            else:
                numstrings = re.findall("\d+",value)
                value = numstrings[0]
        
        #  type = UNIT 
        elif type == DTPM_type[4]: 
            if self.unknownFilter(value):
                value = ""
            else:
                value = unit.unit_proc(value)
            #return value
            
        #  type = CMT 
        elif type == DTPM_type[5]:     
            if self.unknownFilter(value):
                value = ""
            else:
                value = cmt.cmt_proc(value)
        
        #  type = IMG 
        elif type == DTPM_type[6]: 
            if self.unknownFilter(value):
                value = ""
            else:
                value = img.img_proc(value,destHost, True)
            
        return value
        #  type = CAT 
        #  CAT manipulation is in class WikiToXML
        
