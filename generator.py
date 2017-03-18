#coding=utf-8
import os

config = [
    ['jishushuji', '技术书籍', 'notes/技术书籍'],
    ['jishuwendang',      '技术文档',     'notes/技术文档'],
    ['zhishidianlinshizongjie','知识点临时总结', 'notes/知识点临时总结'],
    ['fangfalun',      '方法论',     'notes/方法论'],
    ['kaifapeizhijilu', '开发配置记录', 'notes/开发配置记录'],
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