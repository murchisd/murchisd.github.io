---
layout: post
author: Donald Murchison
category: pr0j3cts
title: "Office 365 Management Activity API Application"
---

![office 365 logo]({{ site.url }}/assets/office/office-365-cloud.png)

During my time working at Sacramento State, many of the University's enterprise-class applications were moved from on-premises servers to the "cloud". While this might have reduced costs, and reduced problems associated with maintenance and deployment, it made things more difficult in the Information Security Office. The most notable issue was the loss of Exchange logs.

A lot of my responsibilites as a SOC analyst, required monitoring logs and looking for anomalies, to detect compromised accounts. The Exchange logs allowed us to geolocate IPs and compare fields, like useragent strings, to determine if there was anomalous access for an account. Unfortunately, once the University moved to Exchange Online, we lost all of our logging capabilites for this service. 

Microsoft informed my supervisor that, for a fee, they would provied logging capabilites, or, for free, we could use the management activity api. Free is always better so we decided to develop a C# application to interact with the API. 

We divided the application in to two parts, a library and a console application. Links to both repositories can be found below:

![github logo]({{ site.url }}/assets/office/github.ico) [ISOLogPullLibrary (Dll)](https://github.com/murchisd/ISOLogPullLibrary)

![github logo]({{ site.url }}/assets/office/github.ico) [ExchangeOnlineLogPull (Console Application)](https://github.com/murchisd/ExchangeOnlineLogPull)

To use the application, follow the setup instructions in either of the READMEs.

The READMEs are currently not posted.

I will also be adding description of the process we took to develop the application and some of the problems we ran in to.



