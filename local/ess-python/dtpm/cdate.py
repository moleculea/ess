# -*- coding: utf-8 -*-
"""
cdate.py : Chinese date process
Part of DTPM engine to process Chinese-formatted date
"""
import re

def year_proc(year):
    if len(year) == 4:
        year = year
    elif len(year) == 2:
        year = '19' + l_year[0]
    else:
        year = year[-4:].zfill(4) # intercept last 4 digit,; if less than 4, fill 0
    return year
    
def month_proc(month):
    if len(month) <= 2 :
        if int(month)>12:
            month = '12'
        month = month.zfill(2)
    else: # if len(month) > 2
        month = month[-2:] # intercept last 2 digit
        month = month_proc(month) # iterate the function it self  
    return month
    
def day_proc(day):
    if len(day) <= 2 :
        if int(day)>31:
            day = '31'
        day = day.zfill(2)
    else: # if len(day) > 2
        day = day[-2:] # intercept last 2 digit
        day = day_proc(day) # iterate the function it self  
    return day    
    
def date_proc(value):
#value = u"2001年1月8日"
    year_pattern = re.compile( u'(\d+)年' )
    month_pattern = re.compile( u'(\d+)月' )
    day_pattern = re.compile( u'(\d+)日' )

    l_year = year_pattern.findall( value )
    l_month = month_pattern.findall( value )
    l_day = day_pattern.findall( value )
       
    if len(l_year)>0:
        year = year_proc(l_year[0])
    else:
        year = '0000'
    if len(l_month)>0:
        month = month_proc(l_month[0])
    else:
        month = '00'
    if len(l_day)>0:
        day = day_proc(l_day[0])
    else:
        day = '00'
    date_value = "%s-%s-%s"%(year,month,day)
    return date_value