---
layout: post
author: Donald Murchison
category: pr0j3cts
title: "Daily Reddit Scrapper"
---

![reddit logo]({{ site.url }}/assets/reddit/reddit_logo.png)

Reddit has become one of my favorite sources of cybersecurity articles. Several subreddits have excellent write-ups and articles for those interested in security, no matter what skill level. 

I also subscribe to daily emails from a few cyber security firms. One day, I realized it would be nice to receive a list of new posts from my favorite subreddits. So, I decided to spin up a python script retrieve a list of new posts and email it to myself.  

First, I needed to read about using the Reddit API. I've worked with a few APIs before and the first step is almost always authenticating, often with a client secret and id. 

With a quick google search, I found a great article which describes how to register your application with reddit, obtain a client secret and id, and even how to use the PRAW python library to read posts.

[Build a Reddit Bot Part 1](http://pythonforengineers.com/build-a-reddit-bot-part-1/)

The Python Reddit API Wrapper (PRAW) documentation is [here](https://praw.readthedocs.io/en/latest/getting_started/quick_start.html)

**To-Do include screenshots and steps here so reader doesn't need to navigate to link



My application is very simple and only reads from subreddits so I did not need to include my username and password to authenticate. I created a praw.ini file in same directory as my script and saved the fields client_id, client_secret, and useragent(PyScrape Bot 0.1).

{% highlight python %}
[pybot]
client_id=
client_secret=
user_agent=PyScrape Bot 0.1 
{% endhighlight %}

Next, I wrote a script to pull any posts created on the previous day, and store the title and url of each post.

{% highlight python %}
#!/usr/bin/python

# Reddit Scrapper 

# Runs as part of a daily script to get any new posts
# for security subreddits

import praw
from datetime import datetime, date, time, timedelta

today = datetime.combine(date.today(), time.min)
yesterday = today - timedelta(1)

reddit = praw.Reddit('pybot')

subreddits = "netsecstudents","netsec","securityCTF", "HowToHack"

body = ""
for sub in subreddits:
	subreddit = reddit.subreddit(sub)
	body += sub + "\n\n"
	for post in subreddit.new():
		posted = datetime.utcfromtimestamp(post.created_utc)
		#Grab all posts created yesterday
		if posted >= yesterday and posted <= today:
			#Added to ignore HowToHack discussions
			if "www.reddit.com" in post.url and sub == "HowToHack":
				continue
			body += "\t"+post.title.encode('utf-8') + "\n"
			body += "\t"+post.url.encode('utf-8')+"\n\n"

{% endhighlight %}

Now I just needed a way to email myself this information. I was originally going to return the "body" string to a powershell script, which would use my windows credentials and outlook to send the email. I liked this approach because I would not need to store my credentials or a secret in a config file. 

Currently, I do not have a windows machine running so I decided to use the Gmail API for now.

**To-Do get copy of my script from work machine

** Finish write up




 
