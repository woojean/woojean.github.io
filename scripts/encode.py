s = open('tmp.html','r').read().decode('gb2312','ignore').encode('utf-8','ignore')
f = open('PHP.html','w')
f.write(s)
f.close()
