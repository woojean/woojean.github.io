if __name__ == '__main__':
  f = open('/Users/wujian/Downloads/test.html','r')
  s = f.read()
  print s

  s = s.decode('gb2312','ignore').encode('utf-8','ignore')

  f = open('test2.html','w')
  f.write(s)
  f.close()