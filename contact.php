---
# You don't need to edit this file, it's empty on purpose.
# Edit theme's home layout instead if you wanna make some changes
# See: https://jekyllrb.com/docs/themes/#overriding-theme-defaults
layout: page
title: Contact
pagetitle: c0nt@ct
navigation_weight: 6
---
<html>
<div class="container">
	<br>
<?php

if(isset($_POST['g-recaptcha-response'])){
	$captcha=$_POST['g-recaptcha-response']
}
?>
{% if captcha%}
{% highlight bash %}
EHLO
RCPT TO: <donaldmurchison@csus.edu>
{% endhighlight %}
{% endif %}
<br>
<p>Currently, the best way to reach me is at my CSUS email address.</p>

    <form action="contact.php" method="POST">
      <div id="html_element"></div>
      <br>
      <input type="submit" value="Email">
		<div class="g-recaptcha" data-sitekey="6LcnOBUUAAAAAFirWKoqdC3oKncJDF5XT_tDuSDK"></div>
		</form>

</div>
</html>
