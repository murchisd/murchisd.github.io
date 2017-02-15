---
layout: post
author: Lukhvir Athwal
category: writeup
title: "BSidesCTF zumbo1 challenge"
---

> Welcome to ZUMBOCOM....you can do anything at ZUMBOCOM.<br>
Three flags await. Can you find them?<br>
[http://zumbo-8ac445b1.ctf.bsidessf.net](http://zumbo-8ac445b1.ctf.bsidessf.net)<br>
Stages 2 and 3 - coming soon!

This was a Web challenge and the link led to a page with no links. I inspected the webpage, and saw that it had Server.py which seemed like an odd thing to place there. 

![inspect zumbo1]({{ site.url }}/assets/bsides/Zumbo1Pt2.png)

I changed the directory of the website to http://zumbo-8ac445b1.ctf.bsidessf.net/server.py which revealed the source code for server.py and the flag for part 1. I formatted the code so it looked nicer.

![server.py code]({{ site.url }}/assets/bsides/ServerCode.jpg) 

{% highlight bash%}
FLAG:FIRST_FLAG_WASNT_HARD
{%endhighlight%}
