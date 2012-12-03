#!/usr/bin/python
# -*- coding: utf-8  -*-
import os

def main():
    f = file("/home/anshichao/repl_cron","r")
    cmd = f.read()
    f.close()
    print cmd
    os.system(cmd)  

if __name__ == '__main__':
    main()