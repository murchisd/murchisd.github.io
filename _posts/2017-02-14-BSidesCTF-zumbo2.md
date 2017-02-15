---
layout: post
author: Donald Murchison
category: writeup
title: "BSidesCTF zumbo2 challenge"
---

>Welcome to ZUMBOCOM....you can do anything at ZUMBOCOM.<br>
Three flags await. Can you find them?<br>
[http://zumbo-8ac445b1.ctf.bsidessf.net](http://zumbo-8ac445b1.ctf.bsidessf.net)<br>
Stage 3 - coming soon!

This was part 2 of another Web challenge, zumbo1. A teammate, Lukhvir Athwal, had discovered the source code for server.py in part 1.

![server code]({{ site.url }}/assets/bsides/ServerCodePt2.jpg)

From this code, I saw that flag 2 was in a local file and the script was using the flask library. A quick google search for "flask vulnerabilites" showed me that this page may be susceptible to Server Side Template Injection. This was a new technique for me but I found two very helpful articles.
-	[https://nvisium.com/blog/2016/03/09/exploring-ssti-in-flask-jinja2/](https://nvisium.com/blog/2016/03/09/exploring-ssti-in-flask-jinja2/)
- [https://nvisium.com/blog/2016/03/11/exploring-ssti-in-flask-jinja2-part-ii/](https://nvisium.com/blog/2016/03/11/exploring-ssti-in-flask-jinja2-part-ii/)

I highly suggest reading these articles. They provide a great explanation about template injection vulnerabilities and how to test and exploit them. I followed the second article's instructions on how to read a local file and had the flag.

![zumbo2 flag]({{ site.url }}/assets/bsides/zumbo2flag.png)

{% highlight bash%}
FLAG:RUNNER_ON_SECOND_BASE
{%endhighlight%}
