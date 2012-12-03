#coding=utf-8
"""
author: Ccyber5
functionality: convert text in FILENAME so as to be put in Template:LatestArticleAuto
"""
import io,re,string
FILENAME = "auto-newarticles.txt"
def firstrepl():
    myfile = open(FILENAME,"r")   
    str1 = myfile.read()  
    patt = re.compile(r"#[^#]*\]\]")  
    arr = patt.findall(str1)  
    print arr[0]
    f = open(FILENAME,"w") 
    f.write(arr[0].replace("#",""))	
    for i in range(1,len(arr)):
        f.write(arr[i].replace("#"," · "))
    f.close()

def secondrepl():
    myfile = open(FILENAME,"r")   
    str1 = myfile.read()
    f = open(FILENAME,"w")
    f.write("AAAA\n\'\'\'Template:LastestArticlesAuto\'\'\'\n<!--Auto created content by Mitsuki Kojima -->\n* " + str1)
    f.close()

def thirdrepl():
    myfile = open(FILENAME,"r")   
    str1 = myfile.read()
    f = open(FILENAME,"a")
    f.write("\n<noinclude>[[Category:模板]]</noinclude>\nBBBB")
    f.close()


firstrepl()
secondrepl()
thirdrepl()

