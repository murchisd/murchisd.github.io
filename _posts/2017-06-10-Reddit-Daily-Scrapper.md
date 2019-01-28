---
layout: post
author: Donald Murchison
category: pr0j3cts
title: "Daily Reddit Scrapper"
---

![reddit logo]({{ site.url }}/assets/reddit/reddit_logo.png)

Reddit has become one of my favorite sources of cybersecurity articles. Several subreddits have excellent write-ups and posts for those interested in security, no matter what skill level. 

I thought it would be nice to automatically receive a list of new posts from my favorite subreddits, so, I spun up a python script retrieve a list of new posts and a python script to email the list to myself. The full scripts are below.  

***Redditscraper.py***([raw]({{ site.url }}/assets/reddit/redditscrape.txt))
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

file = "./redditupdate-tmp"			
if os.path.exists(file):
    os.remove(file)
	
with open(file, 'w') as f:
	f.write(body)

gmailsendemail.main() 
{% endhighlight %}

***Gmailsendemail.py***([raw]({{ site.url }}/assets/reddit/gmailsendemail.txt))

{% highlight python %}
# Send gmail basically taken straight from Google API docs
# https://developers.google.com/gmail/api/quickstart/python
# https://developers.google.com/gmail/api/guides/sending

import httplib2
import os

from apiclient import discovery
from oauth2client import client
from oauth2client import tools
from oauth2client.file import Storage
import base64
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

try:
	import argparse
	flags = argparse.ArgumentParser(parents=[tools.argparser]).parse_args()
except ImportError:
	flags = None

# If modifying these scopes, delete your previously saved credentials
# at ~/.credentials/gmail-python-quickstart.json
SCOPES = 'https://www.googleapis.com/auth/gmail.send'
CLIENT_SECRET_FILE = 'client_secret.json'
APPLICATION_NAME = 'Gmail API Python Send Email'


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

def main():
	"""Shows basic usage of the Gmail API.

	Creates a Gmail API service object and outputs a list of label names
	of the user's Gmail account.
	"""
	#Hard-Coded for now 
	body=''
	frm = "<your-email>@gmail.com"
	to = "<your-email>@gmail.com"
	infile = "<output from redditscrape.py>"
	with open(infile, 'r') as f:
		for line in f:
			body += line
	credentials = get_credentials()
	http = credentials.authorize(httplib2.Http())
	service = discovery.build('gmail', 'v1', http=http)
	tmp = create_message(frm,to,"Reddit Daily Update",body)
	message = send_message(service,"me",tmp)
	print "Did it work?"

	

if __name__ == '__main__':
	#Add options parser later to handle subjects and different options
	#For now hard coding to deal with reddit update
	#if len(sys.argv) < 3:
	#	print "Usage: python gmail-send-email.py <sender> <recipient> <subject> <file path for message text>"
	#	exit(0)
	#else:
	
	main()
		
{% endhighlight %}

First, I needed to read about using the Reddit API. I've worked with a few APIs before and the first step is almost always authenticating, often with a client secret and id. 

With a quick google search, I found a great article which describes how to register your application with reddit, obtain a client secret and id, and even how to use the PRAW python library to read posts.

[Build a Reddit Bot Part 1](http://pythonforengineers.com/build-a-reddit-bot-part-1/)

I followed the instructions in the article to obtain client_id and client_secret. I also read some extra notes on the praw library.

[Python Reddit API Wrapper (PRAW) documentation](https://praw.readthedocs.io/en/latest/getting_started/quick_start.html)

I created a praw.ini file in same directory as my script and saved the fields client_id, client_secret, and useragent(PyScrape Bot 0.1).

{% highlight python %}
[pybot]
client_id=
client_secret=
user_agent=PyScrape Bot 0.1 
{% endhighlight %}

Then, I wrote the script to pull any posts created on the previous day, and store the title and url of each post in a string "body".

{% highlight python %}

body = ""
for sub in subreddits:
	subreddit = reddit.subreddit(sub)
	body += sub + "\n\n"
	for post in subreddit.new():
{% endhighlight %}
<br>
I appended to a string instead of just writing to a file because I was originally going to return the "body" string to a powershell script, which would use my logged-in windows credentials and outlook to send the email. 

But I decided to use gmail instead.

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

Once the application properly authenticates, the main method makes two function calls to generate a "service".

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


