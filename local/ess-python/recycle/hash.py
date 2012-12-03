# -*- coding: utf-8 -*-
import hashlib  
md5 = hashlib.md5() 
md5.update(u"万亚东".encode('utf-8'))  
print md5.hexdigest()
