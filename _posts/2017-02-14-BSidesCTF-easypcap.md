---
layout: post
author: Lukhvir Athwal
category: wr1t3-Ups
title: "BSidesCTF easycap challenge"
---


> Can you get the flag from the packet capture?<br>
[easycap.pcap](https://scoreboard.ctf.bsidessf.com/attachment/5e1cb4ad2dd6aef750654f5377d9e67ed2732b97fe64d56e0603242c4b87d921)


Opened the pcap file in Wireshark, noticed that the source IP address 172.31.98.199  was sending data. I applied a filter of that source Ip address and all packets that contained data.

![wireshark screenshot]({{ site.url }}/assets/bsides/easypcapWireshark.JPG)

Each packet had an extra byte of data, highlighted above. After combining, these bytes the flag was revealed.

{% highlight bash%}
FLAG:385b87afc8671dee07550290d16a8071
{%endhighlight%}
