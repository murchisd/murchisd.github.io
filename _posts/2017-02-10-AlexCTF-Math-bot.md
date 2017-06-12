---
layout: post
author: Donald Murchison
category: wr1t3-Ups
title: "AlexCTF SC1: Math Bot"
---

> SC1: Math Bot

Unfortunately, by the time I got around to writing this the AlexCTF challenges were no longer available so I do not have the exact description.<br><br>
However, this was a basic scripting challenge in which the user needed to connect to a server and answer mulitple math questions.<br><br>
To do this I wrote a python script to parse the line use the operator library to solve the math problem.

{% highlight python %}

#!/usr/bin/python

import socket
import operator

ops = {"+":operator.add, "-":operator.sub, "*":operator.mul, "/":operator.div, "%":operator.mod}

host= "195.154.53.62"

port= 7331

result=0

		
s=socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.connect((host,port))

while 1:
	res = s.recv(1024)
	print res
	if "ALEXCTF{" in res:
		break

	lines = res.split("\n")
	
	for line in lines:
		if "=" in line:
	
			nums = line.split(' ')
			print nums
	
			result = ops[nums[1]](int(nums[0]),int(nums[2]))
			print result
	
			s.send(str(result)+"\n")
			
{% endhighlight %}

This script would continually send the answer to problems until the flag was seen in the response. Luckily, I already had this screenshot.

![flag]({{ site.url }}/assets/alex/mathflag.JPG)

{% highlight bash %}
ALEXCTF{1_4M_l33t_b0t}
{% endhighlight%}
