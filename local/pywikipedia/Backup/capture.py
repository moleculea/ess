# -*- coding: utf-8 -*-
"""
Heavily modified from get.py
Located in pywikipedia folder
"""

# (C) Daniel Herding, 2005
# An Shichao 2012/2/4
# Distributed under the terms of the MIT license.

__version__='$Id: get.py 8525 2010-09-11 16:21:58Z xqt $'

import wikipedia as pywikibot

def capture(pt):
    for pageTitle in pt.pageyield():
        print pageTitle.encode('utf-8')
        page = pywikibot.Page(pywikibot.getSite(), pageTitle) # get page through MediaWiki API using pywikipedia
        #pywikibot.output(page.get(), toStdout = False)
        pageText = page.get()
        yield pageText, pageTitle # yield wiki text and the page title
