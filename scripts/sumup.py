# -*- coding:utf-8 -*- 
import re
import os
import sys
default_encoding = 'utf-8'
if sys.getdefaultencoding() != default_encoding:
  reload(sys)
  sys.setdefaultencoding(default_encoding)


if __name__ == '__main__':
  pass
  root = '../_posts/技术问题分类总结/'
  ret = {}
  
  print '-----------------------------------'
  categoryNum= 0
  for i in os.listdir(root):
    file = os.path.join(root,i)
    if os.path.isfile(file):
        categoryNum += 1
        f = open(file,'r')
        s = f.read()
        f.close()

        arr = re.findall( r'#{2}\s*(.*)\s*\n' , s )
        ret[i] = arr
        
  print 'categoryNum  '.rjust(32) + str(categoryNum)
  print '-----------------------------------'

  s = ''
  total = 0
  
  for fileName,itemList in ret.items() :
    category = fileName.replace('.md','').replace('2017-04-05-','')
    s += '# '+ category + '\n'
    
    totalOfCategory = 0
    for item in itemList:
      s += '* '+item+'\n'
      totalOfCategory += 1
      total +=1
    print category.rjust(30)+'  '+str(totalOfCategory)
    s += '\n'
  print '-----------------------------------'
  
  print 'total  '.rjust(32)+str(total)
  print '-----------------------------------'
  
  f = open('sumup.md','w')
  f.write(s)
  f.close()


