# -*- coding: utf-8 -*-
"""
cmt.py : comment process
Process cmt value into ":"-separated and return ";" separated string
Based on WTEXT, can manipulate colon-separated elements
"""
import re

def cmt_proc(value):
    cmt_split = re.split(u"<br/>|&lt;br/&gt;|&lt;br/ &gt;|&lt;br&gt;|,|，|、|；|;", value)
    cmts = []
    print "cmt_split",cmt_split    
    if len(cmt_split) > 1: 
        for cmt in cmt_split:
            cmt = cmt.strip() # remove whitespaces
            if len(cmt) > 0:
                cln_split = re.sub(u"：", u":", cmt)
                cmt = cln_split
                cmts.append(cmt)
    elif len(cmt_split) == 1:
        cmt = cmt_split[0]
        cmt = cmt.strip()
        cln_split = re.sub(u"：", u":", cmt)
        cmt = cln_split
        if len(cmt) > 0:
            cmts.append(cmt)
    value = ';'.join(cmts)
    value = ';' + value + ';'  
    return value
