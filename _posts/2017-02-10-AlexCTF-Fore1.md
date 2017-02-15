---
layout: post
author: Donald Murchison
category: writeup
title: "AlexCTF Fore1: Hit the Core"
---

> Fore1: Hit the Core<br>
[fore1.core](https://ctf.oddcoder.com/files/375da9681adf6322fb0c4c985474e19b/fore1.core)

The was no description so I started with two basic tools for a file investigation, file and strings.

![basic tools]({{ site.url }}/assets/alex/fore1basic.JPG)

Since strings did not return the flag, I ran strings again and looked through the output. I noticed a line which contained opening and closing brackets. I asssumed this was the flag but encoded somehow.

![encoded flag]({{ site.url }}/assets/alex/fore1.JPG)

I copied the string in to a text editor to see if I could find a pattern. I knew that "ALEXCTF" preceded the "{" so I tried to use this fact to break the code. I divided the lines into 5 characters each and foudn the flag.

![flag highlight]({{ site.url }}/assets/alex/fore1flaghighlight.JPG)

{% highlight bash%}
ALEXCTF{K33P_7H3_g00D_w0rk_up}
{%endhighlight%}

