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
    contents = ''   

    for ca in config:
        count = str(len(os.listdir(ca[2])))
        contents += '## [](#header-4)' + ca[1] + ' ('+ count +')\n'
        contents += '<span id="'+ ca[0] +'"></span>' + '\n'

        for file in os.listdir(ca[2]):
          filePath= ca[2]+'/'+file
          contents += '[' + file.replace('.md','') + '](' + filePath +') <span class="split"> / </span> '

        contents += '\n\n'

    f = open('index.md','w')
    f.write(contents)
    f.close()