# -*- coding: utf-8 -*-
import re
def unit_proc(value):
    npat = re.search("\d+",value)
    if npat: # if Arabic number exists
        units = re.findall("([\d\.]+)([^\d\.]+)",value)
        #print units
        if len(units) > 0:
            num = units[0][0].strip() # number
            cls = units[0][1].strip() # classfier
            if cls.startswith("|"):
                cls = ""
            value = num + ";" + cls
        else:
            units = re.findall("([\d\.]+)",value)
            num = units[0]
            value = num
    else:
        if re.search(u"(一|二|三|四|五|六|七|八|九|十|十一|十二|十三)", value):
            cpat = re.findall(u"(一|二|三|四|五|六|七|八|九|十|十一|十二|十三)(\S+|)",value) # parse Chinese numbers up to 13
            ctoa = {
            u"一" : "1",
            u"二" : "2",
            u"三" : "3",
            u"四" : "4",
            u"五" : "5",
            u"六" : "6",
            u"七" : "7",
            u"八" : "8",                   
            u"九" : "9",   
            u"十" : "10",
            u"十一" : "11",
            u"十二" : "12",
            u"十三" : "13" ,                          
            }
            if len(cpat)>0:
                #print cpat
                cnum = cpat[0][0] # convert Chinese number
                num = ctoa[cnum]  # into Arabic number
                cls = cpat[0][1] # classfier
                value = num + ";" + cls
            else:
                value = ""
        else:
            value =  ""
    return value
