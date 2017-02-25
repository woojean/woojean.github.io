#coding=utf-8
import os

config = [
    ['zhuanyejishu', '专业技术', 'notes/专业技术'],
    ['fangfalun',    '方法论',   'notes/方法论'],
    ['wendang',      '文档',     'notes/文档'],
    ['yunweicaozuo', '运维操作', 'notes/运维操作'],
    ['linshizongjie','临时总结', 'notes/临时总结'],
]

if __name__ == '__main__':
    directory = ''
    contents = ''   

    for ca in config:
        directory += '[' + ca[1] + '](#' + ca[0] + ')' + '\n'
        contents += '## [](#header-4)' + ca[1] + '\n'
        contents += '<span id="'+ ca[0] +'"></span>' + '\n'

        for file in os.listdir(ca[2]):
          filePath= ca[2]+'/'+file
          contents += '[' + file.replace('.md','') + '](' + filePath +') \n\n '

        contents += '\n\n'

    f = open('index.md','w')
    #f.write(directory + '\n\n' + contents)
    f.write(contents)
    f.close()