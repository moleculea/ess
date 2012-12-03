# -*- coding: utf-8 -*-
"""
wtext.py : WTEXT process
Process WTEXT value and return ";" separated string
"""
import re

def wtext_proc(value):
    wtext_split = re.split(u"<br/>|&lt;br/&gt;|&lt;br/ &gt;|&lt;br&gt;|,|，|、|；|;|/", value)
    wtexts = []        
    if len(wtext_split) > 1: 
        for wtext in wtext_split:
            wtext = wtext.strip() # remove whitespaces
            if len(wtext) > 0:
                wtexts.append(wtext)
    elif len(wtext_split) == 1:
        wtext = wtext_split[0]
        wtext = wtext.strip()
        if len(wtext) > 0:
            wtexts.append(wtext)
    value = ';'.join(wtexts)
    value = ';' + value + ';'  
    return value
    