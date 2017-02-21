---
layout: post
author: Donald Murchison
category: writeup
title: "AlexCTF CR3: What is this encryption?"
---

> 
Fady assumed this time that you will be so n00b to tell what encryption he is using
he send the following note to his friend in plain sight :<br><br>
p=0xa6055ec186de51800ddd6fcbf0192384ff42d707a55f57af4fcfb0d1dc7bd97055e8275cd4b78ec63c5d592f567c66393a061324aa2e6a8d8fc2a910cbee1ed9
<br><br>
q=0xfa0f9463ea0a93b929c099320d31c277e0b0dbc65b189ed76124f5a1218f5d91fd0102a4c8de11f28be5e4d0ae91ab319f4537e97ed74bc663e972a4a9119307
<br><br>
e=0x6d1fdab4ce3217b3fc32c9ed480a31d067fd57d93a9ab52b472dc393ab7852fbcb11abbebfd6aaae8032db1316dc22d3f7c3d631e24df13ef23d3b381a1c3e04abcc745d402ee3a031ac2718fae63b240837b4f657f29ca4702da9af22a3a019d68904a969ddb01bcf941df70af042f4fae5cbeb9c2151b324f387e525094c41
<br><br>
c=0x7fe1a4f743675d1987d25d38111fae0f78bbea6852cba5beda47db76d119a3efe24cb04b9449f53becd43b0b46e269826a983f832abb53b7a7e24a43ad15378344ed5c20f51e268186d24c76050c1e73647523bd5f91d9b6ad3e86bbf9126588b1dee21e6997372e36c3e74284734748891829665086e0dc523ed23c386bb520
<br><br>
He is underestimating our crypto skills!
<br>

Being familiar with cryptography and the math used in RSA helped a lot with this challenge. I saw that we were given p, q, e, and the ciphertext so now I just needed to find d use it to decrypt the cipher. Since it helps to understand how RSA works I have provided a very brief description below.

Basic overview of RSA:<br>
1) Pick two large primes p and q<br>
2) n = p * q<br>
3) φ(n) = (p-1)(q-1)<br>
4) Pick an e relatively prime to φ(n) (e will be used to encrypt the message)<br>
5) Find mulitplicative inverse of e mod φ(n) (This means find d such that d*e mod φ(n) = 1) <br>

Once you have all of these variables you can encrypt and decrypt messages.<br>

Encrypt: c = m^e mod n<br>
Decrypt: m = c^d mod n<br>

I wrote a python script to find d and decrypt the message.

{% highlight python %}
#!/usr/bin/python

import gmpy
import binascii
from Crypto.PublicKey import RSA

p=0xa6055ec186de51800ddd6fcbf0192384ff42d707a55f57af4fcfb0d1dc7bd97055e8275cd4b78ec63c5d592f567c66393a061324aa2e6a8d8fc2a910cbee1ed9

q=0xfa0f9463ea0a93b929c099320d31c277e0b0dbc65b189ed76124f5a1218f5d91fd0102a4c8de11f28be5e4d0ae91ab319f4537e97ed74bc663e972a4a9119307

e=0x6d1fdab4ce3217b3fc32c9ed480a31d067fd57d93a9ab52b472dc393ab7852fbcb11abbebfd6aaae8032db1316dc22d3f7c3d631e24df13ef23d3b381a1c3e04abcc745d402ee3a031ac2718fae63b240837b4f657f29ca4702da9af22a3a019d68904a969ddb01bcf941df70af042f4fae5cbeb9c2151b324f387e525094c41

c=0x7fe1a4f743675d1987d25d38111fae0f78bbea6852cba5beda47db76d119a3efe24cb04b9449f53becd43b0b46e269826a983f832abb53b7a7e24a43ad15378344ed5c20f51e268186d24c76050c1e73647523bd5f91d9b6ad3e86bbf9126588b1dee21e6997372e36c3e74284734748891829665086e0dc523ed23c386bb520

n= p*q

d = long(gmpy.invert(e,(p-1)*(q-1)))

print (e*d) % ((p-1)*(q-1))

key = RSA.construct((p*q,e,d))


hexi = "{0:x}".format(key.decrypt(c))

print binascii.unhexlify(hexi)

{% endhighlight %}

{% highlight bash %}
ALEXCTF{RS4_I5_E55ENTIAL_T0_D0_BY_H4ND}
{% endhighlight %}
<br>
** CR4: Poor RSA was a similar challenge except you had to first factor n to find p and q. I was using a program called msieve to try to find a factor but was not succesful. <br><br>
After reading writeups, I found a website which can be used to find known factors.

[http://www.factordb.com/index.php?query=](http://www.factordb.com/index.php?query=)<br>


This reference was found at [http://fadec0d3.blogspot.com/2017/02/alexctf-2017-crypto.html](http://fadec0d3.blogspot.com/2017/02/alexctf-2017-crypto.html)<br>
I have found several very good writeups from this writer, so I recommend book marking this blog.
