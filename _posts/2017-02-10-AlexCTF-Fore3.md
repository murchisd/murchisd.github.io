---
layout: post
author: Donald Murchison
category: wr1t3-Ups
title: "AlexCTF Fore3: USB Probing"
---

>One of our agents managed to sniff important piece of data transferred transmitted via USB, he told us that this pcap file contains all what we need to recover the data can you find it ?<br>
[fore2.pcap](https://ctf.oddcoder.com/files/2dec0720c62f1ad663218618a4822e9b/fore2.pcap)



I opened the pcap file up in Wireshark and saw that the pcap file contained mulitple writes to a usb drive.

![wireshark]({{ site.url }}/assets/alex/usbwireshark.png)

I wanted to examine the data more closely so I ran a tshark command to print out the usb data.

{%highlight bash%}
tshark -r fore2.pcap -Tfields -e usb.capdata
{%endhighlight%}

When looking through this output, I noticed the hex signature for a png file in one of the lines. I was able to use grep, sed, and xxd to output the data in to a png file.

![tshark command]({{ site.url }}/assets/alex/usbflag.JPG)

Once I opened the png file, I had the flag.

![png flag]({{ site.url }}/assets/alex/usbflag.png)

{% highlight bash %}
ALEXCTF{SN1FF_TH3_FL4G_OV3R_USB}
{%endhighlight%}


