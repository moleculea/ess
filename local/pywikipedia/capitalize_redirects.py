#!/usr/bin/python
# -*- coding: utf-8  -*-

'''
Bot to create capitalized redirects where the first character of the first
word is uppercase and the remainig characters and words are lowercase.

Command-line arguments:

&params;

-always           Don't prompt to make changes, just do them.

-titlecase        creates a titlecased redirect version of a given page
                  where all words of the title start with an uppercase
                  character and the remaining characters are lowercase.

Example: "python capitalize_redirects.py -start:B -always"
'''
#
# (C) Yrithinnd
# (C) Pywikipedia bot team, 2007-2010
#
# Class licensed under terms of the MIT license
#
__version__ = '$Id: capitalize_redirects.py 9405 2011-07-16 21:19:16Z xqt $'
#

import time, sys, re
import wikipedia as pywikibot
from pywikibot import i18n
import pagegenerators

docuReplacements = {
    '&params;': pagegenerators.parameterHelp
}

class CapitalizeBot:
    def __init__(self, generator, acceptall, titlecase):
        self.generator = generator
        self.acceptall = acceptall
        self.titlecase = titlecase
        self.site = pywikibot.getSite()
        self.done = False

    def run(self):
        for page in self.generator:
            if self.done: break
            if page.exists():
                self.treat(page)

    def treat(self, page):
        if page.isRedirectPage():
            page = page.getRedirectTarget()
        page_t = page.title()
        # Show the title of the page we're working on.
        # Highlight the title in purple.
        pywikibot.output(u"\n>>> \03{lightpurple}%s\03{default} <<<"
                         % page_t)
        if self.titlecase:
            page_cap = pywikibot.Page(self.site, page_t.title())
        else:
            page_cap = pywikibot.Page(self.site, page_t.capitalize())
        if page_cap.exists():
            pywikibot.output(u'%s already exists, skipping...\n'
                             % page_cap.title(asLink=True))
        else:
            pywikibot.output(u'[[%s]] doesn\'t exist' % page_cap.title())
            if not self.acceptall:
                choice = pywikibot.inputChoice(
                        u'Do you want to create a redirect?',
                        ['Yes', 'No', 'All', 'Quit'], ['y', 'N', 'a', 'q'], 'N')
                if choice == 'a':
                    self.acceptall = True
                elif choice == 'q':
                    self.done = True
            if self.acceptall or choice == 'y':
                comment = i18n.twtranslate(self.site,
                                           'capitalize_redirects-create_redirect',
                                           {'to': page_t})
                try:
                    page_cap.put(u"#%s %s" % (self.site.redirect(True),
                                              page.title(asLink=True,
                                                         textlink=True)),
                                              comment)
                except:
                    pywikibot.output(u"An error occurred, skipping...")

def main():
    genFactory = pagegenerators.GeneratorFactory()
    acceptall = False
    titlecase = False

    for arg in pywikibot.handleArgs():
        if arg == '-always':
            acceptall = True
        elif arg == '-titlecase':
            titlecase = True
        elif genFactory.handleArg(arg):
            pass
        else:
            pywikibot.showHelp(u'capitalize_redirects')
            return

    gen = genFactory.getCombinedGenerator()
    preloadingGen = pagegenerators.PreloadingGenerator(gen)
    bot = CapitalizeBot(preloadingGen, acceptall, titlecase)
    try:
        bot.run()
    except KeyboardInterrupt:
        pywikibot.output('\nQuitting program...')

if __name__ == "__main__":
    try:
        main()
    finally:
        pywikibot.stopme()
