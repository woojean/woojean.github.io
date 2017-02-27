#coding=utf-8
import os

config = [
    ['zhuanyejishu', '专业技术', 'notes/专业技术'],
    ['fangfalun',    '方法论',   'notes/方法论'],
    ['wendang',      '文档',     'notes/文档'],
    ['yunweicaozuo', '运维操作', 'notes/运维操作'],
    ['linshizongjie','临时总结', 'notes/临时总结'],
    ['zhongguogudaizhexue','中国古代哲学', 'notes/中国古代哲学'],
]

if __name__ == '__main__':
    contents = ''   

    for ca in config:
        count = str(len(os.listdir(ca[2])))
        contents += '## ['+ ca[1] +'](#header-4)' + ' '+ count +' \n'
        contents += '<span id="'+ ca[0] +'"></span>' + '\n'

        index = 1
        for file in os.listdir(ca[2]):
          filePath= ca[2]+'/'+file
          size = str(os.path.getsize(filePath) / 1000)
          contents += '[' + file.replace('.md','') + '](' + filePath +')'
          contents += '<span class="size"> '+ size +' k </span>'
          contents += '<span class="split"> / </span>'
          #if( 0 == index % 3 ):
          #    contents += '\n\n'
          index += 1
        contents += '\n\n'

    f = open('index.md','w')
    f.write(contents)
    f.close()