 # -*- coding: utf-8  -*-
 
import family
 
 # The Wiki1.16. #Put a short project description here.
 
class Family(family.Family):
 
     def __init__(self):
         family.Family.__init__(self)
         self.name = 'ibeike' # Set the family name; this should be the same as in the filename.
         self.langs = {
             'en': 'wiki.ibeike.com', # Put the hostname here.
         }
 
         # Translation used on all wikis for the different namespaces.
         # Most namespaces are inherited from family.Family.
         # Check the family.py file (in main directory) to see the standard
         # namespace translations for each known language.
         # You only need to enter translations that differ from the default.
         self.namespaces[4] = {
             '_default': u'iBeiKe Wiki', # Specify the project namespace here.
         }
 
         self.namespaces[5] = {
             '_default': u'iBeiKe Wiki talk', # Specify the talk page of the project namespace here. 
         }		 

         self.namespaces[10] = {
            'en': [u'Template', u'模板'],
        }
		
     def version(self, code):
         return "1.16.5"  # The MediaWiki version used. Not very important in most cases.
 
     def scriptpath(self, code):
         return '' # The relative path of index.php, api.php : look at your wiki address. 
# This line may need to be changed to /wiki or /w, 
# depending on the folder where your mediawiki program is located.
# Note: Do not _include_ index.php, etc.
