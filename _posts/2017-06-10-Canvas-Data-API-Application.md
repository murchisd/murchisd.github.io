---
layout: post
author: Donald Murchison
category: pr0j3cts
title: "Canvas Data API for Information Security"
description: Canvas Data API, logs, security, log pull, C# 
---

![canvas logo]({{ site.url }}/assets/canvas/canvas-logo.jpeg)

Another one bites the dust. 

Sacramento State was previously using on-premises instance of BlackBoard Learn for a learning mangement application. Now, the University is making the shift to a Canvas application hosted by Amazon Web Services. When this happens the Information Security Office will once again lose its logging capabilites for another service.

![cloud meme]({{ site.url }}/assets/canvas/cloud.jpeg)

Canvas provides a Canvas Data API which allows clients to download and implement there own instance of the Canvas Data warehouse. We wanted to create a C# application which would download and implement a queryable instance of the Canvas Data warehosue.

With these tables, we want to detect compromised accounts through anomalous activity and also academic fraud. 

Canvas Data API resources:

* [Generating API Key and Secret](https://community.canvaslms.com/docs/DOC-4656)
* [API Documentation](https://portal.inshosteddata.com/docs/api)
* [Canvas Data Schema](https://portal.inshosteddata.com/docs)
* [Canvas Data API Authentication Guide](https://instructure.jiveon.com/docs/DOC-5553)

The guide mentioned above does a very good job of describing what is required to create a local instance of Canvas Data so all I needed to do was migrate this process to C#. 

The Canvas Data API uses HMAC-SHA-256 to sign a message for authentication. Two header fields must be set in the HTTP request, Authorization and Date. 

{% highlight bash %}
Authorization: HMACAuth <API_Key>:<Signature>
Date: <Date (RFC 7231 format)>
{% endhighlight %}

The Authorization header is the literal string "HMACAuth", followed by the API key and signature seperated by a colon. The signature is a message signed using HMAC-SHA-256 and the API Secret. 

The message is a string consisting of 8 parts, most of which can be hard-coded, joined by newlines. 


{% highlight bash %}
donald@comp:~$ sudo apt-get update
{% endhighlight %}

I will be updating this post as the project progresses.
