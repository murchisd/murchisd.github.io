---
layout: post
author: Donald Murchison
category: pr0j3cts
title: "Office 365 Management Activity API Application"
description: Office 365 Exchange Online log pull library and application
---

![office 365 logo]({{ site.url }}/assets/office/office-365-cloud.png)

During my time working at Sacramento State, many of the University's enterprise-class applications were moved from on-premises servers to the "cloud". While this might have reduced costs, and reduced problems associated with maintenance and deployment, it made things more difficult in the Information Security Office. 

![cloud meme]({{ site.url }}/assets/office/cloudmeme.jpg)

The most notable issue was the loss of Exchange logs.

A lot of my responsibilites as a SOC analyst, required monitoring logs and looking for anomalies, to detect compromised accounts. The Exchange logs allowed us to geolocate IPs and compare fields, like useragent strings, to determine if there was anomalous access for an account. 

![github logo]({{ site.url }}/assets/office/github.ico) [ISOLogPullLibrary (Dll)](https://github.com/murchisd/ISOLogPullLibrary)

![github logo]({{ site.url }}/assets/office/github.ico) [ExchangeOnlineLogPull (Console Application)](https://github.com/murchisd/ExchangeOnlineLogPull)

We divided the application in to two parts, a library and a console application. 

To use the application, follow the setup instructions in either of the READMEs.

{% highlight bash %}
donald@comp:~$ sudo apt-get update
{% endhighlight %}

The READMEs are currently not posted.

I will also be adding description of the process we took to develop the application and some of the problems we ran in to.



