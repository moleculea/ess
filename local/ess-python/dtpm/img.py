# -*- coding: utf-8 -*-
"""
img.py : image/file process
Process img and get thumbnail
"""
import urllib2
import re

HTTP = "http://"
INDEXPHP = "/index.php/"

def img_proc( value, destHost, indexphp = True, scriptPath = ""):
    innerwiki = re.findall(u"\[\[([^]]+)]]",value)
    if len(innerwiki) > 0: # if value format is [[File:File name.jpg|p1|p2|..]]
        rawImgName = re.findall(u"(Image|File|文件):\s*([^\|]+)",innerwiki[0])
        if len(rawImgName) > 0:
            imageName = rawImgName[0][1].strip()
        else:
            imageName = ""
    else: #  if value format is without "[[ ]]"
        imgwiki = re.findall(u"(File|Image|文件):(.+)",value)
        if len(imgwiki) > 0: # if value format is File:File name.jpg
            imageName = imgwiki[0][1].strip()
        else: # if value format is simply File name.jpg
            imageName = value
    imageNameNS = "File:" + imageName # File:Image name
    imageNameNS = re.sub("\s","_",imageNameNS) # File:Image_name
    if len(scriptPath.strip()) == 0:  # if no script path
        if indexphp:
            descriptPageURL = HTTP + destHost + INDEXPHP + imageNameNS
        else:
            descriptPageURL = HTTP + destHost + "/" + imageNameNS
    else:
        if indexphp:
            descriptPageURL = HTTP + destHost + scriptPath + INDEXPHP + imageNameNS
        else:
            descriptPageURL = HTTP + destHost + scriptPath + "/" + imageNameNS
    thumbURL = getThumb(descriptPageURL)       
    value = imageName + ";" + thumbURL
    return value
	
def getThumb( url ):
    try:
        url = url.encode('utf-8')
        fp = urllib2.urlopen( url )
        html = fp.read()
        fp.close()
        patternline = re.compile(u"<td>当前[^\n]+".encode('utf-8'))
        patterntag = re.compile("src=\"(\S+)\"")
        codes = patternline.findall(html)
        if len(codes) > 0:
            for code in codes:
                thumb = patterntag.findall(code)
            return thumb[0]
        else:
            return ""
    except Exception,e:
        print 'Cannot open the URL: ', e
        pass

#img_proc(u"File:Li Qing.jpg", "wiki.ibeike.com", True)
if __name__ == "__main__":
    print getThumb("http://wiki.ibeike.com/index.php/%E6%96%87%E4%BB%B6:Woaiwoshi_seal.png")