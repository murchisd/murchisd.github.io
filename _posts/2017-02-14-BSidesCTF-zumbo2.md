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

This was part 2 of another Web challenge, zumbo1. A teammate, Lukhvir Athwal, had discovered the source code for server.py in part 1. From this code, I saw that flag 2 was in a local file and the script was using the flask library.

![server code]({{ site.url }}/assets/bsides/ServerCode3.png)

One thing that immediately raised a flag was the commented out line at the bottom of the source. This line could be controlled by the user so I tested to see if it was vulnerable to cross-site scripting. 

{% highlight html%}
{% raw%}
http://zumbo-8ac445b1.ctf.bsidessf.net/--><script> alert(1)</script><!--
{%endraw%}
{% endhighlight %}

<!-- Text editor problem-->

The page was vulnerable to cross-site scripting but there seemed to be better approaches to this challenge.<br><br>
A quick google search for "flask vulnerabilites" showed me that this page may be susceptible to Server Side Template Injection(SSTI). This was a new technique for me but I found two very helpful articles.<br>

-	[https://nvisium.com/blog/2016/03/09/exploring-ssti-in-flask-jinja2/](https://nvisium.com/blog/2016/03/09/exploring-ssti-in-flask-jinja2/)
- [https://nvisium.com/blog/2016/03/11/exploring-ssti-in-flask-jinja2-part-ii/](https://nvisium.com/blog/2016/03/11/exploring-ssti-in-flask-jinja2-part-ii/)

I highly suggest reading these articles. They provide a great explanation about template injection vulnerabilities and how to test and exploit them.

The article mentions that pages vulnerable to cross-site scripting are often vulnerable to SSTI. To test this vulnerability they suggest entering \{\{ 7 * 7 \}\} into the vulnerable field.

{% highlight html%}
{% raw%}
http://zumbo-8ac445b1.ctf.bsidessf.net/{{ 7 * 7 }}
{%endraw%}
{% endhighlight %}
<!-- Text editor problem-->

![ssti test]({{ site.url }}/assets/bsides/sstiTest.png)

Since the server returned 49 I know it is vulnerable. I just needed to use this to read the local file ./flag.<br><br>
The second article above gives a great explanation of how to use this exploit to read local files. Essentially, I can access any class in the current python environment and here I use the file class.<br><br>
I instantiated a class of type 'file' and call the read() method. Read the article for a better explanation. 

![zumbo2 flag]({{ site.url }}/assets/bsides/zumbo2flag.png)

{% highlight bash%}
FLAG:RUNNER_ON_SECOND_BASE
{%endhighlight%}
<br>
**I was unable to solve Zumbo 3 but I was on the right path. I was trying to use this exploit with a payload to make a get request to 'http://vault:8080/flag'.<br><br>
Unfortunately, I focused on the request library and should have looked at other options.
I have listed two writeups below for references.<br>
- [http://fadec0d3.blogspot.com/2017/02/blog-post.html](http://fadec0d3.blogspot.com/2017/02/blog-post.html)
- [https://0day.work/bsidessf-ctf-2017-web-writeups/#zumbo3](https://0day.work/bsidessf-ctf-2017-web-writeups/#zumbo3)
