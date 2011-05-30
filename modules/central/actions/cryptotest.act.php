<?

//--------------------------------------------------------------------------------------------------
//*	test of encryption and signing using OpenSSL library
//--------------------------------------------------------------------------------------------------
//+	overview: The intent of this system is to encrypt a message using a symmetric cypher (AES) with 
//+	a random key, and then to encrpt this symmetric key with the public key (RSA) of a peer server.
//+	the AES cyphertext . RSA cyphertext are then signed with DSA using the public key of this 
//+	server.

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	some variables
	//----------------------------------------------------------------------------------------------

	$plaintext = "A visitor to Afghanistan who ventures outside the American security bubble sees 
pretty quickly that President Obama’s decision to triple the number of troops in Afghanistan has 
resulted, with some exceptions, mostly in more dead Americans and Afghans alike.

So what can we do instead? Some useful guidance comes from the man whom Afghans refer to as 
“Dr. Greg” — Greg Mortenson, an American who runs around in Afghan clothing building schools, as 
chronicled in the best-selling book “Three Cups of Tea.”

The conventional wisdom is that education and development are impossible in insecure parts of 
Afghanistan that the Taliban control. That view is wrong.

An organization set up by Mr. Mortenson and a number of others are showing that it is quite 
possible to run schools in Taliban-controlled areas. I visited some of Mr. Mortenson’s schools, 
literacy centers and vocational training centers, and they survive the Taliban not because of 
military protection (which they eschew) but because local people feel “ownership” rather than 
“occupation.”

“Aid can be done anywhere, including where Taliban are,” Mr. Mortenson said. “But it’s imperative 
the elders are consulted, and that the development staff is all local, with no foreigners.”

In volatile Kunar Province, which borders Pakistan, the Taliban recently ordered a halt to a 
school being built by Mr. Mortenson’s organization, the Central Asia Institute. But the villagers 
rushed to the school’s defense. The Taliban, which have been mounting a campaign for hearts and 
minds, dropped the issue, according to Wakil Karimi, who leads Mr. Mortenson’s team in Afghanistan.

In another part of Kunar Province, the Central Asia Institute is running a girls’ primary school 
and middle school in the heart of a Taliban-controlled area. Some of the girls are 17 or 18, which 
is particularly problematic for fundamentalists (who don’t always mind girls getting an education 
as long as they drop out by puberty). Yet this school is expanding, and now has 320 girls, Mr. 
Karimi said.";

	$AESKEY = 

	//----------------------------------------------------------------------------------------------
	//	choose random symmetric key
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	encrypt message with AES
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	encrypt symmetric key with RSA, using peer's public key
	//----------------------------------------------------------------------------------------------

	

?>
