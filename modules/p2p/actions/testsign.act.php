<?

//--------------------------------------------------------------------------------------------------
//*	temporary action to test RSA signing with OpenSSL
//--------------------------------------------------------------------------------------------------
//credit: http://mark.koli.ch/2009/03/howto-using-sha1withrsa-signing-in-php.html

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	try it out
	//----------------------------------------------------------------------------------------------
	$pubkey = $registry->get('p2p.server.pubkey');
	$prvkey = $registry->get('p2p.server.prvkey');

	$signature = null;
	$data = "http://example.com/resources/bogus";

	// Read the private key from the file.
	$prvkeyid = openssl_get_privatekey($prvkey);

	// Compute the signature using OPENSSL_ALGO_SHA1
	// by default.
	openssl_sign($data, $signature, $prvkeyid);

	// Free the key.
	openssl_free_key($prvkeyid);

	// At this point, you've got $signature which
	// contains the digital signature as a series of bytes.
	// If you need to include the signature on a URL
	// for a request to be sent to a REST API, use
	// PHP's bin2hex() function.

	$hex = bin2hex($signature);

	$signature = base64_encode($signature);
	$signature = base64_decode($signature);

	echo "signature: " . $hex . "<br/>\n";

	// check that the signature matches the data for this public key

	$pubkeyid = openssl_get_publickey($pubkey);

	//$data = 'another';

	$check = openssl_verify($data, $signature, $pubkeyid);

	echo "check: $check <br/>\n";

?>
