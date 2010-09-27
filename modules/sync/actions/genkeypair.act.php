<?

//--------------------------------------------------------------------------------------------------
//*	test openSSL key generation
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authentication (only admins can do this)
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	// generate a bit rsa private key (usually 1024 bit)
	//----------------------------------------------------------------------------------------------
	$config = array();
	$config['private_key_bits'] = (int)$rsaKeySize;		// stored in setup.inc.php as a string
	$config['private_key_type'] = OPENSSL_KEYTYPE_RSA;

	$res = openssl_pkey_new($config);					//	make new key pair 
	openssl_pkey_export($res, $prikey);					//	get the private key
	$details = openssl_pkey_get_details($res);			//	get metadata
	$pubkey = $details['key'];							//	get public key

	//----------------------------------------------------------------------------------------------
	// wrap for php
	//----------------------------------------------------------------------------------------------
	$publicKeyWrap = '';
	$lines = explode("\n", $pubkey);
	foreach($lines as $line) { $publicKeyWrap .= "\t\t. \"" . $line . "\\n\"\n"; }

	$privateKeyWrap = '';
	$lines = explode("\n", $prikey);
	foreach($lines as $line) { $privateKeyWrap .= "\t\t. \"" . $line . "\\n\"\n"; }

	//----------------------------------------------------------------------------------------------
	// render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/sync/actions/genkeypair.page.php');
	$page->blockArgs['publicKeyTxt'] = "<textarea rows='6' cols='80'>" . $pubkey . "</textarea>";
	$page->blockArgs['privateKeyTxt'] = "<textarea rows='15' cols='80'>" . $prikey . "</textarea>";
	$page->blockArgs['publicKeyWrap'] = $publicKeyWrap;
	$page->blockArgs['privateKeyWrap'] = $privateKeyWrap;
	$page->render();


?>
