#coding:utf-8
import os

'''
1 - 1    4*1 - 3    1
2 - 5    4*2 - 3    3
3 -                 
4 - 13   4*n - 3    7
'''

def gen_pattern(s):
  w = 4*len(s)-3
  h = 2*len(s)-1
  r = ''
  for i in range(1,h+1):
  	mids = s[i%len(s)],s[i%len(s)]
  	print s[i%len(s)].center(w,'.')




if __name__ == '__main__':
  s = 'WXYZ'
  r = gen_pattern(s)
  print(s)