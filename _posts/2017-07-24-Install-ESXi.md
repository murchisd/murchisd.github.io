---
layout: post
author: Donald Murchison
category: pr0j3cts
title: "Home Lab Series - Installing ESXi 6.5"
description: ESXi 6.5 installation supermicro xeon intel usb
---

![esxi logo]({{ site.url }}/assets/esxi/esxi-logo.jpeg)

This is the second post in my Home Lab Series. I will be doing a quick summary of installing ESXi on my Intel Xeon Server. Checkout ["Building an Intel Xeon Server"]({% post_url 2017-07-11-Intel-Xeon-Server %}) to see how I built the physical server. 

I did a basic install of ESXi so the process consisted of 3 main steps

* Create a bootable ESXi USB installer
* Boot from USB
* Install ESXi on USB drive 

***Create bootable USB***

The first step in the process is creating a bootable ESXi installer. I used Rufus, ESXi 6.5 iso, and a 32 GB usb drive. 8 or 16 GBs would be fine I just happened to have a spare 32 GB drive.

Download Rufus and iso

* [Rufus](https://rufus.akeo.ie/)
* [ESXi 6.5 from VMWare](https://my.vmware.com/web/vmware/details?downloadGroup=ESXI650&productId=614)


Insert your USB drive and start Rufus. In Rufus

* Set "Device" to your USB
* Set "Partition scheme and target system type" to "MBR partition scheme for BIOS or UEFI"
* Verify "Create a bootable disk using" is checked, then select "ISO Image" in the drop-down next to it
* Click on the CD icon and select the iso you downloaded from VMWare
* Click "Start"

I followed this guide, [Create and ESXi 6.5 installation USB under two minutes](https://www.starwindsoftware.com/blog/create-an-esxi-6-5-installation-usb-under-two-minutes). This is another very helpful article from the owner of vladan.fr whose blogs I reference in my ["Building an Intel Xeon Server"]({% post_url 2017-07-11-Intel-Xeon-Server %}) post.

***Boot from USB***

My server has a SuperMirco motherboard so I was going to use IPMI and KVMi to access the BIOS menu. However by default, my new server booted from the interal USB drive. I will update this post with a guide to changing the boot order.

***Install ESXi***

{% highlight bash %}
donald@comp:~$ sudo apt-get update
{% endhighlight %}



 
