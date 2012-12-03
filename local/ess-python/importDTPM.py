# -*- coding: utf-8 -*-

import mysql.connector
import time
from dbconfig import Config # dbconfig is for dbconfig.py
config = Config.dbinfo().copy() # Initialization

def importDTPM(paralist, indexname, hashpath, pageTitle, page):
    db = mysql.connector.Connect(**config)
    cursor = db.cursor()
    
    v_path = hashpath[-4:]
    
    # Get the latest revision id of this page
    rev_id = page.latestRevision()
    
    print v_path
    print rev_id
    tb_name = "tb_in_" + indexname
    query_str = "INSERT INTO `%s` "%(tb_name)
    field_str = "("
    value_str = "("
    field_str += "pg_name,v_path"
    value_str += "'%s','%s'"%(pageTitle,v_path)
    cnt = 0
    for p in paralist:
        field_str += "," + p[0]
        value_str += "," + "'" + p[1] + "'"
        cnt += 1    

    # Get current local time    
    in_time = time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(time.time()))
    field_str += ",rev_id,in_time,up_time"
    value_str += ",'%s','%s','%s'"%(rev_id,in_time,'0000-00-00 00:00:00')
    field_str += ")"
    value_str += ")"
    query_str = query_str + field_str + " VALUES " + value_str
    #print query_str
    cursor.execute(query_str)
    warnings = cursor.fetchwarnings()
    if warnings:
        print warnings
    db.commit()
    db.close()

if __name__ == "__main__":
    paralist = [['f1','v1'],['f2','v2']]
    importDTPM(paralist, 'index_01')
