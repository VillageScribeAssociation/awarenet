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
	$prikey = '';
	$pubkey = '';

	if (true == function_exists('openssl_pkey_get_details')) {
		$config = array();
		$config['private_key_bits'] = 4096;					//	setting?
		$config['private_key_type'] = OPENSSL_KEYTYPE_RSA;

		$res = openssl_pkey_new($config);					//	make new key pair 
		openssl_pkey_export($res, $prikey);					//	get the private key as PEM

		$details = openssl_pkey_get_details($res);			//	get metadata
		$pubkey = $details['key'];							//	get public key
	} else {
		$session->msg('Could not generate key pair, openSSL support missing or incomplete.', 'bad');
	}

	//----------------------------------------------------------------------------------------------
	// render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/p2p/actions/genkeypair.page.php');
	$page->blockArgs['publicKeyTxt'] = $pubkey;
	$page->blockArgs['privateKeyTxt'] = $prikey;
	$page->render();


?>
