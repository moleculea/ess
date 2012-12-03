#!/usr/bin/python
# -*- coding: utf-8  -*-
import os,time
import urllib
def init():
    f = file("/var/local/ess-python/output/count.id","r")
    cnt = f.read()
    f.close()
    return cnt

def run():
    cnt = init()
    while True:
        fn = file("/var/local/ess-python/output/count.id","r")
        currentId = fn.read()
        fn.close()
        if cnt != currentId:
            fc = file("/var/local/ess-python/output/repl_cron.txt","r")
            p = fc.read()

            parts = p.split(";;")
            
            args = parts[0] # args: "paramtername1##valuename1&&paramtername2##valuename2&&.."
            pg_name = parts[1]
            pg_name = urllib.unquote(pg_name).decode('utf-8')
            #print pg_name
            #print "FUCKKKKKKk"
            pl = args.split("&&")
            #print "***********PL*************"
            #print pl
            rcmd = ""
            for repl in pl:
                if repl:
                    #print "REPL"
                    #print repl
                    arg = repl.split("##")
                    parameter = arg[0]
                    value = arg[1]
                    pv = "parameter=u'%s'"%parameter
                    vv = "value=u'%s'"%value
                    exec(pv)
                    exec(vv)
                    #print value
                    #print parameter
                    rcmd += u" \"(\|\s*%s\s*)=(.+)(\s*(\}\}|\|.+=))\" \"\\1=%s\\3\" "%(parameter,value,)

            
            cmd = "/usr/bin/python /var/local/pywikipedia/replace.py"
            #print cmd
            #cmd += u"\"(\|\s*%s\s*)=(.+)(\s*(\}\}|\|.+=))\" \"\\1=%s\\3\"  -page:\"%s\" "%(parameter,value,pg_name)
            cmd += rcmd
            #print cmd
            #print "Fuck"
            cmd +=" -page:\"%s\" -regex -always"%pg_name
            #cmd += " -regex -always >/dev/null 2>&1"
            #print cmd
            #os.system("nohup " + cmd.encode('utf-8') + " >/dev/null 2>&1 &")
            os.system(cmd.encode('utf-8'))
            fc.close()
            cnt = init()
        else:
            #print "No change"
            pass
        time.sleep(1)
        
def main():
    run() 
if __name__ == '__main__':
    main()
    """
    parameter = u"成员数"
    value= 200
    pg_name = "%E5%8C%97%E4%BA%AC%E7%A7%91%E6%8A%80%E5%A4%A7%E5%AD%A6%E5%AD%A6%E7%94%9F%E7%A7%91%E5%AD%A6%E6%8A%80%E6%9C%AF%E5%8D%8F%E4%BC%9A"
    cmd = "/usr/bin/python /var/local/pywikipedia/replace.py "
    cmd += u"\"(\|\s*%s\s*)=(.+)(\s*(\}\}|\|.+=))\" \"\\1=%s\\3\"  -page:\"%s\" "%(parameter,value,pg_name)
    cmd +=" -regex -always"
    print cmd
    os.system(cmd.encode('utf-8'))
    """