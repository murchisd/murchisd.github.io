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

import praw, sys, os
from datetime import datetime, date, time, timedelta
import gmailsendemail

today = datetime.combine(date.today(), time.min)
yesterday = today - timedelta(days=1)

reddit = praw.Reddit('pybot')

subreddits = "netsecstudents","netsec","securityCTF", "HowToHack"

body = ""
for sub in subreddits:
	subreddit = reddit.subreddit(sub)
	body += sub + "\n\n"
	for post in subreddit.new():
		if sub == "HowToHack" and "www.reddit.com" in post.url:
			#Only want write ups from How To Hack, don't care about the discussions
			continue
		if datetime.utcfromtimestamp(post.created_utc) >= yesterday and datetime.utcfromtimestamp(post.created_utc) <= today:
			body += "\t"+post.title.encode('utf-8') + "\n"
			body += "\t"+post.url.encode('utf-8')+"\n\n"
			#print datetime.utcfromtimestamp(post.created_utc)

{% endhighlight %}

Now I just needed a way to email myself this information.

I was originally going to return the "body" string to a powershell script, which would use my windows credentials and outlook to send the email. I liked this approach because I would not need to store my credentials or a secret in a config file. 

Currently, I do not have a windows machine running so I decided to use the Gmail API for now.

My gmail application was taken almost directly from the google api docs for python. They provide very good documentation for authenticating and for sending emails.

[API Quickstart(Authenticating)](https://developers.google.com/gmail/api/quickstart/python)

[API Sending](https://developers.google.com/gmail/api/guides/sending)

To create your own email application, start by following the instructions on the API Quickstart page. The first thing the application must do is authenticate.

{% highlight python %}

def get_credentials():
	"""Gets valid user credentials from storage.
	
	If nothing has been stored, or if the stored credentials are invalid,
	the OAuth2 flow is completed to obtain the new credentials.
	
	Returns:
	Credentials, the obtained credential.
	"""
	home_dir = os.path.expanduser('~')
	credential_dir = os.path.join(home_dir, '.credentials')
	if not os.path.exists(credential_dir):
		os.makedirs(credential_dir)
	credential_path = os.path.join(credential_dir,'gmail-python-send-email.json')
	store = Storage(credential_path)
	credentials = store.get()
	if not credentials or credentials.invalid:
		flow = client.flow_from_clientsecrets(CLIENT_SECRET_FILE, SCOPES)
		flow.user_agent = APPLICATION_NAME
		if flags:
			credentials = tools.run_flow(flow, store, flags)
		else: # Needed only for compatibility with Python 2.6
			credentials = tools.run(flow, store)
		print('Storing credentials to ' + credential_path)
	return credentials
{% endhighlight %}

The Quickstart guide does a good job of describing what is going on in this method. Essentially, the method checks for previously stored credentials, and if they are not found or not valid, tries to reauthenticate.

It appears that the client will need to reathenticate if the scope is changed(read, send, etc.). This is done with the [oauth2client tools](http://oauth2client.readthedocs.io/en/latest/source/oauth2client.tools.html) sub module and the run_flow function. 

run_flow will attempt to open an authorization server page in the user's web borwser. If the user is not already signed in, they will need to do so. 

![reddit allow]({{ site.url }}/assets/reddit/redditallow.JPG)

Once signed in the page will ask the user to allow permissions to the application. These should match the permissions associated with the scope. A list of different scopes and their permissions can be found [here](https://developers.google.com/gmail/api/auth/scopes).

The credentials will be stored so you will not need to be signed in to your google account or allow permissions next time.

Once the application properly authenticates, the main method makes two function to generate a "service".

{% highlight python %} 

http = credentials.authorize(httplib2.Http())
service = discovery.build('gmail', 'v1', http=http)
{% endhighlight %}

We then create a message in the MIME format and send.

{% highlight python %}

def create_message(sender, to, subject, message_text):
	"""Create a message for an email.

	Args:
	sender: Email address of the sender.
	to: Email address of the receiver.
	subject: The subject of the email message.
	message_text: The text of the email message.

	Returns:
	An object containing a base64url encoded email object.
	"""
	message = MIMEText(message_text)
	message['to'] = to
	message['from'] = sender
	message['subject'] = subject
	return {'raw': base64.urlsafe_b64encode(message.as_string())}

def send_message(service, user_id, message):
	"""Send an email message.

	Args:
	service: Authorized Gmail API service instance.
	user_id: User's email address. The special value "me"
	can be used to indicate the authenticated user.
	message: Message to be sent.

	Returns:
	Sent Message.
	"""
	try:
		message = (service.users().messages().send(userId=user_id, body=message).execute())
		print 'Message Id: %s' % message['id']
		return message
	except errors.HttpError, error:
		print 'An error occurred: %s' % error	

{% endhighlight %}

When I get time, I will update this post with more details about what is happening after the application obtains credentials. For now the links I have provided should explain most of it. 

Full source code for both scripts can be found below.

[RedditScrape.py]({{ site.url }}/assets/reddit/redditscrape.txt)

[GmailSendEmail.py]({{ site.url }}/assets/reddit/gmailsendemail.txt) 

