---
layout: post
author: Kameryn Taylor
category: wr1t3-Ups
title: "BSidesCTF easyauth challenge"
---

> Can you gain admin access to this site?<br>
[http://easyauth-afee0e67.ctf.bsidessf.net](http://easyauth-afee0e67.ctf.bsidessf.net)<br>
[easyauth.php](https://scoreboard.ctf.bsidessf.com/attachment/cdbec071e3710a8465030f032d44e72ab2449098c9ad6597256087751d19bb39)

Looking at the php script, we see that the only authentication for the admin account is if the username=administrator. So, log-in as guest to get a valid cookie, then use burp to change the username to administrator.

![burp screenshot]({{ site.url }}/assets/bsides/easyauthburp.png)

After sending the modified request, the flag was revealed.

![adminpanel screenshot]({{ site.url }}/assets/bsides/easyauthadmin.png)

{% highlight bash%}
FLAG:0076ecde2daae415d7e5ccc7db909e7e
{%endhighlight%}
